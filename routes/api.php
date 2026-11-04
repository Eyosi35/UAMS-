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
    Route::get('/admin/enrollments', [EnrollmentController::class, 'index']);
    Route::delete('/admin/enrollments/{uuid}', [EnrollmentController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:lecturer'])->group(function () {
});

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
});