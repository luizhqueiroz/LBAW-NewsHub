<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['news' => function($query) {
            $query->withCount(['likes', 'comments']);
             }])
            ->withCount(['followers', 'followings'])
            ->findOrFail($id);

        if (Auth::check()) {
            $user->isBeingFollowed = Auth::user()->followings()->where('followed_id', $id)->exists();
        } else {
            $user->isBeingFollowed = false;
        }

        return view('pages.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        return view('pages.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_name' => 'required|string|max:30', 
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, 
            'password' => 'nullable|string|min:8|confirmed', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        $user->user_name = $request->input('user_name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->user_password = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/avatars'), $fileName);
    
            // Save the image in the images table
            $image = new Image();
            $image->image_path = 'images/avatars/' . $fileName;
            $image->save();
    
            // Associate the image with the user
            $user->image_id = $image->id;
        }

        $user->save();

        return redirect()->route('user.show', $user->id)->with('success', 'Profile updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        try {
            $this->authorize('delete', $user);

            $user->delete();

            return redirect()->route('home')->with('success', 'User deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}