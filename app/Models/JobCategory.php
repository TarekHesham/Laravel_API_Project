<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCategory extends Model
{
    use HasFactory;
    protected $table = "job_category";

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function category(): BelongsTo
    {
        return $this->BelongsTo(Categories::class, 'category_id');
    }
}
