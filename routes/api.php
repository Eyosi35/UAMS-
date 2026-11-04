<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/admin/enrollments', [EnrollmentController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:lecturer'])->group(function () {
    // Lecturer routes will go here.
});

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    // Student routes will go here.
});