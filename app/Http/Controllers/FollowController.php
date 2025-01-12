<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Events\FollowNotification;

class FollowController extends Controller
{
    public function followUser(User $user)
    {
        try {
            $this->authorize('follow', $user);

            $authUser = auth()->user();

            $authUser->followings()->attach($user->id);

            event(new FollowNotification($authUser, $user));

            return response()->json(['success' => true,
                'unfollow_url' => route('users.unfollow', $user->id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function unfollowUser(User $user)
    {
        try {
            $this->authorize('unfollow', $user);

            $authUser = auth()->user();

            $authUser->followings()->detach($user->id);
            return response()->json(['success' => true, 
                'follow_url' => route('users.follow', $user->id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function showFollows($id)
    {   
        $user = User::findOrFail($id);

        $followers = $user->followers()->paginate(10);
        $followings = $user->followings()->paginate(10);

        return view('pages.follows.show', compact('user', 'followers', 'followings'));
    }
}
