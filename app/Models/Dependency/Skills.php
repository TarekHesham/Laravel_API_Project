<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use App\Models\Jobs\JobBenefit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skills extends Model
{
    use HasFactory;
    protected $table = "skills";
    protected $fillable = ['name'];
    
    public function jobListings()
    {
        return $this->belongsToMany(Job::class, 'job_skills', 'skill_id', 'job_listing_id');
    }
}
