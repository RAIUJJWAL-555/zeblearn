<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/courses/sap-fico-training', 'pages.courses.sap-fico')->name('courses.fico');
Route::view('/blogs', 'pages.blog.index')->name('blogs');
Route::view('/blogs/top-50-sap-ewm-interview-question-answer', 'pages.blog.ewm-interview-questions')->name('blogs.ewm');
Route::view('/about', 'pages.about')->name('about');
Route::view('/book-demo', 'pages.book-demo')->name('book-demo');
Route::view('/book-demo/thank-you', 'pages.book-demo-thank-you')->name('book-demo.thanks');
Route::view('/contact', 'pages.contact')->name('contact');
