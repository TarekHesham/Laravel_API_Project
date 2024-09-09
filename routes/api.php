<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\Jobs\JobController;
use App\Http\Controllers\Jobs\CommentController;
use App\Http\Controllers\Jobs\SearchController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // user data
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);

    // Jobs
    Route::apiResource('/jobs', JobController::class);
    Route::get('/search', [SearchController::class, 'search']);

    // comments
    Route::apiResource('/comments', CommentController::class);

    // employer
    Route::post('/employer/{job}/cancel', [EmployerJobController::class, 'cancelJob']);
    Route::get('/employer/jobs', [EmployerJobController::class, 'index']);

    // Admin
    Route::put('/jobs/{job}/status', [JobController::class, 'acceptReject']);

    // application
    Route::apiResource('/application', ApplicationController::class)->except('update');
});

// TEST Accounts
// emp@emp.com -> employer
// can@can.com -> candidate
// admin@admin.com -> admin
// password for all accounts: 123456789
