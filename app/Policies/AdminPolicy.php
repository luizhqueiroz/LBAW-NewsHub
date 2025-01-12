<?php

namespace App\Policies;

use App\Models\Administrator;

class AdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update($user, Administrator $admin)
    {   
        return $user instanceof Administrator && $admin->id === $user->id;
    }
}
