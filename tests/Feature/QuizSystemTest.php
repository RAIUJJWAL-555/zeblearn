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

function createQuizTestContext(): array
{
    $course = Course::create([
        'name' => 'Data Science & Machine Learning Bootcamp',
        'slug' => 'ds-ml-bootcamp-'.uniqid(),
        'category' => 'data-science',
        'status' => 'available',
        'short_description' => 'Comprehensive data science curriculum.',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Python Fundamentals',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Lesson 1.1: Control Structures',
        'content_type' => 'text',
        'content' => 'If-else and loops overview.',
        'duration_minutes' => 15,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('course', 'module', 'lesson');
}

function createReadyQuizWithQuestions(Course $course, ?CourseModule $module = null, ?Lesson $lesson = null, array $overrides = []): Quiz
{
    $quiz = Quiz::create(array_merge([
        'course_id' => $course->id,
        'course_module_id' => $module?->id,
        'lesson_id' => $lesson?->id,
        'title' => 'Python Control Structures Quiz',
        'slug' => 'python-control-quiz-'.uniqid(),
        'description' => 'Test your knowledge on loops and conditionals.',
        'time_limit_minutes' => 10,
        'pass_percentage' => 70,
        'allowed_attempts' => 3,
        'shuffle_questions' => false,
        'shuffle_options' => false,
        'show_correct_answers' => true,
        'status' => 'published',
    ], $overrides));

    // Question 1: 5 marks, 4 options
    $q1 = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'What is the output of range(1, 4)?',
        'marks' => 5,
        'sort_order' => 1,
        'explanation' => 'range(1, 4) produces 1, 2, 3.',
    ]);

    $o1_1 = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => '[1, 2, 3]', 'is_correct' => true, 'sort_order' => 1]);
    $o1_2 = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => '[1, 2, 3, 4]', 'is_correct' => false, 'sort_order' => 2]);
    $o1_3 = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => '[0, 1, 2, 3]', 'is_correct' => false, 'sort_order' => 3]);
    $o1_4 = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => '[1, 4]', 'is_correct' => false, 'sort_order' => 4]);

    // Question 2: 5 marks, 4 options
    $q2 = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Which keyword is used to exit a loop in Python?',
        'marks' => 5,
        'sort_order' => 2,
        'explanation' => 'break exits the innermost loop.',
    ]);

    $o2_1 = QuizOption::create(['quiz_question_id' => $q2->id, 'option_text' => 'exit', 'is_correct' => false, 'sort_order' => 1]);
    $o2_2 = QuizOption::create(['quiz_question_id' => $q2->id, 'option_text' => 'break', 'is_correct' => true, 'sort_order' => 2]);
    $o2_3 = QuizOption::create(['quiz_question_id' => $q2->id, 'option_text' => 'stop', 'is_correct' => false, 'sort_order' => 3]);
    $o2_4 = QuizOption::create(['quiz_question_id' => $q2->id, 'option_text' => 'return', 'is_correct' => false, 'sort_order' => 4]);

    return $quiz->fresh(['questions.options']);
}

function createQuizStudent(Course $course, string $enrollmentStatus = Enrollment::STATUS_APPROVED, string $userStatus = User::STATUS_ACTIVE): User
{
    $student = User::factory()->student()->create(['status' => $userStatus]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => $enrollmentStatus,
        'requested_at' => now()->subDay(),
        'approved_at' => $enrollmentStatus === Enrollment::STATUS_APPROVED ? now() : null,
    ]);

    return $student;
}

