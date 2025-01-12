<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagNews extends Model
{
    use HasFactory;
    protected $table = 'tag_news';
    public $timestamps = false;
}
