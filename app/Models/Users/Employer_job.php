<?php

namespace App\Models\Users;

use App\Models\Jobs\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employer_job extends Model
{
    use HasFactory;
    protected $table = "employer_jobs";
    protected $fillable = [
        'status',
        'employer_id',
        'job_id',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }
}
