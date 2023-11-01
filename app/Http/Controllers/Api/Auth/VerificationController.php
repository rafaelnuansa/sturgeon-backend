<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function verify(Request $request, EmailVerificationRequest $emailVerificationRequest)
    {
        $user = auth('api')->user(); // Use the 'api' guard for API authentication

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah diverifikasi.'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user)); // Fire the event

            return response()->json([
                
                'success' => true,
                'message' => 'Email berhasil diverifikasi.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal verifikasi email.'], 400);
    }

    public function resend(Request $request)
    {
        $user = auth('api')->user(); 
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email anda sudah diverifikasi.'
            ], 200);
        }
        $user->sendEmailVerificationNotification();
        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang silahkan cek email.'
        ], 200);
    }
}
