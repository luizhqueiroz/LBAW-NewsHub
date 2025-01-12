<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use App\Models\Image;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{   
    
    public function show(String $id) 
    {
        $admin = Administrator::findOrfail($id);

        return view('pages.admins.show', ['admin' => $admin]);
    }

    public function edit(String $id) 
    {
        $admin = Administrator::findOrfail($id);

        return view('pages.admins.edit', ['admin' => $admin]);
    }

    public function update(Request $request, String $id) 
    {
        $admin = Administrator::findOrfail($id);

        $this->authorize('update', $admin);

        $request->validate([
            'adm_name' => 'required|string|max:30', 
            'email' => 'required|string|email|max:255|unique:administrator,email,' . $admin->id, 
            'password' => 'nullable|string|min:8|confirmed', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);

        $admin->adm_name = $request->input('adm_name');
        $admin->email = $request->input('email');

        if ($request->filled('password')) {
            $admin->adm_password = bcrypt($request->password);
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
            $admin->image_id = $image->id;
        }

        $admin->save();

        return redirect()->route('admin.show', $admin->id)->with('success', 'Profile updated successfully');
    }

    public function showUsers()
    {
        return view('pages.admins.users', ['users' => User::orderBy('user_name')->get()]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        User::create([
            'user_name' => $request->name,
            'email' => $request->email,
            'user_password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show');
    }

    public function deleteUser(String $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.show')->with('error', 'User not found.');
        }

        try {
            $user->delete();

            return redirect()->route('admin.users.show')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.show')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function blockUser(String $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.show')->with('error', 'User not found.');
        }

        $user->blocked()->create([
            'user_id' => $user->id,
            'blocked_date' => now(),
            'appeal' => false,
        ]);

        return redirect()->route('admin.users.show')->with('success', 'User blocked successfully.');
    }

    public function unblockUser(String $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.show')->with('error', 'User not found.');
        }

        $user->blocked()->delete();

        return redirect()->route('admin.users.show')->with('success', 'User unblocked successfully.');
    }
}
