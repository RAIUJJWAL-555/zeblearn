<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PublicCertificateController;
use App\Http\Controllers\SitemapController;
use App\Services\CourseCmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/search/api', [CourseController::class, 'searchApi'])->name('courses.search.api');
Route::get('/courses/sap-fico-training', [CourseController::class, 'fico'])->name('courses.fico');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])
    ->name('courses.show')
    ->missing(function (Request $request) {
        $slug = trim((string) ($request->route('course') ?? basename($request->path())), "/ \t\n\r\0\x0B");
        $newSlug = app(CourseCmsService::class)->resolveRedirect($slug);

        if ($newSlug && trim($newSlug, '/') !== $slug) {
            return redirect()->to(url('/courses/'.trim($newSlug, '/')), 301);
        }

        abort(404);
    });
Route::post('/courses/{course:slug}/reviews', [CourseController::class, 'storeReview'])->name('courses.reviews.store');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::view('/blogs/top-50-sap-ewm-interview-question-answer', 'pages.blog.ewm-interview-questions')->name('blogs.ewm');
Route::get('/blogs/{slug}', [BlogController::class, 'showByBlogPrefix'])->name('blogs.show');

// Category Archives (e.g. /category/sap-blogs/ and paginated)
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blogs.category');
Route::get('/category/{slug}/page/{page}', [BlogController::class, 'category'])->name('blogs.category.page');

// Tag Archives (e.g. /tag/sap-fico/ and paginated)
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blogs.tag');
Route::get('/tag/{slug}/page/{page}', [BlogController::class, 'tag'])->name('blogs.tag.page');

Route::view('/about', 'pages.about')->name('about');
Route::view('/book-demo', 'pages.book-demo')->name('book-demo');
Route::view('/book-demo/thank-you', 'pages.book-demo-thank-you')->name('book-demo.thanks');
Route::view('/contact', 'pages.contact')->name('contact');

// Public Certificate Verification
Route::get('/verify-certificate/{certificate_number?}', [PublicCertificateController::class, 'verify'])->name('certificates.verify');

// Razorpay Asynchronous Payment Webhook
Route::post('/webhooks/razorpay', [PaymentWebhookController::class, 'handle'])->name('webhooks.razorpay');

// Inbound Lead Attribution Webhook / API
Route::post('/api/leads', [LeadController::class, 'store'])->name('leads.store');

// Centralized Form Submissions (Public endpoints)
Route::post('/forms/submit', [FormSubmissionController::class, 'store'])->name('forms.submit');
Route::post('/api/form-submissions', [FormSubmissionController::class, 'store'])->name('api.forms.submit');

require __DIR__.'/auth.php';

// Category-based Course Landing Page Route (e.g. /sap-functional/sap-fico-on-s-4-hana/)
Route::get('/{category}/{course}', [CourseController::class, 'showByCategory'])
    ->where('category', '^(?!admin|instructor|student|learning|checkout|verify-certificate|webhooks|forms|form-submissions|blogs|category|tag|about|contact|book-demo|sitemap.xml|login|register|logout|forgot-password|reset-password).*$')
    ->name('courses.category.show');

// Exact Legacy WordPress URL fallback (e.g. /sap-sd-power-user-certificate/)
Route::get('/{slug}', [BlogController::class, 'show'])->name('blogs.legacy');
