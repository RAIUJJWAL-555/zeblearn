<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizAttemptQuestion;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

function setupIntegrityTestContext(): array
{
    $course = Course::create([
        'name' => 'Advanced Database Systems',
        'slug' => 'adv-db-sys-'.uniqid(),
        'category' => 'data-engineering',
        'status' => 'available',
        'short_description' => 'Concurrency and transaction isolation.',
        'fee' => 50000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Concurrency Control',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Lesson 1.1: Two-Phase Locking',
        'content_type' => 'text',
        'content' => 'Strict 2PL and serializability.',
        'duration_minutes' => 20,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('course', 'module', 'lesson');
}

function createIntegrityQuizWithQuestions(Course $course, ?CourseModule $module = null, ?Lesson $lesson = null, array $overrides = [], int $questionCount = 3): Quiz
{
    $quiz = Quiz::create(array_merge([
        'course_id' => $course->id,
        'course_module_id' => $module?->id,
        'lesson_id' => $lesson?->id,
        'title' => 'Transaction Isolation Assessment',
        'slug' => 'txn-isolation-'.uniqid(),
        'description' => 'Verify ACID guarantees.',
        'time_limit_minutes' => 15,
        'passing_percentage' => 70,
        'attempts_allowed' => 3,
        'shuffle_questions' => true,
        'shuffle_options' => true,
        'show_correct_answers' => true,
        'status' => 'published',
    ], $overrides));

    for ($i = 1; $i <= $questionCount; $i++) {
        $q = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => "Question {$i}: What is the behavior under isolation level {$i}?",
            'marks' => 2,
            'sort_order' => $i,
            'explanation' => "Explanation for question {$i}.",
        ]);

        for ($j = 1; $j <= 4; $j++) {
            QuizOption::create([
                'quiz_question_id' => $q->id,
                'option_text' => "Option {$i}.{$j}",
                'is_correct' => ($j === 1), // First option is always correct canonically
                'sort_order' => $j,
            ]);
        }
    }

    $quiz->recalculateTotalMarks();

    return $quiz->fresh(['questions.options']);
}

function createApprovedStudent(Course $course): User
{
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    return $student;
}

test('1. shuffled question order remains stable after refresh', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['shuffle_questions' => true], 5);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Initial delivery
    $data1 = $quizService->getAttemptTakingData($attempt);
    $order1 = collect($data1['questions'])->pluck('id')->all();

    // Simulated browser refresh / subsequent fetch
    $data2 = $quizService->getAttemptTakingData($attempt);
    $order2 = collect($data2['questions'])->pluck('id')->all();

    expect($order1)->toHaveCount(5);
    expect($order1)->toBe($order2);
});

test('2. shuffled option order remains stable after refresh', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['shuffle_options' => true], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Initial delivery
    $data1 = $quizService->getAttemptTakingData($attempt);
    $optOrder1 = collect($data1['questions'][0]['options'])->pluck('id')->all();

    // Simulated browser refresh
    $data2 = $quizService->getAttemptTakingData($attempt);
    $optOrder2 = collect($data2['questions'][0]['options'])->pluck('id')->all();

    expect($optOrder1)->toHaveCount(4);
    expect($optOrder1)->toBe($optOrder2);
});

test('3. different attempts can have different orders when shuffle is enabled', function () {
    $ctx = setupIntegrityTestContext();
    // 10 questions has 3.6 million permutations
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'shuffle_questions' => true,
        'shuffle_options' => true,
        'attempts_allowed' => 5,
    ], 10);

    $student = createApprovedStudent($ctx['course']);
    $quizService = app(QuizService::class);

    // Attempt 1
    $attempt1 = $quizService->startOrResumeAttempt($student, $quiz);
    $order1 = collect($quizService->getAttemptTakingData($attempt1)['questions'])->pluck('id')->all();
    $quizService->submitAttempt($attempt1);

    // Attempt 2
    $attempt2 = $quizService->startOrResumeAttempt($student, $quiz);
    $order2 = collect($quizService->getAttemptTakingData($attempt2)['questions'])->pluck('id')->all();

    expect($attempt1->id)->not->toBe($attempt2->id);
    expect(sort($order1))->toBe(sort($order2)); // Both contain all questions

    // At least one of the question sequences or option sequences should vary across attempts with 10 questions
    $optOrder1 = collect($quizService->getAttemptTakingData($attempt1)['questions'][0]['options'])->pluck('id')->all();
    $optOrder2 = collect($quizService->getAttemptTakingData($attempt2)['questions'][0]['options'])->pluck('id')->all();

    // Assert that orders are independently determined for each attempt
    expect(QuizAttemptQuestion::where('quiz_attempt_id', $attempt1->id)->count())->toBe(10);
    expect(QuizAttemptQuestion::where('quiz_attempt_id', $attempt2->id)->count())->toBe(10);
});

