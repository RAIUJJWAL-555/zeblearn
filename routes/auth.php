<?php

use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminAssignmentController;
use App\Http\Controllers\Admin\AdminBlogCategoryController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminBlogDashboardController;
use App\Http\Controllers\Admin\AdminBlogMigrationController;
use App\Http\Controllers\Admin\AdminBlogTagController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminCurriculumController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminFormSubmissionController;
use App\Http\Controllers\Admin\AdminInstructorController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminQuizQuestionController;
use App\Http\Controllers\Admin\AdminSeoSpiderController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTrustItemController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CourseCsvImportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Instructor\InstructorAnalyticsController;
use App\Http\Controllers\Instructor\InstructorAssignmentController;
use App\Http\Controllers\Instructor\InstructorCourseController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\InstructorProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\StudentAnalyticsController;
use App\Http\Controllers\Student\StudentAssignmentController;
use App\Http\Controllers\Student\StudentCertificateController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController;
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

    // Centralized Notification Actions
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{id}/go', [NotificationController::class, 'readAndRedirect'])->name('notifications.read-and-redirect');
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

    // Student Certificates
    Route::get('/student/certificates', [StudentCertificateController::class, 'index'])->name('student.certificates.index');
    Route::get('/student/certificates/{certificate}', [StudentCertificateController::class, 'show'])->name('student.certificates.show');
    Route::get('/student/certificates/{certificate}/download', [StudentCertificateController::class, 'download'])->name('student.certificates.download');

    // Student Notifications
    Route::get('/student/notifications', [NotificationController::class, 'studentIndex'])->name('student.notifications.index');

    // Student Profile & Learning Profile
    Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile.show');
    Route::get('/student/profile/edit', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::put('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::delete('/student/profile/avatar', [StudentProfileController::class, 'destroyAvatar'])->name('student.profile.avatar.destroy');
    Route::put('/student/profile/password', [StudentProfileController::class, 'updatePassword'])->name('student.profile.password.update');
    Route::get('/student/learning-profile', [StudentProfileController::class, 'learningProfile'])->name('student.profile.learning');
    Route::get('/student/analytics', [StudentAnalyticsController::class, 'index'])->name('student.analytics.index');
});

Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

    // Instructor Performance Analytics
    Route::get('/analytics', [InstructorAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [InstructorAnalyticsController::class, 'export'])->name('analytics.export');

    // Assigned Course Management & Student Progress
    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [InstructorCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/students', [InstructorCourseController::class, 'students'])->name('courses.students');

    // Assignment Submissions & Grading
    Route::get('/assignments', [InstructorAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}/submissions/{submission}', [InstructorAssignmentController::class, 'submissionShow'])->name('assignments.submissions.show');
    Route::post('/assignments/{assignment}/submissions/{submission}/grade', [InstructorAssignmentController::class, 'grade'])->name('assignments.submissions.grade');
    Route::get('/assignments/{assignment}/submissions/{submission}/files/{file}/download', [InstructorAssignmentController::class, 'downloadFile'])->name('assignments.files.download');

    // Instructor Notifications
    Route::get('/notifications', [NotificationController::class, 'instructorIndex'])->name('notifications.index');

    // Instructor Faculty Profile
    Route::get('/profile', [InstructorProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [InstructorProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [InstructorProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::put('/profile/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Course Management CRUD
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');

    // Course Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Course CSV Import (Full Form Parity)
    Route::get('/courses/import', [CourseCsvImportController::class, 'index'])->name('courses.import.index');
    Route::get('/courses/import/template', [CourseCsvImportController::class, 'downloadTemplate'])->name('courses.import.template');
    Route::get('/courses/import/guide', [CourseCsvImportController::class, 'guide'])->name('courses.import.guide');
    Route::post('/courses/import/preview', [CourseCsvImportController::class, 'preview'])->name('courses.import.preview');
    Route::post('/courses/import/process', [CourseCsvImportController::class, 'process'])->name('courses.import.process');
    // Direct Course Publishing Controls
    Route::patch('/courses/{course}/publish', [AdminCourseController::class, 'publish'])->name('courses.publish');
    Route::patch('/courses/{course}/unpublish', [AdminCourseController::class, 'unpublish'])->name('courses.unpublish');

    Route::get('/courses/{course}/preview', [AdminCourseController::class, 'preview'])->name('courses.preview');
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

    // Centralized Trust & Proof of Work Library
    Route::get('/trust', [AdminTrustItemController::class, 'index'])->name('trust.index');
    Route::get('/trust/create', [AdminTrustItemController::class, 'create'])->name('trust.create');
    Route::post('/trust', [AdminTrustItemController::class, 'store'])->name('trust.store');
    Route::get('/trust/{trustItem}/edit', [AdminTrustItemController::class, 'edit'])->name('trust.edit');
    Route::put('/trust/{trustItem}', [AdminTrustItemController::class, 'update'])->name('trust.update');
    Route::delete('/trust/{trustItem}', [AdminTrustItemController::class, 'destroy'])->name('trust.destroy');
    Route::patch('/trust/{trustItem}/toggle-status', [AdminTrustItemController::class, 'toggleStatus'])->name('trust.toggle-status');

    // Blog CMS Management
    Route::get('/blogs/dashboard', [AdminBlogDashboardController::class, 'index'])->name('blogs.dashboard');
    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [AdminBlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blog}/edit', [AdminBlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
    Route::get('/blogs/{blog}/preview', [AdminBlogController::class, 'preview'])->name('blogs.preview');
    Route::patch('/blogs/{blog}/publish', [AdminBlogController::class, 'publish'])->name('blogs.publish');
    Route::patch('/blogs/{blog}/unpublish', [AdminBlogController::class, 'unpublish'])->name('blogs.unpublish');

    // Blog Categories & Tags
    Route::get('/blogs/categories', [AdminBlogCategoryController::class, 'index'])->name('blogs.categories.index');
    Route::post('/blogs/categories', [AdminBlogCategoryController::class, 'store'])->name('blogs.categories.store');
    Route::put('/blogs/categories/{category}', [AdminBlogCategoryController::class, 'update'])->name('blogs.categories.update');
    Route::delete('/blogs/categories/{category}', [AdminBlogCategoryController::class, 'destroy'])->name('blogs.categories.destroy');

    Route::get('/blogs/tags', [AdminBlogTagController::class, 'index'])->name('blogs.tags.index');
    Route::post('/blogs/tags', [AdminBlogTagController::class, 'store'])->name('blogs.tags.store');
    Route::delete('/blogs/tags/{tag}', [AdminBlogTagController::class, 'destroy'])->name('blogs.tags.destroy');

    // Blog Migration & Legacy Recovery Hub
    Route::get('/blogs/migration', [AdminBlogMigrationController::class, 'index'])->name('blogs.migration.index');
    Route::post('/blogs/migration/start', [AdminBlogMigrationController::class, 'start'])->name('blogs.migration.start');
    Route::get('/blogs/migration/logs', [AdminBlogMigrationController::class, 'logs'])->name('blogs.migration.logs');

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

    // Certificate Registry & Management
    Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
    Route::post('/certificates/{certificate}/revoke', [AdminCertificateController::class, 'revoke'])->name('certificates.revoke');
    Route::get('/certificates/{certificate}/download', [AdminCertificateController::class, 'download'])->name('certificates.download');

    // Instructor Management
    Route::resource('instructors', AdminInstructorController::class);
    Route::patch('/instructors/{instructor}/toggle-status', [AdminInstructorController::class, 'toggleStatus'])->name('instructors.toggle-status');
    Route::post('/instructors/{instructor}/courses', [AdminInstructorController::class, 'assignCourse'])->name('instructors.courses.assign');
    Route::delete('/instructors/{instructor}/courses/{course}', [AdminInstructorController::class, 'unassignCourse'])->name('instructors.courses.unassign');

    // Admin Notifications
    Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications.index');

    // Platform User & Student Management Directory
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Platform Analytics & Reporting Hub
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/revenue', [AdminAnalyticsController::class, 'revenue'])->name('analytics.revenue');
    Route::get('/analytics/enrollments', [AdminAnalyticsController::class, 'enrollments'])->name('analytics.enrollments');
    Route::get('/analytics/courses', [AdminAnalyticsController::class, 'courses'])->name('analytics.courses');
    Route::get('/analytics/students', [AdminAnalyticsController::class, 'students'])->name('analytics.students');
    Route::get('/analytics/instructors', [AdminAnalyticsController::class, 'instructors'])->name('analytics.instructors');
    Route::get('/analytics/export/{type}', [AdminAnalyticsController::class, 'export'])->name('analytics.export');

    // System Settings & Platform Configuration
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // SEO Spider Web & Internal Linking Engine
    Route::get('/internal-linking', [AdminSeoSpiderController::class, 'index'])->name('seo.spider');
    Route::post('/internal-linking/recalculate', [AdminSeoSpiderController::class, 'recalculate'])->name('seo.recalculate');
    Route::post('/internal-linking/overrides', [AdminSeoSpiderController::class, 'storeOverride'])->name('seo.override.store');
    Route::delete('/internal-linking/overrides/{override}', [AdminSeoSpiderController::class, 'destroyOverride'])->name('seo.override.destroy');

    // Centralized Form Submissions Management
    Route::get('/form-submissions', [AdminFormSubmissionController::class, 'index'])->name('form-submissions.index');
    Route::get('/form-submissions/export', [AdminFormSubmissionController::class, 'export'])->name('form-submissions.export');
    Route::get('/form-submissions/{formSubmission}', [AdminFormSubmissionController::class, 'show'])->name('form-submissions.show');
    Route::patch('/form-submissions/{formSubmission}/status', [AdminFormSubmissionController::class, 'updateStatus'])->name('form-submissions.status');
    Route::patch('/form-submissions/{formSubmission}/notes', [AdminFormSubmissionController::class, 'updateNotes'])->name('form-submissions.notes');
    Route::delete('/form-submissions/{formSubmission}', [AdminFormSubmissionController::class, 'destroy'])->name('form-submissions.destroy');
});
