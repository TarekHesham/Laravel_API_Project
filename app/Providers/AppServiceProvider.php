<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Users\Comment;
use App\Models\Users\Application;
use App\Policies\ApplicationPolicy;
use App\Policies\JobPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        // Add polices
        Gate::define(User::class, JobPolicy::class);
        Gate::define(Comment::class, CommentPolicy::class);
        Gate::define(Application::class, ApplicationPolicy::class);
    }
}