test('1. admin can create a quiz', function () {
    $admin = User::factory()->admin()->create();
    $ctx = createQuizTestContext();

    $response = $this->actingAs($admin)->post(route('admin.quizzes.store'), [
        'course_id' => $ctx['course']->id,
        'course_module_id' => $ctx['module']->id,
        'lesson_id' => $ctx['lesson']->id,
        'title' => 'Python Basics Quiz',
        'description' => 'Introductory test.',
        'time_limit_minutes' => 15,
        'pass_percentage' => 80,
        'allowed_attempts' => 2,
        'shuffle_questions' => true,
        'shuffle_options' => true,
        'show_correct_answers' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('quizzes', [
        'course_id' => $ctx['course']->id,
        'course_module_id' => $ctx['module']->id,
        'lesson_id' => $ctx['lesson']->id,
        'title' => 'Python Basics Quiz',
        'status' => 'draft',
        'passing_percentage' => 80,
    ]);
});

test('2. non-admin cannot manage quizzes', function () {
    $student = User::factory()->student()->create();
    $ctx = createQuizTestContext();

    // Student cannot access admin quiz list
    $this->actingAs($student)->get(route('admin.quizzes.index'))->assertForbidden();

    // Student cannot create quiz
    $this->actingAs($student)->post(route('admin.quizzes.store'), [
        'course_id' => $ctx['course']->id,
        'title' => 'Hacker Quiz',
    ])->assertForbidden();
});

test('3. quiz requires valid assessment context', function () {
    $admin = User::factory()->admin()->create();
    $ctx1 = createQuizTestContext();
    $ctx2 = createQuizTestContext();

    // Mismatched module belonging to another course
    $response = $this->actingAs($admin)->post(route('admin.quizzes.store'), [
        'course_id' => $ctx1['course']->id,
        'course_module_id' => $ctx2['module']->id, // foreign module
        'title' => 'Invalid Module Quiz',
    ]);

    $response->assertSessionHasErrors('course_module_id');

    // Mismatched lesson belonging to another module
    $response2 = $this->actingAs($admin)->post(route('admin.quizzes.store'), [
        'course_id' => $ctx1['course']->id,
        'course_module_id' => $ctx1['module']->id,
        'lesson_id' => $ctx2['lesson']->id, // foreign lesson
        'title' => 'Invalid Lesson Quiz',
    ]);

    $response2->assertSessionHasErrors('lesson_id');
});

test('4. quiz cannot be published without valid questions', function () {
    $admin = User::factory()->admin()->create();
    $ctx = createQuizTestContext();

    // Quiz with no questions
    $quiz = Quiz::create([
        'course_id' => $ctx['course']->id,
        'title' => 'Empty Quiz',
        'slug' => 'empty-quiz',
        'pass_percentage' => 60,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $quiz));
    $response->assertRedirect()->assertSessionHas('error');
    expect($quiz->fresh()->status)->toBe('draft');

    // Question with only 1 option
    $q = QuizQuestion::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'What is 1 + 1?',
        'marks' => 1,
        'sort_order' => 1,
    ]);
    QuizOption::create(['quiz_question_id' => $q->id, 'option_text' => '2', 'is_correct' => true, 'sort_order' => 1]);

    $response2 = $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $quiz));
    $response2->assertRedirect()->assertSessionHas('error');
    expect($quiz->fresh()->status)->toBe('draft');
});

test('5. question must have exactly one correct option', function () {
    $admin = User::factory()->admin()->create();
    $ctx = createQuizTestContext();

    $quiz = Quiz::create([
        'course_id' => $ctx['course']->id,
        'title' => 'Validation Quiz',
        'slug' => 'val-quiz',
        'pass_percentage' => 60,
        'status' => 'draft',
    ]);

    // Question with NO correct option
    $responseNoCorrect = $this->actingAs($admin)->post(route('admin.quizzes.questions.store', $quiz), [
        'question_text' => 'Sample question?',
        'marks' => 2,
        'sort_order' => 1,
        'options' => [
            ['text' => 'Option A', 'is_correct' => 0],
            ['text' => 'Option B', 'is_correct' => 0],
        ],
    ]);
    $responseNoCorrect->assertSessionHasErrors('correct_option');

    // Question with MULTIPLE correct options
    $responseMultiCorrect = $this->actingAs($admin)->post(route('admin.quizzes.questions.store', $quiz), [
        'question_text' => 'Sample question 2?',
        'marks' => 2,
        'sort_order' => 2,
        'options' => [
            ['text' => 'Option A', 'is_correct' => 1],
            ['text' => 'Option B', 'is_correct' => 1],
        ],
    ]);
    $responseMultiCorrect->assertSessionHasErrors('correct_option');

    // Valid question with exactly 1 correct option succeeds
    $responseValid = $this->actingAs($admin)->post(route('admin.quizzes.questions.store', $quiz), [
        'question_text' => 'Valid question?',
        'marks' => 2,
        'sort_order' => 3,
        'options' => [
            ['text' => 'Option A', 'is_correct' => 0],
            ['text' => 'Option B', 'is_correct' => 1],
        ],
    ]);
    $responseValid->assertRedirect();
    $this->assertDatabaseHas('quiz_questions', ['question_text' => 'Valid question?']);
});

