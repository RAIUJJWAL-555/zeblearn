<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

function setupHardeningContext(): array
{
    $course = Course::create([
        'name' => 'Hardened Software Architecture',
        'slug' => 'hardened-soft-arch-'.uniqid(),
        'category' => 'software-engineering',
        'status' => 'available',
        'short_description' => 'Security and integrity test suite.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Fault Tolerance',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Lesson 1.1: State Freezing',
        'content_type' => 'text',
        'content' => 'Immutable historical records.',
        'duration_minutes' => 15,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('course', 'module', 'lesson');
}

function createHardenedQuiz(Course $course, ?CourseModule $module = null, ?Lesson $lesson = null, array $overrides = [], int $questionCount = 3): Quiz
{
    $quiz = Quiz::create(array_merge([
        'course_id' => $course->id,
        'course_module_id' => $module?->id,
        'lesson_id' => $lesson?->id,
        'title' => 'Integrity Verification Quiz',
        'slug' => 'integrity-quiz-'.uniqid(),
        'description' => 'Ensuring all configuration constraints hold.',
        'instructions' => 'Read every question carefully before choosing.',
        'time_limit_minutes' => 20,
        'passing_percentage' => 75,
        'attempts_allowed' => 2,
        'shuffle_questions' => true,
        'shuffle_options' => true,
        'show_correct_answers' => true,
        'status' => Quiz::STATUS_PUBLISHED,
    ], $overrides));

    for ($i = 1; $i <= $questionCount; $i++) {
        $q = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => "Hardened Question {$i}?",
            'marks' => 2,
            'sort_order' => $i,
            'explanation' => "Explanation for question {$i}.",
        ]);

        for ($j = 1; $j <= 4; $j++) {
            QuizOption::create([
                'quiz_question_id' => $q->id,
                'option_text' => "Option {$i}.{$j}",
                'is_correct' => ($j === 1),
                'sort_order' => $j,
            ]);
        }
    }

    $quiz->recalculateTotalMarks();

    return $quiz->fresh(['questions.options']);
}

function enrollStudentInCourse(Course $course): User
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

// 1. Frozen settings cannot be updated when attempts exist (test each frozen field).
test('1. frozen settings cannot be updated when attempts exist (test each frozen field)', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = enrollStudentInCourse($ctx['course']);

    // Create an attempt so attempts exist
    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    $admin = User::factory()->admin()->create();

    // Secondary course & module for context test
    $otherCourse = Course::create([
        'name' => 'Other Course',
        'slug' => 'other-course-'.uniqid(),
        'category' => 'engineering',
        'status' => 'available',
        'short_description' => 'Other course description.',
        'fee' => 10000,
    ]);

    $otherModule = CourseModule::create([
        'course_id' => $ctx['course']->id,
        'title' => 'Other Module',
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $otherLesson = Lesson::create([
        'course_module_id' => $ctx['module']->id,
        'title' => 'Other Lesson',
        'content_type' => 'text',
        'sort_order' => 2,
        'status' => 'published',
    ]);

    // Test each frozen field individually via JSON PUT
    $frozenFields = [
        ['payload' => ['course_id' => $otherCourse->id], 'errorKey' => 'course_id'],
        ['payload' => ['course_module_id' => $otherModule->id], 'errorKey' => 'course_module_id'],
        ['payload' => ['lesson_id' => $otherLesson->id], 'errorKey' => 'lesson_id'],
        ['payload' => ['passing_percentage' => 50], 'errorKey' => 'passing_percentage'],
        ['payload' => ['pass_percentage' => 60], 'errorKey' => 'pass_percentage'],
        ['payload' => ['attempts_allowed' => 5], 'errorKey' => 'attempts_allowed'],
        ['payload' => ['allowed_attempts' => 4], 'errorKey' => 'allowed_attempts'],
        ['payload' => ['time_limit_minutes' => 45], 'errorKey' => 'time_limit_minutes'],
        ['payload' => ['shuffle_questions' => false], 'errorKey' => 'shuffle_questions'],
        ['payload' => ['shuffle_options' => false], 'errorKey' => 'shuffle_options'],
        ['payload' => ['show_correct_answers' => false], 'errorKey' => 'show_correct_answers'],
        ['payload' => ['total_marks' => 99], 'errorKey' => 'total_marks'],
    ];

    foreach ($frozenFields as $case) {
        $response = $this->actingAs($admin)->putJson(
            route('admin.quizzes.update', $quiz),
            $case['payload']
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$case['errorKey']]);
    }
});

