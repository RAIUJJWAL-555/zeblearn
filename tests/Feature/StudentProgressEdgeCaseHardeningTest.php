<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\LessonProgressService;
use Illuminate\Database\QueryException;

function setupHardeningCurriculum(string $slug = 'hardening-test-course'): array
{
    $course = Course::create([
        'name' => 'Hardening Test Course',
        'slug' => $slug,
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Security edge case verification course.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Core Setup',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Lesson 1: System Config',
        'content_type' => 'text',
        'content' => 'System configuration documentation.',
        'duration_minutes' => 15,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return [
        'course' => $course,
        'module' => $module,
        'lesson' => $lesson,
    ];
}

function setupHardeningStudent(Course $course): User
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

test('1. student cannot mark an unpublished lesson incomplete', function () {
    $curriculum = setupHardeningCurriculum('unpublished-lesson-incomplete');
    $student = setupHardeningStudent($curriculum['course']);

    // Mark lesson as completed first
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Now lesson is changed to draft by an instructor
    $curriculum['lesson']->update(['status' => 'draft']);

    $response = $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson'],
    ]));

    $response->assertNotFound();

    // Verify status was not reverted
    expect(LessonProgress::where('user_id', $student->id)->where('lesson_id', $curriculum['lesson']->id)->first()->status)
        ->toBe(LessonProgress::STATUS_COMPLETED);
});

test('2. student cannot mark a lesson from another course incomplete', function () {
    $curriculumA = setupHardeningCurriculum('course-a-incomplete-test');
    $curriculumB = setupHardeningCurriculum('course-b-incomplete-test');
    $studentA = setupHardeningStudent($curriculumA['course']);

    // Student A has progress on course A's lesson
    LessonProgress::create([
        'user_id' => $studentA->id,
        'lesson_id' => $curriculumA['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Student A attempts to call incomplete on Course B's lesson using Course A's route
    $response = $this->actingAs($studentA)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculumA['course'],
        'lesson' => $curriculumB['lesson'],
    ]));

    $response->assertNotFound();
});

test('3. student cannot mark a lesson incomplete after enrollment is revoked', function () {
    $curriculum = setupHardeningCurriculum('revoked-enrollment-test');
    $student = setupHardeningStudent($curriculum['course']);

    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Revoke enrollment (e.g. cancelled or rejected by admin)
    Enrollment::where('user_id', $student->id)
        ->where('course_id', $curriculum['course']->id)
        ->update(['status' => Enrollment::STATUS_CANCELLED]);

    $response = $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson'],
    ]));

    $response->assertForbidden();

    // Also verify rejection state
    Enrollment::where('user_id', $student->id)
        ->where('course_id', $curriculum['course']->id)
        ->update(['status' => Enrollment::STATUS_REJECTED]);

    $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson'],
    ]))->assertForbidden();
});

test('4. suspended student cannot mark a lesson incomplete', function () {
    $curriculum = setupHardeningCurriculum('suspended-incomplete-test');
    $student = setupHardeningStudent($curriculum['course']);

    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Suspend the student account
    $student->forceFill(['status' => User::STATUS_SUSPENDED])->save();

    $response = $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson'],
    ]));

    $response->assertForbidden();
});

test('5. concurrent or duplicate first-access attempts cannot create duplicate records', function () {
    $curriculum = setupHardeningCurriculum('concurrent-access-test');
    $student = setupHardeningStudent($curriculum['course']);
    $service = app(LessonProgressService::class);

    // Initial first access creates the single row
    $firstRecord = $service->recordLessonAccess($student, $curriculum['lesson']);
    expect($firstRecord)->not->toBeNull();
    expect($firstRecord->status)->toBe(LessonProgress::STATUS_IN_PROGRESS);

    // Second simulated concurrent access on the same user and lesson
    $secondRecord = $service->recordLessonAccess($student, $curriculum['lesson']);
    expect($secondRecord->id)->toBe($firstRecord->id);

    // Verify exact count in database is strictly 1
    $count = LessonProgress::where('user_id', $student->id)
        ->where('lesson_id', $curriculum['lesson']->id)
        ->count();

    expect($count)->toBe(1);

    // Simulate duplicate-key recovery mechanism directly with simulated QueryException
    $pdoException = new PDOException('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry');
    $queryException = new QueryException('sqlite', 'insert into lesson_progress ...', [], $pdoException);

    expect($service->isDuplicateKeyException($queryException))->toBeTrue();
});

