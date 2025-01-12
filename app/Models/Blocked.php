<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blocked extends Model
{
    protected $table = 'blocked';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id', 'blocked_date', 'appeal', 'appeal_description'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
