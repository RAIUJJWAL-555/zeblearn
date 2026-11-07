<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StitchController;

Route::prefix('stitch')->group(function () {
    Route::get('projects', [StitchController::class, 'index']);
    Route::post('projects', [StitchController::class, 'store']);
    Route::post('upload', [StitchController::class, 'uploadDesign']);
    Route::post('generate', [StitchController::class, 'generateScreen']);
    Route::post('download', [StitchController::class, 'downloadFile']);
});