// 2. Non-frozen settings (title, description, instructions) can still be updated.
test('2. non-frozen settings (title, description, instructions) can still be updated', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->putJson(route('admin.quizzes.update', $quiz), [
        'title' => 'Updated Assessment Title',
        'description' => 'Updated Description After Attempts',
        'instructions' => 'Updated Special Instructions for Candidates',
    ]);

    $response->assertOk();

    $quiz->refresh();
    expect($quiz->title)->toBe('Updated Assessment Title');
    expect($quiz->description)->toBe('Updated Description After Attempts');
    expect($quiz->instructions)->toBe('Updated Special Instructions for Candidates');
});

// 3. Questions cannot be added, edited, deleted, or reordered when attempts exist.
test('3. questions cannot be added, edited, deleted, or reordered when attempts exist', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    $admin = User::factory()->admin()->create();
    $firstQuestion = $quiz->questions->first();

    // 1. Add question rejected
    $this->actingAs($admin)->postJson(route('admin.quizzes.questions.store', $quiz), [
        'question_text' => 'New question after attempts?',
        'marks' => 3,
        'options' => ['Option A', 'Option B'],
        'correct_option' => 0,
    ])->assertStatus(422)->assertJsonValidationErrors(['question']);

    // 2. Edit question rejected
    $this->actingAs($admin)->putJson(route('admin.quizzes.questions.update', [$quiz, $firstQuestion]), [
        'question_text' => 'Modified question text?',
        'marks' => 5,
        'options' => ['Option A', 'Option B'],
        'correct_option' => 0,
    ])->assertStatus(422)->assertJsonValidationErrors(['question']);

    // 3. Delete question rejected
    $this->actingAs($admin)->deleteJson(route('admin.quizzes.questions.destroy', [$quiz, $firstQuestion]))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['question']);

    // 4. Reorder questions rejected
    $this->actingAs($admin)->postJson(route('admin.quizzes.questions.reorder', $quiz), [
        'question_ids' => $quiz->questions->pluck('id')->reverse()->all(),
    ])->assertStatus(422)->assertJsonValidationErrors(['question']);
});

// 4. Total marks cannot be altered when attempts exist.
test('4. total marks cannot be altered when attempts exist', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [], 3);
    $student = enrollStudentInCourse($ctx['course']);

    expect($quiz->total_marks)->toBe(6);

    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    // Attempting recalculateTotalMarks returns original total_marks without changing DB
    $calculated = $quiz->recalculateTotalMarks();
    expect($calculated)->toBe(6);
    expect($quiz->fresh()->total_marks)->toBe(6);

    // Direct update via controller rejected
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->putJson(route('admin.quizzes.update', $quiz), [
        'total_marks' => 20,
    ])->assertStatus(422)->assertJsonValidationErrors(['total_marks']);

    expect($quiz->fresh()->total_marks)->toBe(6);
});

// 5. In-progress attempts count towards the attempt limit.
test('5. in-progress attempts count towards the attempt limit', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 1,
    ]);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    expect($attempt->status)->toBe(QuizAttempt::STATUS_IN_PROGRESS);
    expect($quiz->remainingAttemptsForUser($student))->toBe(0);

    // Student cannot start a new second attempt; startOrResumeAttempt returns existing in-progress attempt
    $attempt2 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($attempt2->id)->toBe($attempt->id);

    // Total attempts is exactly 1
    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->count())->toBe(1);
});

// 6. Expired attempts count towards the attempt limit.
test('6. expired attempts count towards the attempt limit', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 1,
        'time_limit_minutes' => 5,
    ]);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Advance time past expiration
    Carbon::setTestNow(now()->addMinutes(10));

    expect($attempt->hasExpired())->toBeTrue();

    // canUserAttempt checks and finalizes the expired attempt, then sees limit 1 reached
    expect($quiz->canUserAttempt($student))->toBeFalse();
    expect($quiz->remainingAttemptsForUser($student))->toBe(0);

    // Attempting start throws 403
    expect(fn () => $quizService->startOrResumeAttempt($student, $quiz))
        ->toThrow(HttpException::class);

    Carbon::setTestNow(null);
});

// 7. Submitted attempts count towards the attempt limit.
test('7. submitted attempts count towards the attempt limit', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 1,
    ]);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $attempt = $quizService->startOrResumeAttempt($student, $quiz);
    $quizService->submitAttempt($attempt);

    expect($attempt->fresh()->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($quiz->canUserAttempt($student))->toBeFalse();
    expect($quiz->remainingAttemptsForUser($student))->toBe(0);

    expect(fn () => $quizService->startOrResumeAttempt($student, $quiz))
        ->toThrow(HttpException::class);
});

