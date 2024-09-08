<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CVApplication extends Model
{
    use HasFactory;
    protected $table = 'cv_application';
    protected $fillable = ['cv', 'application_id'];
}
