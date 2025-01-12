<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrator extends Authenticatable
{
    protected $table = 'administrator';
    protected $fillable = ['adm_name', 'email', 'adm_password', 'image_id'];
    public $timestamps = false;
    
    protected $hidden = [
        'adm_password',
    ];

    protected $casts = [
        'adm_password' => 'hashed',
    ];

    public function getNameAttribute()
    {
        return $this->adm_name;
    }

    public function getAuthPassword()
    {
        return $this->adm_password;
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}