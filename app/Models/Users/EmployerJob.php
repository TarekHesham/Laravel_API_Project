<?php

namespace App\Models\Users;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerJob extends Model
{
    use HasFactory;
    protected $table = "employer_jobs";
    protected $fillable = [
        'status',
        'employer_id',
        'job_listing_id',
        'created_at',
        'updated_at'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_listing_id', 'id');
    }
}
