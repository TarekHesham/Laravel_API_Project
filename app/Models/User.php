<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Jobs\Job;
use App\Models\Users\Application;
use App\Models\Users\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isEmployer()
    {
        return $this->role === 'employer';
    }

    public function isCandidate()
    {
        return $this->role === 'candidate';
    }

    function jobs(): HasMany
    {
        if ($this->isEmployer()) {
            return $this->hasMany(Job::class, 'user_id');
        }
    }
    function applications(): HasMany
    {
        if ($this->isCandidate()) {
            return $this->hasMany(Application::class, 'candidate_id');
        }
    }
    function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