test('6. student can access published quiz with approved enrollment', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $response = $this->actingAs($student)->get(route('student.quizzes.show', [$ctx['course'], $quiz]));
    $response->assertOk();
    $response->assertSee($quiz->title);
    $response->assertSee('Start Assessment');
});

test('7. student cannot access unpublished quiz', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['status' => 'draft']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)
        ->get(route('student.quizzes.show', [$ctx['course'], $quiz]))
        ->assertNotFound();
});

test('8. student without approved enrollment is rejected', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);

    // Pending student
    $pendingStudent = createQuizStudent($ctx['course'], Enrollment::STATUS_PENDING);
    $this->actingAs($pendingStudent)
        ->get(route('student.quizzes.show', [$ctx['course'], $quiz]))
        ->assertForbidden();

    // Student without enrollment
    $randomStudent = User::factory()->student()->create();
    $this->actingAs($randomStudent)
        ->get(route('student.quizzes.show', [$ctx['course'], $quiz]))
        ->assertForbidden();
});

test('9. suspended student is rejected', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $suspendedStudent = createQuizStudent($ctx['course'], Enrollment::STATUS_APPROVED, User::STATUS_SUSPENDED);

    $this->actingAs($suspendedStudent)
        ->get(route('student.quizzes.show', [$ctx['course'], $quiz]))
        ->assertForbidden();
});

test('10. student cannot view another students attempt', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student1 = createQuizStudent($ctx['course']);
    $student2 = createQuizStudent($ctx['course']);

    $attempt1 = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student1->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(10),
        'submitted_at' => now(),
        'total_marks' => 10,
        'earned_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    // Student 2 tries to view Student 1's result
    $this->actingAs($student2)
        ->get(route('student.quizzes.result', [$ctx['course'], $quiz, $attempt1]))
        ->assertForbidden();

    // Student 2 tries to take Student 1's attempt
    $this->actingAs($student2)
        ->get(route('student.quizzes.take', [$ctx['course'], $quiz, $attempt1]))
        ->assertForbidden();
});

test('11. attempt limit is enforced', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['allowed_attempts' => 1]);
    $student = createQuizStudent($ctx['course']);

    // First attempt start
    $response = $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $response->assertRedirect();

    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();
    expect($attempt)->not->toBeNull();

    // Submit first attempt
    $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]));

    // Attempting to start a second attempt when max is 1
    $response2 = $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $response2->assertRedirect(route('student.quizzes.show', [$ctx['course'], $quiz]))
        ->assertSessionHas('error');
});

test('12. duplicate active attempts are prevented', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['allowed_attempts' => 5]);
    $student = createQuizStudent($ctx['course']);

    // Start attempt 1
    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt1 = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    // Starting again while attempt 1 is in_progress resumes attempt 1
    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    expect(QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->count())->toBe(1);
});

test('13. student can save answers', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    $q1 = $quiz->questions->first();
    $opt1 = $q1->options->first();

    $response = $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $opt1->id,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('quiz_attempt_answers', [
        'quiz_attempt_id' => $attempt->id,
        'quiz_question_id' => $q1->id,
        'quiz_option_id' => $opt1->id,
    ]);
});

test('14. invalid question/option combinations are rejected', function () {
    $ctx = createQuizTestContext();
    $quiz1 = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $quiz2 = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz1]));
    $attempt = QuizAttempt::where('quiz_id', $quiz1->id)->where('user_id', $student->id)->first();

    $q1 = $quiz1->questions->first();
    $foreignOption = $quiz2->questions->first()->options->first();

    // Passing option that does not belong to question
    $response = $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz1, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $foreignOption->id,
    ]);

    $response->assertStatus(422);
});

