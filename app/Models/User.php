<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['user_name', 'email', 'user_password', 'reputation', 'image_id'];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_password',
     // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    //  'email_verified_at' => 'datetime',
        'user_password' => 'hashed',
    ];

    public function getNameAttribute()
    {
        return $this->user_name;
    }
    
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function influencer()
    {
        return $this->hasOne(Influencer::class);
    }

    public function blocked()
    {
        return $this->hasOne(Blocked::class, 'user_id');
    }

    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follow', 'followed_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follow', 'follower_id', 'followed_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'sender_id');
    }

    public function followedTags()
    {
        return $this->belongsToMany(Tag::class, 'follow_tag', 'user_id', 'tag_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('viewed', false);
    }
}