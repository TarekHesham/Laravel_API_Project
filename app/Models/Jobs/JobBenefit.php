<?php

namespace App\Models\Jobs;

use App\Models\Dependency\Benefits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobBenefit extends Model
{
    use HasFactory;
    protected $table = "job_benefits";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_listing_id',
        'benefit_id',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function benefit(): BelongsTo
    {
        return $this->BelongsTo(Benefits::class, 'benefit_id');
    }
}
