<?php

namespace App\Models\Dependency;

use App\Models\Jobs\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;
    protected $table = "locations";

    public function job()
    {
        return $this->belongsToMany(Job::class, 'location_id');
    }
}
