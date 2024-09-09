<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $table = "categories";
    protected $timestamps = false;
    protected $fillable = ['name'];
    
    public function jobListings()
    {
        return $this->belongsToMany(Job::class, 'job_categories', 'category_id', 'job_listing_id');
    }
}
