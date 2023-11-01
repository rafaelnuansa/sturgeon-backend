<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/verification-error');
        }

        $user->verification_token = null;
        $user->email_verified_at = now(); // Mark the email as verified
        $user->save();

        // Redirect to a success page or a login page
        return redirect('/verification-success');
    }
}
