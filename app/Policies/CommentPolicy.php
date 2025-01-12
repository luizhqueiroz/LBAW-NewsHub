<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\Administrator;

class CommentPolicy 
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update($user, Comment $comment)
    {
        return ($user instanceof User && $user->id === $comment->author_id) || ($user instanceof Administrator);
    }

    public function destroy($user, Comment $comment)
    {
        return ($user instanceof User && $user->id === $comment->author_id) || ($user instanceof Administrator);
    }
}
