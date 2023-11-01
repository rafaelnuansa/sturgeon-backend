<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
            'success' => true,
                'message' => 'Password reset link sent to your email.'
            ], 200)
            : response()->json([

            'success' => true,
            'message' => 'Unable to send password reset link.'
            ], 400);
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status == Password::PASSWORD_RESET
        ? response()->json([
            'success' => true,
            'message' => 'Password has been reset.'
        ], 200)
        : response()->json([
            'success' => false,
            'message' => 'Unable to reset the password.'
        ], 400);
}
}
