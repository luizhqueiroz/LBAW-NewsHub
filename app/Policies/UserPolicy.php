<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Administrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update($user, User $targetUser)
    {
        return ($user instanceof User && $user->id === $targetUser->id) || ($user instanceof Administrator);
    }

    public function delete($user, User $targetUser)
    {
        return ($user->id === $targetUser->id) || ($user instanceof Administrator);
    }

    public function follow(User $user, User $targetUser)
    {
        if ($user->id === $targetUser->id) {
            return Response::deny('You cannot follow yourself.');
        }

        if ($user->blocked || $targetUser->blocked) {
            return Response::deny('You cannot follow this user.');
        }

        return true;
    }

    public function unfollow(User $user, User $targetUser)
    {
        if (!$user->followings()->where('followed_id', $targetUser->id)->exists()) {
            return Response::deny('You are not following this user.');
        }

        return true;
    }
}
