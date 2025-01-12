<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['image_path'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function administrators()
    {
        return $this->hasMany(Administrator::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
