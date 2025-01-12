<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'password_reset_tokens';

    // Primary key is not "id"
    protected $primaryKey = 'email';

    // Disable auto-increment for primary key
    public $incrementing = false;

    // Specify the primary key data type
    protected $keyType = 'string';

    // Fields that are mass assignable
    protected $fillable = ['email', 'token', 'created_at'];

    // Disable timestamps if they are not used
    public $timestamps = false;
}
