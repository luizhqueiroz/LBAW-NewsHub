<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetToken;
use Illuminate\Http\Request;

class PasswordResetTokenController extends Controller
{
    /**
     * Store a newly created token in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure the email exists in the 'users' table
            'token' => 'required|string',
        ]);

        // If the token for this email already exists, it will be updated; otherwise, it will be created
        PasswordResetToken::updateOrCreate(
            ['email' => $request->email], // Condition for finding the record
            ['token' => $request->token, 'created_at' => now()] // Data to insert or update
        );
    }

}
