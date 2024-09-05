<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobBenefit extends Model
{
    use HasFactory;
    protected $table = "job_benefits";

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function benefit(): BelongsTo
    {
        return $this->BelongsTo(Benefits::class, 'benefit_id');
    }
}
