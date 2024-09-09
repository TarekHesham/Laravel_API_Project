<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = "locations";
    protected $timestamps = false;
    protected $fillable = ['name'];
    
    public function job()
    {
        return $this->belongsToMany(Job::class, 'location_id');
    }
}