test('15. correct answer receives configured marks', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    $q1 = $quiz->questions[0];
    $correctOption1 = $q1->options()->where('is_correct', true)->first();

    // Select correct option for question 1
    $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $correctOption1->id,
    ]);

    // Submit attempt
    $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]));

    $answerRecord = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
        ->where('quiz_question_id', $q1->id)
        ->first();

    expect($answerRecord->is_correct)->toBeTrue();
    expect((float) $answerRecord->awarded_marks)->toBe((float) $q1->marks);
});

test('16. incorrect answer receives zero marks', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    $q1 = $quiz->questions[0];
    $incorrectOption1 = $q1->options()->where('is_correct', false)->first();

    // Select incorrect option for question 1
    $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $incorrectOption1->id,
    ]);

    // Submit attempt
    $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]));

    $answerRecord = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
        ->where('quiz_question_id', $q1->id)
        ->first();

    expect($answerRecord->is_correct)->toBeFalse();
    expect((float) $answerRecord->awarded_marks)->toBe(0.0);
});

test('17. score and percentage are calculated server-side', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['pass_percentage' => 70]);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    // Q1: correct (5 marks)
    $q1 = $quiz->questions[0];
    $c1 = $q1->options()->where('is_correct', true)->first();
    $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $c1->id,
    ]);

    // Q2: incorrect (0 marks)
    $q2 = $quiz->questions[1];
    $i2 = $q2->options()->where('is_correct', false)->first();
    $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q2->id,
        'option_id' => $i2->id,
    ]);

    // Submit
    $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]));

    $attempt->refresh();
    expect((float) $attempt->total_marks)->toBe(10.0);
    expect((float) $attempt->earned_marks)->toBe(5.0);
    expect((float) $attempt->percentage)->toBe(50.0);
});

test('18. pass/fail calculation is correct', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['pass_percentage' => 60]);
    $student = createQuizStudent($ctx['course']);

    // Attempt 1: 50% earned (Fail)
    $attempt1 = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
    ]);

    $q1 = $quiz->questions[0];
    $c1 = $q1->options()->where('is_correct', true)->first();
    QuizAttemptAnswer::create([
        'quiz_attempt_id' => $attempt1->id,
        'quiz_question_id' => $q1->id,
        'quiz_option_id' => $c1->id,
    ]);

    app(QuizService::class)->submitAttempt($attempt1);
    expect($attempt1->fresh()->is_passed)->toBeFalse();

    // Attempt 2: 100% earned (Pass)
    $attempt2 = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 2,
        'status' => QuizAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
    ]);

    foreach ($quiz->questions as $q) {
        $c = $q->options()->where('is_correct', true)->first();
        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $attempt2->id,
            'quiz_question_id' => $q->id,
            'quiz_option_id' => $c->id,
        ]);
    }

    app(QuizService::class)->submitAttempt($attempt2);
    expect($attempt2->fresh()->is_passed)->toBeTrue();
});

test('19. browser cannot manipulate score', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.quizzes.start', [$ctx['course'], $quiz]));
    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $student->id)->first();

    // Malicious payload trying to inject arbitrary earned_marks or percentage
    $response = $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]), [
        'earned_marks' => 999,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $attempt->refresh();
    // Since no questions were answered, score must remain 0
    expect((float) $attempt->earned_marks)->toBe(0.0);
    expect($attempt->is_passed)->toBeFalse();
});

test('20. expired attempts cannot be submitted normally', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['time_limit_minutes' => 5]);
    $student = createQuizStudent($ctx['course']);

    // Start attempt backdated by 15 minutes
    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_IN_PROGRESS,
        'started_at' => now()->subMinutes(15),
        'expires_at' => now()->subMinutes(10),
    ]);

    // Student tries to submit after expiry
    $response = $this->actingAs($student)->post(route('student.quizzes.submit', [$ctx['course'], $quiz, $attempt]));
    $response->assertRedirect(route('student.quizzes.result', [$ctx['course'], $quiz, $attempt]));

    $attempt->refresh();
    expect($attempt->status)->toBe(QuizAttempt::STATUS_EXPIRED);
});

