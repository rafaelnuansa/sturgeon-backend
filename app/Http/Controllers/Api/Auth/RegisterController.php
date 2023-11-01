<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:255',
            'username' => 'required|unique:users|min:5|max:12',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return new ApiResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        // Membuat user baru
        $user = new User();
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));


        $user->save();

        // Send the email verification notification
        $user->sendEmailVerificationNotification();

        // Event for registering
        event(new Registered($user));


        // Response berhasil mendaftar
        return new ApiResource(true, 'Registrasi berhasil', $user);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return new ApiResource(true, 'Email verifikasi telah dikirim ulang.', 200);
        } else {
            return new ApiResource(false, 'Email sudah diverifikasi atau pengguna tidak ditemukan.', null, 422);
        }
    }
}
