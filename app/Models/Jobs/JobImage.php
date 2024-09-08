<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobImage extends Model
{
    use HasFactory;
    protected $table = 'job_images';
    public $timestamps = false;
    protected $fillable = ['image', 'job_listing_id'];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_listing_id', 'id');
    }
}
