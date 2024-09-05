<?php

namespace App\Models\Dependency;

use App\Models\Jobs\JobCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categories extends Model
{
    use HasFactory;
    protected $table = "categories";

    function benefit(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }
}