<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordResetToken;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        // Validate the token and email
        $resetToken = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid or expired token.']);
        }

        return view('auth.password_reset', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle the password reset request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validate the token and email
        $resetToken = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return redirect()->back()->withErrors(['error' => 'Invalid or expired token.']);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }

        $user->user_password = Hash::make($request->password);
        $user->save();

        // Delete the reset token after successful reset
        $resetToken->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully.');
    }
}
