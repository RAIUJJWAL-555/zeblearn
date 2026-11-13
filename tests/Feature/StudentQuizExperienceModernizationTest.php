<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;

function setupStudentQuizTestEnvironment(array $quizOverrides = []): array
{
    $course = Course::create([
        'name' => 'SAP S/4HANA Modern Finance',
        'slug' => 'sap-s4hana-modern-finance-'.uniqid(),
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive financial architecture and ledger test.',
        'fee' => 40000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Financial Accounting Architecture',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Universal Journal & General Ledger',
        'content_type' => 'text',
        'content' => 'Overview of Universal Journal table ACDOCA.',
        'duration_minutes' => 20,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $quiz = Quiz::create(array_merge([
        'course_id' => $course->id,
        'course_module_id' => $module->id,
        'lesson_id' => $lesson->id,
        'title' => 'ACDOCA Universal Journal Knowledge Check',
        'slug' => 'acdoca-universal-journal-'.uniqid(),
        'description' => 'Assess your understanding of Universal Journal lines.',
        'instructions' => 'Read all options carefully before submitting.',
        'time_limit_minutes' => 10,
        'passing_percentage' => 70,
        'allowed_attempts' => 2,
        'shuffle_questions' => false,
        'shuffle_options' => false,
        'show_correct_answers' => true,
        'status' => Quiz::STATUS_PUBLISHED,
    ], $quizOverrides));

    // Question 1
    $q1 = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Which database table stores the Universal Journal in S/4HANA?',
        'marks' => 5,
        'sort_order' => 1,
        'explanation' => 'Table ACDOCA stores the Universal Journal line items.',
    ]);

    $q1Opt1 = QuizOption::create([
        'quiz_question_id' => $q1->id,
        'option_text' => 'ACDOCA',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $q1Opt2 = QuizOption::create([
        'quiz_question_id' => $q1->id,
        'option_text' => 'BSEG',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    // Question 2
    $q2 = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Is document splitting configured at the client level or company code level?',
        'marks' => 5,
        'sort_order' => 2,
        'explanation' => 'Document splitting is activated at the client level.',
    ]);

    $q2Opt1 = QuizOption::create([
        'quiz_question_id' => $q2->id,
        'option_text' => 'Client Level',
        'is_correct' => true,
        'sort_order' => 1,
    ]);

    $q2Opt2 = QuizOption::create([
        'quiz_question_id' => $q2->id,
        'option_text' => 'Company Code Level',
        'is_correct' => false,
        'sort_order' => 2,
    ]);

    $quiz->recalculateTotalMarks();

    $student = User::factory()->student()->active()->create([
        'name' => 'Kavya Raman',
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    return compact('course', 'module', 'lesson', 'quiz', 'q1', 'q1Opt1', 'q1Opt2', 'q2', 'q2Opt1', 'q2Opt2', 'student');
}

test('1. student quiz overview page renders specifications, rules, and start CTA', function () {
    $env = setupStudentQuizTestEnvironment();

    $response = $this->actingAs($env['student'])->get(route('student.quizzes.show', [$env['course'], $env['quiz']]));

    $response->assertOk();
    $response->assertSee('ACDOCA Universal Journal Knowledge Check');
    $response->assertSee('2 MCQs');
    $response->assertSee('10 Marks');
    $response->assertSee('70%');
    $response->assertSee('10 Minutes');
    $response->assertSee('Start Assessment');
    $response->assertSee('Timed Assessment');
    $response->assertSee('Instant Autosaving');
});

test('2. student without approved enrollment cannot access quiz overview', function () {
    $env = setupStudentQuizTestEnvironment();
    $unapproved = User::factory()->student()->active()->create();

    $this->actingAs($unapproved)
        ->get(route('student.quizzes.show', [$env['course'], $env['quiz']]))
        ->assertForbidden();
});

test('3. quiz attempt screen renders questions, options, countdown timer, and navigator', function () {
    $env = setupStudentQuizTestEnvironment();

    // Start attempt
    $this->actingAs($env['student'])->post(route('student.quizzes.start', [$env['course'], $env['quiz']]))
        ->assertRedirect();

    $attempt = QuizAttempt::where('user_id', $env['student']->id)
        ->where('quiz_id', $env['quiz']->id)
        ->first();

    $response = $this->actingAs($env['student'])->get(route('student.quizzes.take', [$env['course'], $env['quiz'], $attempt]));

    $response->assertOk();
    $response->assertSee('ACDOCA Universal Journal Knowledge Check');
    $response->assertSee('Submit Test');
    $response->assertSee('Jump to:');
    $response->assertSee('Previous Question');
    $response->assertSee('Next Question');

    // Questions and options are safely JSON-encoded in Alpine data without leaking correct keys
    $content = $response->getContent();
    expect($content)->toContain('Which database table stores the Universal Journal');
    expect($content)->toContain('ACDOCA');
    expect($content)->toContain('BSEG');
    expect($content)->not->toContain('"is_correct":true');
});

test('4. answer selection autosaves accurately via JSON endpoint', function () {
    $env = setupStudentQuizTestEnvironment();

    $this->actingAs($env['student'])->post(route('student.quizzes.start', [$env['course'], $env['quiz']]));
    $attempt = QuizAttempt::where('user_id', $env['student']->id)->first();

    $saveResponse = $this->actingAs($env['student'])->postJson(route('student.quizzes.save-answer', [
        $env['course'],
        $env['quiz'],
        $attempt,
    ]), [
        'question_id' => $env['q1']->id,
        'option_id' => $env['q1Opt1']->id,
    ]);

    $saveResponse->assertOk();
    $saveResponse->assertJson(['success' => true]);

    $this->assertDatabaseHas('quiz_attempt_answers', [
        'quiz_attempt_id' => $attempt->id,
        'quiz_question_id' => $env['q1']->id,
        'quiz_option_id' => $env['q1Opt1']->id,
    ]);
});

test('5. expired attempt is automatically finalized and cannot be retaken in progress', function () {
    $env = setupStudentQuizTestEnvironment();

    $this->actingAs($env['student'])->post(route('student.quizzes.start', [$env['course'], $env['quiz']]));
    $attempt = QuizAttempt::where('user_id', $env['student']->id)->first();

    // Expire the attempt
    $attempt->update([
        'expires_at' => now()->subMinute(),
    ]);

    $response = $this->actingAs($env['student'])->get(route('student.quizzes.take', [$env['course'], $env['quiz'], $attempt]));

    // Controller detects expiration, finalizes, and redirects to result
    $response->assertRedirect(route('student.quizzes.result', [$env['course'], $env['quiz'], $attempt]));
    $attempt->refresh();
    expect($attempt->isExpired())->toBeTrue();
});

test('6. quiz result page displays persisted score, percentage, and scorecard', function () {
    $env = setupStudentQuizTestEnvironment();

    $this->actingAs($env['student'])->post(route('student.quizzes.start', [$env['course'], $env['quiz']]));
    $attempt = QuizAttempt::where('user_id', $env['student']->id)->first();

    // Save correct answers for both questions
    $this->actingAs($env['student'])->postJson(route('student.quizzes.save-answer', [$env['course'], $env['quiz'], $attempt]), [
        'question_id' => $env['q1']->id,
        'option_id' => $env['q1Opt1']->id,
    ]);
    $this->actingAs($env['student'])->postJson(route('student.quizzes.save-answer', [$env['course'], $env['quiz'], $attempt]), [
        'question_id' => $env['q2']->id,
        'option_id' => $env['q2Opt1']->id,
    ]);

    // Submit attempt
    $this->actingAs($env['student'])->post(route('student.quizzes.submit', [$env['course'], $env['quiz'], $attempt]))
        ->assertRedirect(route('student.quizzes.result', [$env['course'], $env['quiz'], $attempt]));

    $response = $this->actingAs($env['student'])->get(route('student.quizzes.result', [$env['course'], $env['quiz'], $attempt]));

    $response->assertOk();
    $response->assertSee('ASSESSMENT PASSED');
    $response->assertSee('10');
    $response->assertSee('/ 10');
    $response->assertSee('100% Overall');
    $response->assertSee('Correct Answer');
});

test('7. correct answers remain strictly hidden when show_correct_answers is disabled', function () {
    $env = setupStudentQuizTestEnvironment(['show_correct_answers' => false]);

    $this->actingAs($env['student'])->post(route('student.quizzes.start', [$env['course'], $env['quiz']]));
    $attempt = QuizAttempt::where('user_id', $env['student']->id)->first();

    $this->actingAs($env['student'])->post(route('student.quizzes.submit', [$env['course'], $env['quiz'], $attempt]));

    $response = $this->actingAs($env['student'])->get(route('student.quizzes.result', [$env['course'], $env['quiz'], $attempt]));

    $response->assertOk();
    $response->assertSee('Detailed answers hidden per assessment policy');
    $response->assertDontSee('Table ACDOCA stores the Universal Journal line items.');
});

test('8. quiz history page shows student attempts and empty state when none exist', function () {
    $env = setupStudentQuizTestEnvironment();

    // Initially no attempts
    $responseEmpty = $this->actingAs($env['student'])->get(route('student.quizzes.history', [$env['course'], $env['quiz']]));
    $responseEmpty->assertOk();
    $responseEmpty->assertSee('No Submitted Attempts');

    // Create a submitted attempt
    QuizAttempt::create([
        'quiz_id' => $env['quiz']->id,
        'user_id' => $env['student']->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(15),
        'submitted_at' => now()->subMinutes(5),
        'score' => 10,
        'total_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $responseWithData = $this->actingAs($env['student'])->get(route('student.quizzes.history', [$env['course'], $env['quiz']]));
    $responseWithData->assertOk();
    $responseWithData->assertSee('Attempt #1');
    $responseWithData->assertSee('Passed');
    $responseWithData->assertSee('10/10');
    $responseWithData->assertSee('View Result');
});