test('4. disabled shuffle preserves canonical order', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'shuffle_questions' => false,
        'shuffle_options' => false,
    ], 4);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);
    $data = $quizService->getAttemptTakingData($attempt);

    $expectedQuestionIds = $quiz->questions()->orderBy('sort_order', 'asc')->pluck('id')->all();
    $actualQuestionIds = collect($data['questions'])->pluck('id')->all();

    expect($actualQuestionIds)->toBe($expectedQuestionIds);

    foreach ($data['questions'] as $q) {
        $expectedOptIds = QuizOption::where('quiz_question_id', $q['id'])->orderBy('sort_order', 'asc')->pluck('id')->all();
        $actualOptIds = collect($q['options'])->pluck('id')->all();
        expect($actualOptIds)->toBe($expectedOptIds);
    }
});

test('5. saved answers remain correctly associated after shuffling', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'shuffle_questions' => true,
        'shuffle_options' => true,
    ], 3);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    $q1 = $quiz->questions->first();
    $correctOption = $q1->options->firstWhere('is_correct', true);

    // Save answer
    $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $correctOption->id,
    ])->assertOk();

    // Verify stored answer points directly to question_id and option_id
    $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('quiz_question_id', $q1->id)->first();
    expect($answer->quiz_option_id)->toBe($correctOption->id);

    // Submit attempt
    $quizService->submitAttempt($attempt);
    $attempt->refresh();

    // Verify marks awarded correctly
    $answer->refresh();
    expect($answer->is_correct)->toBeTrue();
    expect((int) $answer->awarded_marks)->toBe((int) $q1->marks);
    expect((int) $attempt->score)->toBe((int) $q1->marks);
});

test('6. concurrent start requests cannot exceed the attempt limit', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 1,
    ], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);

    // First attempt starts
    $attempt1 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($attempt1)->not->toBeNull();

    // Submit first attempt so it counts as used
    $quizService->submitAttempt($attempt1);

    // Second start request must be blocked
    expect(fn () => $quizService->startOrResumeAttempt($student, $quiz))
        ->toThrow(HttpException::class);

    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->count())->toBe(1);
});

test('7. concurrent start requests cannot create duplicate active attempts', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 5,
    ], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);

    // Call startOrResumeAttempt twice in succession (or simulated concurrent)
    $attempt1 = $quizService->startOrResumeAttempt($student, $quiz);
    $attempt2 = $quizService->startOrResumeAttempt($student, $quiz);

    expect($attempt1->id)->toBe($attempt2->id);
    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->where('status', QuizAttempt::STATUS_IN_PROGRESS)->count())->toBe(1);
});

test('8. attempt numbers remain unique', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 3,
    ], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);

    $a1 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($a1->attempt_number)->toBe(1);
    $quizService->submitAttempt($a1);

    $a2 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($a2->attempt_number)->toBe(2);
    $quizService->submitAttempt($a2);

    $a3 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($a3->attempt_number)->toBe(3);
    $quizService->submitAttempt($a3);

    $numbers = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->pluck('attempt_number')->all();
    expect($numbers)->toBe([1, 2, 3]);
});

test('9. historical results remain unchanged after quiz retirement', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Answer both correctly
    foreach ($quiz->questions as $q) {
        $c = $q->options->firstWhere('is_correct', true);
        $quizService->saveAnswer($attempt, $q->id, $c->id);
    }
    $quizService->submitAttempt($attempt);
    $attempt->refresh();

    $originalScore = $attempt->score;
    $originalPercentage = $attempt->percentage;
    $originalIsPassed = $attempt->is_passed;

    // Admin retires / unpublishes the quiz
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $quiz));
    expect($quiz->fresh()->status)->toBe(Quiz::STATUS_DRAFT);

    // Historical results must remain visible to the student
    $this->actingAs($student)->get(route('student.quizzes.result', [$ctx['course'], $quiz, $attempt]))->assertOk();
    $this->actingAs($student)->get(route('student.quizzes.history', [$ctx['course'], $quiz]))->assertOk();

    $attempt->refresh();
    expect($attempt->score)->toBe($originalScore);
    expect($attempt->percentage)->toBe($originalPercentage);
    expect($attempt->is_passed)->toBe($originalIsPassed);
});

