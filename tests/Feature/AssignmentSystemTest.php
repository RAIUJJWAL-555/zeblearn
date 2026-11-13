<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\AssignmentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

function setupAssignmentTestContext(): array
{
    $course = Course::create([
        'name' => 'Enterprise System Architecture',
        'slug' => 'ent-sys-arch-'.uniqid(),
        'category' => 'software-development',
        'status' => 'available',
        'short_description' => 'Scalable microservices and cloud infrastructure.',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Distributed Transactions',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Lesson 1.1: Event Sourcing & CQRS',
        'content_type' => 'text',
        'content' => 'Hands on implementation of event sourcing.',
        'duration_minutes' => 30,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('course', 'module', 'lesson');
}

function createAssignmentWithRules(Course $course, ?CourseModule $module = null, ?Lesson $lesson = null, array $overrides = []): Assignment
{
    return Assignment::create(array_merge([
        'course_id' => $course->id,
        'course_module_id' => $module?->id,
        'lesson_id' => $lesson?->id,
        'title' => 'Microservice Ledger Project',
        'slug' => 'ledger-proj-'.uniqid(),
        'description' => 'Build an event-sourced ledger service.',
        'instructions' => 'Include architecture diagrams and unit test results.',
        'max_marks' => 100,
        'passing_marks' => 70,
        'start_at' => now()->subDay(),
        'due_at' => now()->addDays(7),
        'allow_late_submissions' => false,
        'max_attempts' => 2,
        'allow_text_submission' => true,
        'allow_file_submission' => true,
        'allowed_file_types' => 'pdf,doc,docx,zip,txt',
        'max_file_size_mb' => 5,
        'status' => Assignment::STATUS_PUBLISHED,
    ], $overrides));
}

function createEnrolledStudent(Course $course): User
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

// 1. Admin can create assignments.
test('1. admin can create assignments', function () {
    $ctx = setupAssignmentTestContext();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.assignments.store'), [
        'course_id' => $ctx['course']->id,
        'course_module_id' => $ctx['module']->id,
        'lesson_id' => $ctx['lesson']->id,
        'title' => 'Cloud Native Ledger Project',
        'description' => 'Capstone distributed ledger task.',
        'instructions' => 'Provide code repository ZIP and design doc.',
        'max_marks' => 100,
        'passing_marks' => 75,
        'max_attempts' => 3,
        'allow_text_submission' => true,
        'allow_file_submission' => true,
        'allowed_file_types' => 'pdf,zip',
        'max_file_size_mb' => 10,
    ]);

    $response->assertRedirect();

    $assignment = Assignment::where('course_id', $ctx['course']->id)
        ->where('title', 'Cloud Native Ledger Project')
        ->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->max_marks)->toBe(100);
    expect($assignment->passing_marks)->toBe(75);
    expect($assignment->max_attempts)->toBe(3);
    expect($assignment->status)->toBe(Assignment::STATUS_DRAFT);
});

// 2. Admin can update assignments before submissions exist.
test('2. admin can update assignments before submissions exist', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.assignments.update', $assignment), [
        'title' => 'Updated Architecture Assignment',
        'max_marks' => 150,
        'passing_marks' => 100,
        'max_attempts' => 4,
        'due_at' => now()->addDays(14)->format('Y-m-d H:i:s'),
    ]);

    $response->assertRedirect();

    $assignment->refresh();
    expect($assignment->title)->toBe('Updated Architecture Assignment');
    expect($assignment->max_marks)->toBe(150);
    expect($assignment->passing_marks)->toBe(100);
    expect($assignment->max_attempts)->toBe(4);
});

// 3. Assignment context is validated.
test('3. assignment context is validated (course, module, lesson mismatch rejected)', function () {
    $ctx = setupAssignmentTestContext();
    $admin = User::factory()->admin()->create();

    $otherCourse = Course::create([
        'name' => 'Other Course',
        'slug' => 'other-course-'.uniqid(),
        'category' => 'engineering',
        'status' => 'available',
        'short_description' => 'Other description.',
        'fee' => 20000,
    ]);

    // Module belongs to course 1, but we pass course_id for other course
    $response = $this->actingAs($admin)->post(route('admin.assignments.store'), [
        'course_id' => $otherCourse->id,
        'course_module_id' => $ctx['module']->id,
        'title' => 'Mismatched Context Assignment',
    ]);

    $response->assertSessionHasErrors(['course_module_id']);
    expect(Assignment::where('title', 'Mismatched Context Assignment')->exists())->toBeFalse();
});