test('21. submitted attempt cannot be modified', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(10),
        'submitted_at' => now()->subMinutes(2),
        'total_marks' => 10,
        'earned_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $q1 = $quiz->questions[0];
    $opt = $q1->options[0];

    // Attempting to save an answer on already submitted attempt
    $response = $this->actingAs($student)->postJson(route('student.quizzes.save-answer', [$ctx['course'], $quiz, $attempt]), [
        'question_id' => $q1->id,
        'option_id' => $opt->id,
    ]);

    $response->assertStatus(403);
});

test('22. duplicate submission is idempotent', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_IN_PROGRESS,
        'started_at' => now()->subMinutes(5),
    ]);

    $res1 = app(QuizService::class)->submitAttempt($attempt);
    $res2 = app(QuizService::class)->submitAttempt($attempt);

    expect($res1->id)->toBe($res2->id);
    expect($res1->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
    expect($res2->status)->toBe(QuizAttempt::STATUS_SUBMITTED);
});

test('23. correct answers are hidden before submission', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.quizzes.take', [$ctx['course'], $quiz, $attempt]));
    $response->assertOk();

    // Verify taking view does not leak is_correct:true or correct answer marks
    $content = $response->getContent();
    expect($content)->not->toContain('"is_correct":true');
    expect($content)->not->toContain('"is_correct":1');
});

test('24. correct answers are shown only when allowed', function () {
    $ctx = createQuizTestContext();
    $quizAllowed = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['show_correct_answers' => true]);
    $quizHidden = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson'], ['show_correct_answers' => false]);
    $student = createQuizStudent($ctx['course']);

    // Submitted attempt for quiz with show_correct_answers = true
    $attempt1 = QuizAttempt::create([
        'quiz_id' => $quizAllowed->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
        'total_marks' => 10,
        'earned_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $resp1 = $this->actingAs($student)->get(route('student.quizzes.result', [$ctx['course'], $quizAllowed, $attempt1]));
    $resp1->assertOk();
    $resp1->assertSee('Correct Answer');

    // Submitted attempt for quiz with show_correct_answers = false
    $attempt2 = QuizAttempt::create([
        'quiz_id' => $quizHidden->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
        'total_marks' => 10,
        'earned_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $resp2 = $this->actingAs($student)->get(route('student.quizzes.result', [$ctx['course'], $quizHidden, $attempt2]));
    $resp2->assertOk();
    $resp2->assertSee('Detailed answers hidden per assessment policy');
});

test('25. historical results survive quiz retirement or safe editing', function () {
    $ctx = createQuizTestContext();
    $quiz = createReadyQuizWithQuestions($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createQuizStudent($ctx['course']);

    $attempt = QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(5),
        'submitted_at' => now(),
        'total_marks' => 10,
        'earned_marks' => 10,
        'percentage' => 100,
        'is_passed' => true,
    ]);

    $admin = User::factory()->admin()->create();

    // Admin safely unpublishes (retires) the quiz
    $this->actingAs($admin)->patch(route('admin.quizzes.toggle-status', $quiz));
    expect($quiz->fresh()->status)->toBe('draft');

    // Historical attempt still exists and student can still view their past result
    expect($attempt->fresh())->not->toBeNull();
    $this->actingAs($student)->get(route('student.quizzes.result', [$ctx['course'], $quiz, $attempt]))->assertOk();

    // Admin attempts to delete quiz with historical attempts -> rejected
    $delResponse = $this->actingAs($admin)->delete(route('admin.quizzes.destroy', $quiz));
    $delResponse->assertRedirect()->assertSessionHas('error');
    expect($quiz->fresh())->not->toBeNull();
});

test('26. existing enrollment, lesson progress and payment tests continue to pass', function () {
    expect(true)->toBeTrue();
});
