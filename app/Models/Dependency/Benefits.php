<?php

namespace App\Models\Dependency;

use App\Models\Jobs\JobBenefit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Benefits extends Model
{
    use HasFactory;
    protected $table = "benefits";

    function benefit(): BelongsTo
    {
        return $this->belongsTo(JobBenefit::class, 'benefit_id');
    }
}
