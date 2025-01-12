<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // The table associated with the model.
    protected $table = 'tag';

    // The attributes that are mass assignable.
    protected $fillable = ['name'];

    // Disable the default timestamps (created_at and updated_at).
    public $timestamps = false;

    /**
     * The news articles associated with the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function news()
    {
        return $this->belongsToMany(News::class, 'tag_news', 'tag_id', 'news_id');
    }

    /**
     * The users following the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'follow_tag', 'tag_id', 'user_id');
    }

}