// 4. Students cannot access unpublished assignments.
test('4. students cannot access unpublished assignments', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'status' => Assignment::STATUS_DRAFT,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $response = $this->actingAs($student)->get(route('student.assignments.show', [$ctx['course'], $assignment]));
    $response->assertStatus(404);
});

// 5. Students without approved enrollment cannot submit.
test('5. students without approved enrollment cannot submit', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $unenrolledStudent = User::factory()->student()->active()->create();

    $response = $this->actingAs($unenrolledStudent)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'Unenrolled student attempting to turn in work.',
    ]);

    $response->assertStatus(403);
    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->exists())->toBeFalse();
});

// 6. Students can view available assignments.
test('6. students can view available assignments within enrolled course', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createEnrolledStudent($ctx['course']);

    $response = $this->actingAs($student)->get(route('student.assignments.index', $ctx['course']));
    $response->assertOk();
    $response->assertSee($assignment->title);
    $response->assertSee((string) $assignment->max_marks);
});

// 7. Students can submit written responses.
test('7. students can submit written responses', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createEnrolledStudent($ctx['course']);

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'This is my comprehensive design documentation for the distributed ledger service.',
    ]);

    $response->assertRedirect(route('student.assignments.show', [$ctx['course'], $assignment]));

    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('user_id', $student->id)
        ->first();

    expect($submission)->not->toBeNull();
    expect($submission->attempt_number)->toBe(1);
    expect($submission->status)->toBe(AssignmentSubmission::STATUS_SUBMITTED);
    expect($submission->submission_text)->toContain('distributed ledger service');
    expect($submission->is_late)->toBeFalse();
});

// 8. Students can upload permitted files.
test('8. students can upload permitted files', function () {
    Storage::fake('local');

    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'allowed_file_types' => 'pdf,zip',
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $file = UploadedFile::fake()->create('architecture-diagram.pdf', 500, 'application/pdf');

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'See attached PDF documentation.',
        'files' => [$file],
    ]);

    $response->assertRedirect();

    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('user_id', $student->id)
        ->with('files')
        ->first();

    expect($submission)->not->toBeNull();
    expect($submission->files)->toHaveCount(1);
    $storedFile = $submission->files->first();
    expect($storedFile->original_filename)->toBe('architecture-diagram.pdf');
    expect(Storage::disk('local')->exists($storedFile->stored_path))->toBeTrue();
});

// 9. Invalid file types are rejected.
test('9. invalid file types are rejected', function () {
    Storage::fake('local');

    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'allowed_file_types' => 'pdf,txt',
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $exeFile = UploadedFile::fake()->create('malware.exe', 200, 'application/x-msdownload');

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'files' => [$exeFile],
    ]);

    $response->assertStatus(422);
    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->exists())->toBeFalse();
});

// 10. Oversized uploads are rejected.
test('10. oversized uploads are rejected', function () {
    Storage::fake('local');

    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_file_size_mb' => 2,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    // 4MB file exceeds 2MB limit
    $largeFile = UploadedFile::fake()->create('huge-video.mp4', 4096);

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'files' => [$largeFile],
    ]);

    $response->assertSessionHasErrors(['files.0']);
    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->exists())->toBeFalse();

    // Also assert JSON request returns 422 Unprocessable Entity
    $jsonResponse = $this->actingAs($student)->postJson(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'files' => [$largeFile],
    ]);
    $jsonResponse->assertStatus(422)->assertJsonValidationErrors(['files.0']);
});

// 11. Students cannot access another student's files.
test('11. students cannot access another student\'s files', function () {
    Storage::fake('local');

    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $studentA = createEnrolledStudent($ctx['course']);
    $studentB = createEnrolledStudent($ctx['course']);

    $file = UploadedFile::fake()->create('studentA-private.pdf', 300, 'application/pdf');

    $service = app(AssignmentService::class);
    $submissionA = $service->submitAssignment($studentA, $assignment, 'Confidential text', [$file]);
    $fileRecord = $submissionA->files->first();

    // Student B attempts to download Student A's file
    $response = $this->actingAs($studentB)->get(route('student.assignments.files.download', [
        $ctx['course'],
        $assignment,
        $submissionA,
        $fileRecord,
    ]));

    $response->assertStatus(403);
});

