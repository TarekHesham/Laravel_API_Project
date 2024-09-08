<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CVApplication extends Model
{
    use HasFactory;
    protected $table = 'cv_application';
    protected $fillable = ['cv', 'application_id'];

    public function application()
    {
        return $this->belongsTo(application::class, 'application_id', 'id');
    }
}