test('6. reopening a completed lesson preserves completion', function () {
    $curriculum = setupHardeningCurriculum('reopen-preserves-completion-test');
    $student = setupHardeningStudent($curriculum['course']);
    $service = app(LessonProgressService::class);

    $originalStartedAt = now()->subHours(10);
    $originalCompletedAt = now()->subHours(2);

    $progress = LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'first_started_at' => $originalStartedAt,
        'last_accessed_at' => $originalCompletedAt,
        'completed_at' => $originalCompletedAt,
    ]);

    // Student reopens lesson through recordLessonAccess
    $reopened = $service->recordLessonAccess($student, $curriculum['lesson']);

    expect($reopened->status)->toBe(LessonProgress::STATUS_COMPLETED);
    expect($reopened->completed_at->toDateTimeString())->toBe($originalCompletedAt->toDateTimeString());
    expect($reopened->first_started_at->toDateTimeString())->toBe($originalStartedAt->toDateTimeString());

    // Also verify via GET HTTP request to player
    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson'],
    ]));

    $response->assertOk();
    $progress->refresh();
    expect($progress->status)->toBe(LessonProgress::STATUS_COMPLETED);
    expect($progress->completed_at->toDateTimeString())->toBe($originalCompletedAt->toDateTimeString());
});

test('7. admin cannot accidentally delete a lesson with historical progress', function () {
    $curriculum = setupHardeningCurriculum('admin-lesson-delete-protection-test');
    $student = setupHardeningStudent($curriculum['course']);
    $admin = User::factory()->admin()->active()->create();

    // Create student progress for the lesson
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
        'first_started_at' => now(),
        'last_accessed_at' => now(),
    ]);

    // Admin attempts to delete the lesson
    $response = $this->actingAs($admin)->delete(route('admin.courses.lessons.destroy', [
        'course' => $curriculum['course'],
        'module' => $curriculum['module'],
        'lesson' => $curriculum['lesson'],
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Lesson must still exist in DB
    expect(Lesson::where('id', $curriculum['lesson']->id)->exists())->toBeTrue();
    expect(LessonProgress::where('lesson_id', $curriculum['lesson']->id)->exists())->toBeTrue();
});

test('8. course and module deletion does not silently erase student progress', function () {
    $curriculum = setupHardeningCurriculum('admin-module-course-delete-protection-test');
    $student = setupHardeningStudent($curriculum['course']);
    $admin = User::factory()->admin()->active()->create();

    // Create student progress
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'first_started_at' => now(),
        'last_accessed_at' => now(),
        'completed_at' => now(),
    ]);

    // 1. Admin attempts to delete the module
    $responseModule = $this->actingAs($admin)->delete(route('admin.courses.modules.destroy', [
        'course' => $curriculum['course'],
        'module' => $curriculum['module'],
    ]));

    $responseModule->assertRedirect();
    $responseModule->assertSessionHas('error');
    expect(CourseModule::where('id', $curriculum['module']->id)->exists())->toBeTrue();
    expect(Lesson::where('id', $curriculum['lesson']->id)->exists())->toBeTrue();
    expect(LessonProgress::where('lesson_id', $curriculum['lesson']->id)->exists())->toBeTrue();

    // 2. Admin attempts to delete the course
    $responseCourse = $this->actingAs($admin)->delete(route('admin.courses.destroy', $curriculum['course']));

    $responseCourse->assertRedirect();
    $responseCourse->assertSessionHas('error');
    expect(Course::where('id', $curriculum['course']->id)->exists())->toBeTrue();
    expect(LessonProgress::where('lesson_id', $curriculum['lesson']->id)->exists())->toBeTrue();
});

test('9. existing course and lesson CRUD remains functional where deletion is permitted', function () {
    $admin = User::factory()->admin()->active()->create();

    $cleanCourse = Course::create([
        'name' => 'Clean Removable Course',
        'slug' => 'clean-removable-course',
        'category' => 'salesforce',
        'status' => 'available',
        'short_description' => 'Course without any students or progress.',
        'fee' => 15000,
        'published_at' => now(),
    ]);

    $cleanModule = CourseModule::create([
        'course_id' => $cleanCourse->id,
        'title' => 'Empty Module',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $cleanLesson = Lesson::create([
        'course_module_id' => $cleanModule->id,
        'title' => 'Unenrolled Lesson',
        'content_type' => 'text',
        'content' => 'Content without any progress.',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    // 1. Delete lesson with no progress -> succeeds
    $delLessonResponse = $this->actingAs($admin)->delete(route('admin.courses.lessons.destroy', [
        'course' => $cleanCourse,
        'module' => $cleanModule,
        'lesson' => $cleanLesson,
    ]));

    $delLessonResponse->assertRedirect();
    $delLessonResponse->assertSessionHas('success');
    expect(Lesson::where('id', $cleanLesson->id)->exists())->toBeFalse();

    // 2. Delete empty module with no progress -> succeeds
    $delModuleResponse = $this->actingAs($admin)->delete(route('admin.courses.modules.destroy', [
        'course' => $cleanCourse,
        'module' => $cleanModule,
    ]));

    $delModuleResponse->assertRedirect();
    $delModuleResponse->assertSessionHas('success');
    expect(CourseModule::where('id', $cleanModule->id)->exists())->toBeFalse();

    // 3. Delete course with no enrollments and no progress -> succeeds
    $delCourseResponse = $this->actingAs($admin)->delete(route('admin.courses.destroy', $cleanCourse));

    $delCourseResponse->assertRedirect();
    $delCourseResponse->assertSessionHas('success');
    expect(Course::where('id', $cleanCourse->id)->exists())->toBeFalse();
});