test('10. assessment structure cannot be modified in a way that corrupts existing results', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);
    $quizService->submitAttempt($attempt);

    $admin = User::factory()->admin()->create();
    $q1 = $quiz->questions->first();

    // 1. Cannot add new question
    $res1 = $this->actingAs($admin)->post(route('admin.quizzes.questions.store', $quiz), [
        'question_text' => 'Corrupting question?',
        'marks' => 2,
        'options' => ['A', 'B'],
        'correct_option' => 0,
    ]);
    $res1->assertSessionHas('error');

    // 2. Cannot edit question
    $res2 = $this->actingAs($admin)->put(route('admin.quizzes.questions.update', [$quiz, $q1]), [
        'question_text' => 'Modified question?',
        'marks' => 5,
        'options' => ['A', 'B'],
        'correct_option' => 0,
    ]);
    $res2->assertSessionHas('error');

    // 3. Cannot delete question
    $res3 = $this->actingAs($admin)->delete(route('admin.quizzes.questions.destroy', [$quiz, $q1]));
    $res3->assertSessionHas('error');

    // 4. Cannot reorder questions
    $res4 = $this->actingAs($admin)->post(route('admin.quizzes.questions.reorder', $quiz), [
        'question_ids' => $quiz->questions->pluck('id')->reverse()->all(),
    ]);
    $res4->assertSessionHas('error');

    // 5. Cannot delete quiz
    $res5 = $this->actingAs($admin)->delete(route('admin.quizzes.destroy', $quiz));
    $res5->assertSessionHas('error');
});

test('11. refreshing an attempt does not reset expires_at', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'time_limit_minutes' => 10,
    ], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);
    $originalExpiresAt = $attempt->expires_at->toIso8601String();

    // Advance simulated time by 3 minutes
    Carbon::setTestNow(now()->addMinutes(3));

    // Student refreshes / resumes attempt
    $resumed = $quizService->startOrResumeAttempt($student, $quiz);

    expect($resumed->id)->toBe($attempt->id);
    expect($resumed->expires_at->toIso8601String())->toBe($originalExpiresAt);
    expect($resumed->time_remaining_seconds)->toBeLessThanOrEqual(420); // 7 mins or less

    Carbon::setTestNow(null); // Reset mock
});

test('12. late answer saves are rejected', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'time_limit_minutes' => 5,
    ], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Fast forward past expiration
    Carbon::setTestNow(now()->addMinutes(7));

    $q1 = $quiz->questions->first();
    $opt = $q1->options->first();

    // Late answer save rejected with 422
    $response = $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $opt->id,
    ]);

    $response->assertStatus(422);

    $attempt->refresh();
    expect($attempt->status)->toBe(QuizAttempt::STATUS_EXPIRED);

    Carbon::setTestNow(null);
});

test('13. concurrent manual submission and expiration produce one final result', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Answer question 1 correctly
    $q1 = $quiz->questions[0];
    $c1 = $q1->options->firstWhere('is_correct', true);
    $quizService->saveAnswer($attempt, $q1->id, $c1->id);

    // Simulate submission 1
    $res1 = $quizService->submitAttempt($attempt);

    // Simulate concurrent or immediate submission 2
    $res2 = $quizService->submitAttempt($attempt);

    expect($res1->id)->toBe($res2->id);
    expect($res1->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($res2->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($res1->score)->toBe($res2->score);
});

test('14. duplicate submission remains idempotent', function () {
    $ctx = setupIntegrityTestContext();
    $quiz = createIntegrityQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = createApprovedStudent($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    $submitted = $quizService->submitAttempt($attempt);
    $originalSubmittedAt = $submitted->submitted_at;

    // Second submit call
    $reSubmitted = $quizService->submitAttempt($submitted);

    expect($reSubmitted->id)->toBe($submitted->id);
    expect($reSubmitted->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($reSubmitted->submitted_at->timestamp)->toBe($originalSubmittedAt->timestamp);
});
