<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate a reset token
        $token = Str::random(64);

        // Create a password reset record in the database
        DB::table('password_resets')->insert([
            'email' => $request->input('email'),
            'token' => $token,
            'created_at' => now(),
        ]);

        // Send an email with the reset link
        $user = User::where('email', $request->input('email'))->first();
        $resetLink = route('password.reset', $token); // You will need to define the 'password.reset' route in your routes/web.php file.
        Mail::to($user->email)->send(new ResetPasswordEmail($resetLink));
        // Return a response indicating that the reset email has been sent
        return response()->json(['message' => 'Password reset email sent', 200]);
    }
}
