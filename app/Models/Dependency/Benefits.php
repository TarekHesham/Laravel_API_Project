<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefits extends Model
{
    use HasFactory;
    protected $table = "benefits";
    protected $fillable = ['name'];
    
    public function job()
    {
        return $this->belongsToMany(Job::class, 'job_benefits', 'benefit_id', 'job_listing_id');
    }
}
