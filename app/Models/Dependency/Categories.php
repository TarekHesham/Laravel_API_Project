<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use App\Models\Jobs\JobCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categories extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $fillable = ['name'];
    
    public function jobListings()
    {
        return $this->belongsToMany(Job::class, 'job_categories', 'category_id', 'job_listing_id');
    }
}
