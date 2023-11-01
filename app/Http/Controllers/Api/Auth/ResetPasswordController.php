<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Find the user with the provided email
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the token exists in the password reset table
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->input('email'))
            ->where('token', $request->input('token'))
            ->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Remove the password reset token from the table
        DB::table('password_resets')->where('email', $request->input('email'))->delete();

        // Send a password reset confirmation email (optional)
        // You can use the same ResetPasswordEmail class as before

        // Return a success response
        return response()->json(['message' => 'Password reset successful'], 200);
    }
}
