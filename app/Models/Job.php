<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Job extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'job_listings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_title',
        'description',
        'experience_level',
        'salary_from',
        'salary_to',
        'work_type',
        'deadline',
        'location_id',
        'employer_id'
    ];

    function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'job_id');
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'job_title'
            ]
        ];
    }
}
