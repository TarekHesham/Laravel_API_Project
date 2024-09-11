<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\Jobs\JobController;
use App\Http\Controllers\Jobs\CommentController;
use App\Http\Controllers\Jobs\SearchController;
use App\Models\Users\EmployerJob;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // user data
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/logout', [AuthController::class, 'logout']);

    // Jobs
    Route::post('/jobs/{job}', [JobController::class, 'update']);
    Route::apiResource('/jobs', JobController::class);
    Route::get('/job/{slug}/applications', [EmployerJobController::class, 'applicationsOnJob'])->where('slug', '[a-z0-9-]+');
    Route::get('/job/{slug}', [JobController::class, 'showBySlug'])->where('slug', '[a-z0-9-]+');
    Route::get('/search/autocomplete', [SearchController::class, 'autocomplete']);
    Route::get('/search', [SearchController::class, 'search']);

    // comments
    Route::apiResource('/comments', CommentController::class);

    // employer
    Route::post('/employer/{job}/cancel', [EmployerJobController::class, 'cancelJob']);
    Route::get('/employer/jobs', [EmployerJobController::class, 'index']);

    // Deprecated
    Route::get('/locations', [SearchController::class, 'locations']);
    Route::get('/categories', [SearchController::class, 'categories']);
    Route::get('/skills', [SearchController::class, 'skills']);
    Route::get('/benefits', [SearchController::class, 'benefits']);



    // Admin
    Route::put('/jobs/{job}/status', [JobController::class, 'acceptReject']);
    
    // application
    Route::put('/application/{application}/accept', [EmployerJobController::class, 'acceptApplication']);
    Route::put('/application/{application}/reject', [EmployerJobController::class, 'rejectApplication']);
    Route::apiResource('/application', ApplicationController::class)->except('update');
});

// TEST Accounts
// emp@emp.com -> employer
// can@can.com -> candidate
// admin@admin.com -> admin
// password for all accounts: 123456789
