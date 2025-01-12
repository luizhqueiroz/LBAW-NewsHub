<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use App\Models\Administrator;

class NewsPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update($user, News $news): bool
    {   

        return ($user instanceof User && $user->id === $news->author_id) || ($user instanceof Administrator);
    }

    public function delete($user, News $news): bool
    {
        return ($user instanceof User && $user->id === $news->author_id) || ($user instanceof Administrator);
    }
}
