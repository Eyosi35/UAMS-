<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/enrollments', [
        EnrollmentController::class,
        'store',
    ]);
});