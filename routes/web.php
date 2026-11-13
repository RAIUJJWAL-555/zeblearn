<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/sap-fico-training', [CourseController::class, 'fico'])->name('courses.fico');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::view('/blogs', 'pages.blog.index')->name('blogs');
Route::view('/blogs/top-50-sap-ewm-interview-question-answer', 'pages.blog.ewm-interview-questions')->name('blogs.ewm');
Route::view('/about', 'pages.about')->name('about');
Route::view('/book-demo', 'pages.book-demo')->name('book-demo');
Route::view('/book-demo/thank-you', 'pages.book-demo-thank-you')->name('book-demo.thanks');
Route::view('/contact', 'pages.contact')->name('contact');

// Razorpay Asynchronous Payment Webhook
Route::post('/webhooks/razorpay', [PaymentWebhookController::class, 'handle'])->name('webhooks.razorpay');

require __DIR__.'/auth.php';
