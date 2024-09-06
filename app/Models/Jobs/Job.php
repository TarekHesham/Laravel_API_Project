<?php

namespace App\Models\Jobs;

use App\Models\Dependency\Location;
use App\Models\Users\Comment;
use App\Models\User;
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

    function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'job_id');
    }

    function benefits(): HasMany
    {
        return $this->hasMany(JobBenefit::class, 'job_listing_id');
    }

    function skills(): HasMany
    {
        return $this->hasMany(JobSkill::class, 'job_listing_id');
    }

    function categories(): HasMany
    {
        return $this->hasMany(JobCategory::class, 'job_listing_id');
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
