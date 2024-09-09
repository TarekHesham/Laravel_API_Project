<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormApplication extends Model
{
    use HasFactory;
    protected $table = 'form_application';
    protected $fillable = ['name', 'email', 'phone_number', 'application_id'];

    public function application()
    {
        return $this->belongsTo(application::class, 'application_id', 'id');
    }
}
