<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    use HasFactory;
    protected $table = "skills";
    public $timestamps = false;
    protected $fillable = ['name'];
    
    public function jobListings()
    {
        return $this->belongsToMany(Job::class, 'job_skills', 'skill_id', 'job_listing_id');
    }
}
