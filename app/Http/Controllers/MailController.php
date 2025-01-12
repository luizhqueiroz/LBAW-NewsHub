<?php

namespace App\Http\Controllers;

use Mail;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use App\Mail\MailModel;
use TransportException;
use Exception;

use App\Models\User;

class MailController extends Controller
{
    function send(Request $request) {

        $missingVariables = [];
        $requiredEnvVariables = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ];
    
        foreach ($requiredEnvVariables as $envVar) {
            if (empty(env($envVar))) {
                $missingVariables[] = $envVar;
            }
        }
        // Query the user by email
        $user = User::where('email', $request->email)->first();
        $redirect_page='login';
        
        if ($user==null) {
            $redirect_page = 'recover';
            $error = 'The email address is not valid. Please try again.';
            $request->session()->flash('error', $error);
            return redirect()->route($redirect_page);
        }
            // Generate or update the password reset token for the user
            $token = Str::random(60);
            PasswordResetToken::updateOrCreate(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
    
            // Create the password reset link
            $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
    
        
        if (empty($missingVariables)) {

            $mailData = [
                'email' => $user->email,
                'name' => $user->user_name, // Use the `user_name` field from the model
                'link' => $resetLink,
            ];

            try {
                Mail::to($request->email)->send(new MailModel($mailData));
                $status = 'Success!';
                $message = $request->name . ', an email has been sent to ' . $request->email;
            } catch (TransportException $e) {
                $status = 'Error!';
                $message = 'SMTP connection error occurred during the email sending process to ' . $request->email;
            } catch (Exception $e) {
                $status = 'Error!';
                $message = 'An unhandled exception occurred during the email sending process to ' . $request->email;
            }

        } else {
            $status = 'Error!';
            $message = 'The SMTP server cannot be reached due to missing environment variables:';
        }

        $request->session()->flash('status', $status);
        $request->session()->flash('message', $message);
        $request->session()->flash('details', $missingVariables);
        return redirect()->route($redirect_page);
    }

    function showRecoverForm() {
        return view('auth.recover');
    }
}
