<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_id';
    protected $table = 'influencer';
    protected $fillable = ['user_id', 'started_date', 'has_privilege'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
