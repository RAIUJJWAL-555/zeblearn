<?php

use App\Http\Controllers\Admin\AdminAssignmentController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminCurriculumController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminQuizQuestionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\StudentAssignmentController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentQuizController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // Password Recovery
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Authenticated session management
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Student routes: Learning area, dashboard, enrollment and payments
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/courses/{course}/enroll', [EnrollmentController::class, 'create'])->name('courses.enroll.get');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
    Route::get('/learning/{course:slug}', [StudentCourseController::class, 'show'])->name('student.courses.show');
    Route::get('/learning/{course:slug}/resume', [StudentCourseController::class, 'resume'])->name('student.courses.resume');
    Route::get('/learning/{course:slug}/lessons/{lesson}', [StudentCourseController::class, 'lesson'])->name('student.courses.lesson');
    Route::post('/learning/{course:slug}/lessons/{lesson}/complete', [StudentCourseController::class, 'completeLesson'])->name('student.courses.lesson.complete');
    Route::match(['post', 'delete'], '/learning/{course:slug}/lessons/{lesson}/incomplete', [StudentCourseController::class, 'incompleteLesson'])->name('student.courses.lesson.incomplete');

    // Quiz & Assessment System (Student)
    Route::get('/learning/{course:slug}/quizzes/{quiz:slug}', [StudentQuizController::class, 'show'])->name('student.quizzes.show');
    Route::post('/learning/{course:slug}/quizzes/{quiz:slug}/start', [StudentQuizController::class, 'start'])->name('student.quizzes.start');
    Route::get('/learning/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}', [StudentQuizController::class, 'take'])->name('student.quizzes.take');
    Route::post('/learning/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/save-answer', [StudentQuizController::class, 'saveAnswer'])->name('student.quizzes.save-answer');
    Route::post('/learning/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/learning/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/result', [StudentQuizController::class, 'result'])->name('student.quizzes.result');
    Route::get('/learning/{course:slug}/quizzes/{quiz:slug}/history', [StudentQuizController::class, 'history'])->name('student.quizzes.history');

    // Assignment System (Student)
    Route::get('/learning/{course:slug}/assignments', [StudentAssignmentController::class, 'index'])->name('student.assignments.index');
    Route::get('/learning/{course:slug}/assignments/{assignment:slug}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
    Route::post('/learning/{course:slug}/assignments/{assignment:slug}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
    Route::get('/learning/{course:slug}/assignments/{assignment:slug}/submissions/{submission}', [StudentAssignmentController::class, 'submissionShow'])->name('student.assignments.submissions.show');
    Route::get('/learning/{course:slug}/assignments/{assignment:slug}/submissions/{submission}/files/{file}/download', [StudentAssignmentController::class, 'downloadFile'])->name('student.assignments.files.download');

    // Online Payment Checkout & History with rate limiting
    Route::get('/checkout/{course:slug}', [PaymentController::class, 'checkout'])
        ->middleware('throttle:30,1')
        ->name('student.checkout');
    Route::post('/checkout/{course:slug}/verify', [PaymentController::class, 'verify'])
        ->middleware('throttle:10,1')
        ->name('student.checkout.verify');
    Route::get('/student/payments', [PaymentController::class, 'history'])->name('student.payments.history');
    Route::get('/student/payments/{payment}', [PaymentController::class, 'receipt'])->name('student.payments.receipt');
});

Route::middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/instructor/dashboard', fn () => response('Instructor Dashboard Placeholder'))->name('instructor.dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Course Management CRUD
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
    Route::patch('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('courses.toggle-status');

    // Course Curriculum & Module Management
    Route::get('/courses/{course}/curriculum', [AdminCurriculumController::class, 'index'])->name('courses.curriculum');
    Route::post('/courses/{course}/modules', [AdminCurriculumController::class, 'storeModule'])->name('courses.modules.store');
    Route::put('/courses/{course}/modules/{module}', [AdminCurriculumController::class, 'updateModule'])->name('courses.modules.update');
    Route::delete('/courses/{course}/modules/{module}', [AdminCurriculumController::class, 'destroyModule'])->name('courses.modules.destroy');
    Route::match(['post', 'patch'], '/courses/{course}/modules/reorder', [AdminCurriculumController::class, 'reorderModules'])->name('courses.modules.reorder');
    Route::post('/courses/{course}/modules/{module}/lessons', [AdminCurriculumController::class, 'storeLesson'])->name('courses.lessons.store');
    Route::put('/courses/{course}/modules/{module}/lessons/{lesson}', [AdminCurriculumController::class, 'updateLesson'])->name('courses.lessons.update');
    Route::delete('/courses/{course}/modules/{module}/lessons/{lesson}', [AdminCurriculumController::class, 'destroyLesson'])->name('courses.lessons.destroy');
    Route::match(['post', 'patch'], '/courses/{course}/modules/{module}/lessons/reorder', [AdminCurriculumController::class, 'reorderLessons'])->name('courses.lessons.reorder');

    // Student Enrollment Approvals & Management
    Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/{enrollment}', [AdminEnrollmentController::class, 'show'])->name('enrollments.show');
    Route::patch('/enrollments/{enrollment}/approve', [AdminEnrollmentController::class, 'approve'])->name('enrollments.approve');
    Route::patch('/enrollments/{enrollment}/reject', [AdminEnrollmentController::class, 'reject'])->name('enrollments.reject');
    Route::patch('/enrollments/{enrollment}/cancel', [AdminEnrollmentController::class, 'cancel'])->name('enrollments.cancel');

    // Payment Management & Reconciliation
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');

    // Quiz & Assessment Management
    Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/create', [AdminQuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes', [AdminQuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}', [AdminQuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [AdminQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::patch('/quizzes/{quiz}/toggle-status', [AdminQuizController::class, 'toggleStatus'])->name('quizzes.toggle-status');
    Route::get('/quizzes/{quiz}/attempts/{attempt}', [AdminQuizController::class, 'attemptShow'])->name('quizzes.attempts.show');

    // Quiz Question Management
    Route::post('/quizzes/{quiz}/questions', [AdminQuizQuestionController::class, 'store'])->name('quizzes.questions.store');
    Route::put('/quizzes/{quiz}/questions/{question}', [AdminQuizQuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('/quizzes/{quiz}/questions/{question}', [AdminQuizQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    Route::post('/quizzes/{quiz}/questions/reorder', [AdminQuizQuestionController::class, 'reorder'])->name('quizzes.questions.reorder');

    // Assignment Management & Grading
    Route::get('/assignments', [AdminAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AdminAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AdminAssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}', [AdminAssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/assignments/{assignment}/edit', [AdminAssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AdminAssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AdminAssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::patch('/assignments/{assignment}/toggle-status', [AdminAssignmentController::class, 'toggleStatus'])->name('assignments.toggle-status');
    Route::get('/assignments/{assignment}/submissions/{submission}', [AdminAssignmentController::class, 'submissionShow'])->name('assignments.submissions.show');
    Route::post('/assignments/{assignment}/submissions/{submission}/grade', [AdminAssignmentController::class, 'grade'])->name('assignments.submissions.grade');
    Route::get('/assignments/{assignment}/submissions/{submission}/files/{file}/download', [AdminAssignmentController::class, 'downloadFile'])->name('assignments.files.download');
});
