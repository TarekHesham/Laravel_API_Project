<?php

namespace App\Models\Dependency;

use App\Models\Jobs\JobBenefit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skills extends Model
{
    use HasFactory;
    protected $table = "skills";

    function benefit(): BelongsTo
    {
        return $this->belongsTo(JobBenefit::class, 'skill_id');
    }
}