// 12. Submission deadlines are enforced.
test('12. submission deadlines are enforced (rejected when late submissions disabled)', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'due_at' => now()->subDay(), // Expired deadline
        'allow_late_submissions' => false,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'Submitting past deadline.',
    ]);

    $response->assertStatus(422);
    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->exists())->toBeFalse();
});

// 13. Late submission policy works correctly.
test('13. late submission policy works correctly (accepted and marked is_late)', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'due_at' => now()->subHours(5),
        'allow_late_submissions' => true,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $response = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'Accepted late submission.',
    ]);

    $response->assertRedirect();

    $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('user_id', $student->id)
        ->first();

    expect($submission)->not->toBeNull();
    expect($submission->is_late)->toBeTrue();
});

// 14. Maximum attempts are enforced.
test('14. maximum attempts are enforced', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_attempts' => 2,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);

    // Attempt 1
    $sub1 = $service->submitAssignment($student, $assignment, 'Attempt 1 writeup');
    expect($sub1->attempt_number)->toBe(1);

    // Attempt 2
    $sub2 = $service->submitAssignment($student, $assignment, 'Attempt 2 writeup');
    expect($sub2->attempt_number)->toBe(2);

    // Attempt 3 must be blocked with 403
    expect(fn () => $service->submitAssignment($student, $assignment, 'Attempt 3 writeup'))
        ->toThrow(HttpException::class);

    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $student->id)->count())->toBe(2);
});

// 15. Concurrent submissions cannot exceed the attempt limit.
test('15. concurrent submissions cannot exceed the attempt limit', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_attempts' => 1,
    ]);
    $student = createEnrolledStudent($ctx['course']);
    $service = app(AssignmentService::class);

    // First submission succeeds
    $sub1 = $service->submitAssignment($student, $assignment, 'Initial submission');
    expect($sub1)->not->toBeNull();

    // Competing submission rejected under lock
    expect(fn () => $service->submitAssignment($student, $assignment, 'Concurrent submission'))
        ->toThrow(HttpException::class);

    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $student->id)->count())->toBe(1);
});

// 16. Duplicate requests do not create duplicate submissions.
test('16. duplicate requests do not create duplicate submissions', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_attempts' => 1,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'First attempt text.',
    ])->assertRedirect();

    // Duplicate submission attempt
    $res2 = $this->actingAs($student)->post(route('student.assignments.submit', [$ctx['course'], $assignment]), [
        'submission_text' => 'Duplicate attempt text.',
    ]);

    $res2->assertStatus(403);
    expect(AssignmentSubmission::where('assignment_id', $assignment->id)->where('user_id', $student->id)->count())->toBe(1);
});

// 17. Administrators can grade submissions.
test('17. administrators can grade submissions', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_marks' => 100,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Student architecture solution');

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.assignments.submissions.grade', [$assignment, $submission]), [
        'marks_awarded' => 92,
        'feedback' => 'Exceptional schema design and clear event stream layout.',
        'private_notes' => 'Candidate exhibits strong senior backend understanding.',
        'action' => 'grade',
    ]);

    $response->assertRedirect();

    $submission->refresh();
    expect($submission->status)->toBe(AssignmentSubmission::STATUS_GRADED);
    expect($submission->marks_awarded)->toBe(92);
    expect($submission->feedback)->toContain('Exceptional schema design');
    expect($submission->private_notes)->toContain('Candidate exhibits strong');
    expect($submission->grader_id)->toBe($admin->id);
    expect($submission->graded_at)->not->toBeNull();
});

// 18. Marks cannot exceed maximum marks.
test('18. marks cannot exceed maximum marks', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_marks' => 50,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Solution writeup');

    $admin = User::factory()->admin()->create();

    // Trying to award 75 on a 50 max marks assignment
    $response = $this->actingAs($admin)->postJson(route('admin.assignments.submissions.grade', [$assignment, $submission]), [
        'marks_awarded' => 75,
        'feedback' => 'Over score test.',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['marks_awarded']);
    expect($submission->fresh()->status)->toBe(AssignmentSubmission::STATUS_SUBMITTED);
});

