<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\Jobs\JobController;
use App\Http\Controllers\Jobs\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // user data
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);

    // Jobs
    Route::apiResource('/jobs', JobController::class);
    // comments
    Route::apiResource('/comments', CommentController::class);
    // employer
    Route::post('/employer/{job}/cancel', [EmployerJobController::class, 'cancelJob']);
    Route::get('/employer/jobs', [EmployerJobController::class, 'index']);
});

// TEST Accounts
// test@test.com -> employer
// test2@test2.com -> candidate
// admin@admin.com -> admin
// password for all accounts: 123456789