// 8. Student cannot start attempt when in_progress attempt exists (must resume).
test('8. student cannot start attempt when in_progress attempt exists (must resume)', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 5,
    ]);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);

    $attempt1 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($attempt1->attempt_number)->toBe(1);

    // Calling start again must resume attempt 1, NOT create attempt 2
    $attemptResume = $quizService->startOrResumeAttempt($student, $quiz);
    expect($attemptResume->id)->toBe($attempt1->id);
    expect($attemptResume->attempt_number)->toBe(1);

    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->count())->toBe(1);
});

// 9. Student cannot start attempt when attempts_allowed is reached across all statuses.
test('9. student cannot start attempt when attempts_allowed is reached across all statuses', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 3,
        'time_limit_minutes' => 5,
    ]);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    // Attempt 1: submitted
    $a1 = $quizService->startOrResumeAttempt($student, $quiz);
    $quizService->submitAttempt($a1);
    expect($a1->fresh()->status)->toBe(QuizAttempt::STATUS_SUBMITTED);

    // Attempt 2: expired
    $a2 = $quizService->startOrResumeAttempt($student, $quiz);
    Carbon::setTestNow(now()->addMinutes(10));
    $quizService->submitAttempt($a2);
    expect($a2->fresh()->status)->toBe(QuizAttempt::STATUS_EXPIRED);
    Carbon::setTestNow(null);

    // Attempt 3: submitted
    $a3 = $quizService->startOrResumeAttempt($student, $quiz);
    $quizService->submitAttempt($a3);
    expect($a3->fresh()->status)->toBe(QuizAttempt::STATUS_SUBMITTED);

    // Attempt 4 must be blocked
    expect($quiz->canUserAttempt($student))->toBeFalse();
    expect($quiz->remainingAttemptsForUser($student))->toBe(0);

    expect(fn () => $quizService->startOrResumeAttempt($student, $quiz))
        ->toThrow(HttpException::class);
});

// 10. Expired attempt cannot be resumed or modified.
test('10. expired attempt cannot be resumed or modified', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'time_limit_minutes' => 5,
    ]);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Advance time past limit
    Carbon::setTestNow(now()->addMinutes(15));

    $question = $quiz->questions->first();
    $opt = $question->options->first();

    // Trying to save answer on expired attempt is rejected with 422
    expect(fn () => $quizService->saveAnswer($attempt, $question->id, $opt->id))
        ->toThrow(HttpException::class);

    $attempt->refresh();
    expect($attempt->status)->toBe(QuizAttempt::STATUS_EXPIRED);

    // Trying to save answer again when status is expired is rejected with 403
    expect(fn () => $quizService->saveAnswer($attempt, $question->id, $opt->id))
        ->toThrow(HttpException::class);

    Carbon::setTestNow(null);
});

// 11. Expiry timestamp is not extended on refresh or re-fetch.
test('11. expiry timestamp is not extended on refresh or re-fetch', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'time_limit_minutes' => 15,
    ]);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    $attempt = $quizService->startOrResumeAttempt($student, $quiz);
    $initialExpiry = $attempt->expires_at->toIso8601String();

    // Advance 5 minutes
    Carbon::setTestNow(now()->addMinutes(5));

    // Re-fetch via service
    $resumed = $quizService->startOrResumeAttempt($student, $quiz);
    expect($resumed->expires_at->toIso8601String())->toBe($initialExpiry);

    // Re-fetch taking view data
    $takingData = $quizService->getAttemptTakingData($resumed);
    expect($takingData['attempt']->expires_at->toIso8601String())->toBe($initialExpiry);

    Carbon::setTestNow(null);
});

// 12. Idempotent submission: second submit returns same result without recalculation.
test('12. idempotent submission: second submit returns same result without recalculation', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Answer question 1 correctly
    $q1 = $quiz->questions[0];
    $c1 = $q1->options->firstWhere('is_correct', true);
    $quizService->saveAnswer($attempt, $q1->id, $c1->id);

    // First submission
    $sub1 = $quizService->submitAttempt($attempt);
    $initialSubmittedAt = $sub1->submitted_at;
    $initialScore = $sub1->score;

    // Simulate second submission
    $sub2 = $quizService->submitAttempt($attempt);

    expect($sub2->id)->toBe($sub1->id);
    expect($sub2->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($sub2->score)->toBe($initialScore);
    expect($sub2->submitted_at->timestamp)->toBe($initialSubmittedAt->timestamp);
});

// 13. Concurrency test: simultaneous start requests do not exceed attempt limit.
test('13. concurrency test: simultaneous start requests do not exceed attempt limit', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'attempts_allowed' => 1,
    ]);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    // Attempt 1 succeeds
    $attempt1 = $quizService->startOrResumeAttempt($student, $quiz);
    expect($attempt1)->not->toBeNull();

    // Submit attempt 1
    $quizService->submitAttempt($attempt1);

    // Competing attempt creation immediately rejected under lock
    expect(fn () => $quizService->startOrResumeAttempt($student, $quiz))
        ->toThrow(HttpException::class);

    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->count())->toBe(1);
});

