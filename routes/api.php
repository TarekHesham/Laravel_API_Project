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
    Route::resource('/jobs', JobController::class);
    // comments
    Route::resource('/comments', CommentController::class);
    // employer
    Route::post('/employer/{job}/cancel', [EmployerJobController::class, 'cancelJob']);
    Route::get('/employer/jobs', [EmployerJobController::class, 'index']);
});

// TEST TOKENS
// 1|zKwf31BHjNFW682NiUi9GkCo8IjB19YJTkXdDN8s23b0db52 -> employer
// 2|zqoUZkHQOnWx4PTJa510Wi5dESroVlOeRBHzFhJw10e8639b -> 
// 4|3Xr7YaMheGk2uLdemEeLRJiTZUcVR7gPegaVlYq854911c21 -> admin