// 19. Students cannot modify grading records.
test('19. students cannot modify grading records', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Student solution');

    // Student tries to POST to grade route
    $response = $this->actingAs($student)->post(route('admin.assignments.submissions.grade', [$assignment, $submission]), [
        'marks_awarded' => 100,
        'feedback' => 'Hacked feedback.',
    ]);

    $response->assertStatus(403);
    expect($submission->fresh()->marks_awarded)->toBeNull();
});

// 20. Students can view published grading results.
test('20. students can view published grading results', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_marks' => 100,
        'passing_marks' => 70,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Verified solution');

    $admin = User::factory()->admin()->create();
    $service->gradeSubmission($admin, $submission, 88, 'Great analysis of concurrency!');

    $response = $this->actingAs($student)->get(route('student.assignments.submissions.show', [$ctx['course'], $assignment, $submission]));
    $response->assertOk();
    $response->assertSee('88');
    $response->assertSee('Passed');
    $response->assertSee('Great analysis of concurrency!');
});

// 21. Private administrator notes are not exposed.
test('21. private administrator notes are not exposed to students', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Submission content');

    $admin = User::factory()->admin()->create();
    $service->gradeSubmission(
        $admin,
        $submission,
        85,
        'Visible public feedback for student.',
        'TOP_SECRET_INTERNAL_NOTE: Candidate flagged for staff evaluation.'
    );

    $response = $this->actingAs($student)->get(route('student.assignments.submissions.show', [$ctx['course'], $assignment, $submission]));
    $response->assertOk();
    $response->assertSee('Visible public feedback for student.');
    $response->assertDontSee('TOP_SECRET_INTERNAL_NOTE');
});

// 22. Historical submissions remain accessible after unpublishing assignment.
test('22. historical submissions remain accessible after unpublishing assignment', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson']);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $submission = $service->submitAssignment($student, $assignment, 'Historical submission');

    $admin = User::factory()->admin()->create();
    $service->gradeSubmission($admin, $submission, 95, 'Archival grade');

    // Admin unpublishes the assignment
    $this->actingAs($admin)->patch(route('admin.assignments.toggle-status', $assignment));
    expect($assignment->fresh()->status)->toBe(Assignment::STATUS_DRAFT);

    // Submission and grade remains intact and accessible to student
    $response = $this->actingAs($student)->get(route('student.assignments.submissions.show', [$ctx['course'], $assignment, $submission]));
    $response->assertOk();
    $response->assertSee('95');
    $response->assertSee('Archival grade');
});

// 23. Structural settings cannot be modified once submissions exist.
test('23. structural settings cannot be modified once submissions exist', function () {
    $ctx = setupAssignmentTestContext();
    $assignment = createAssignmentWithRules($ctx['course'], $ctx['module'], $ctx['lesson'], [
        'max_marks' => 100,
        'max_attempts' => 1,
    ]);
    $student = createEnrolledStudent($ctx['course']);

    $service = app(AssignmentService::class);
    $service->submitAssignment($student, $assignment, 'Attempt to lock assignment');

    $admin = User::factory()->admin()->create();

    // Modifying max_marks rejected with 422
    $this->actingAs($admin)->putJson(route('admin.assignments.update', $assignment), [
        'max_marks' => 200,
    ])->assertStatus(422)->assertJsonValidationErrors(['max_marks']);

    // Modifying max_attempts rejected with 422
    $this->actingAs($admin)->putJson(route('admin.assignments.update', $assignment), [
        'max_attempts' => 5,
    ])->assertStatus(422)->assertJsonValidationErrors(['max_attempts']);

    // Metadata changes (title, instructions) still succeed
    $this->actingAs($admin)->putJson(route('admin.assignments.update', $assignment), [
        'title' => 'Updated Assignment Title After Submissions',
    ])->assertOk();

    expect($assignment->fresh()->title)->toBe('Updated Assignment Title After Submissions');
    expect($assignment->fresh()->max_marks)->toBe(100);
    expect($assignment->fresh()->max_attempts)->toBe(1);
});