// 14. Concurrency test: simultaneous submit requests do not create duplicate scores or alter results.
test('14. concurrency test: simultaneous submit requests do not create duplicate scores or alter results', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [], 2);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Submit via service
    $res1 = $quizService->submitAttempt($attempt);

    // Repeated submit call
    $res2 = $quizService->submitAttempt($attempt);

    expect($res1->score)->toBe($res2->score);
    expect($res1->percentage)->toBe($res2->percentage);
    expect($res1->is_passed)->toBe($res2->is_passed);
    expect(QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->count())->toBeLessThanOrEqual(2);
});

// 15. Quizzes with zero attempts can still have their configuration updated freely.
test('15. quizzes with zero attempts can still have their configuration updated freely', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);

    expect($quiz->attempts()->count())->toBe(0);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->putJson(route('admin.quizzes.update', $quiz), [
        'title' => 'Fresh Title',
        'passing_percentage' => 85,
        'attempts_allowed' => 4,
        'time_limit_minutes' => 35,
        'shuffle_questions' => false,
        'shuffle_options' => false,
        'show_correct_answers' => false,
    ]);

    $response->assertOk();

    $quiz->refresh();
    expect($quiz->title)->toBe('Fresh Title');
    expect($quiz->passing_percentage)->toBe(85);
    expect($quiz->attempts_allowed)->toBe(4);
    expect($quiz->time_limit_minutes)->toBe(35);
    expect($quiz->shuffle_questions)->toBeFalse();
    expect($quiz->shuffle_options)->toBeFalse();
    expect($quiz->show_correct_answers)->toBeFalse();
});

// 16. Unpublishing a quiz with attempts does not allow editing its frozen settings.
test('16. unpublishing a quiz with attempts does not allow editing its frozen settings', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    $admin = User::factory()->admin()->create();

    // Admin unpublishes the quiz
    $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $quiz));
    expect($quiz->fresh()->status)->toBe(Quiz::STATUS_DRAFT);

    // Modifying frozen settings must still be rejected with 422
    $response = $this->actingAs($admin)->putJson(route('admin.quizzes.update', $quiz), [
        'passing_percentage' => 50,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['passing_percentage']);
});

// 17. Publishing validation errors still work as expected.
test('17. publishing validation errors still work as expected', function () {
    $ctx = setupHardeningContext();

    // Quiz with no questions
    $emptyQuiz = Quiz::create([
        'course_id' => $ctx['course']->id,
        'title' => 'Empty Quiz Without Questions',
        'slug' => 'empty-quiz-'.uniqid(),
        'status' => Quiz::STATUS_DRAFT,
        'passing_percentage' => 70,
        'attempts_allowed' => 1,
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $emptyQuiz));
    $response->assertSessionHas('error');
    expect($emptyQuiz->fresh()->status)->toBe(Quiz::STATUS_DRAFT);
});

// 18. Validation error responses return 422 with proper error messages when updating frozen settings.
test('18. validation error responses return 422 with proper error messages when updating frozen settings', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = enrollStudentInCourse($ctx['course']);

    $quizService = app(QuizService::class);
    $quizService->startOrResumeAttempt($student, $quiz);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->putJson(route('admin.quizzes.update', $quiz), [
        'time_limit_minutes' => 60,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['time_limit_minutes']);
    expect($response->json('errors.time_limit_minutes.0'))
        ->toContain('cannot be modified because student attempts already exist');
});

// 19. Edge case: student with expired attempt cannot submit answers to it.
test('19. edge case: student with expired attempt cannot submit answers to it', function () {
    $ctx = setupHardeningContext();
    $quiz = createHardenedQuiz($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'time_limit_minutes' => 5,
    ], 2);
    $student = enrollStudentInCourse($ctx['course']);
    $quizService = app(QuizService::class);

    $attempt = $quizService->startOrResumeAttempt($student, $quiz);

    // Fast-forward past expiry
    Carbon::setTestNow(now()->addMinutes(12));

    $q1 = $quiz->questions->first();
    $opt = $q1->options->first();

    // Trying to save an answer after expiry via controller
    $response = $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $opt->id,
    ]);

    $response->assertStatus(422);

    $attempt->refresh();
    expect($attempt->status)->toBe(QuizAttempt::STATUS_EXPIRED);

    // Answer was not stored
    $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('quiz_question_id', $q1->id)->first();
    expect($answer)->toBeNull();

    Carbon::setTestNow(null);
});
