<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Jobs\Job;
use App\Models\User;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'candidate_id', 'job_id', 'status'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id', 'id');
    }

    
    // Relation with cv
    public function cv(): BelongsTo
    {
        return $this->belongsTo(CVApplication::class, 'id', 'application_id');
    }

    // Relation with form
    public function form(): BelongsTo
    {
        return $this->belongsTo(FormApplication::class, 'id', 'application_id');
    }
}
