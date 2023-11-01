<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        // Set validasi
        $validator = Validator::make($request->all(), [
            'email'    => 'required', // Kolom "email" untuk menerima email atau username
            'password' => 'required',
        ]);

        // Response error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loginField = $request->input('email');

        // Cek apakah email/username dan password sesuai
        if (!$token = auth()->guard('api')->attempt(['email' => $loginField, 'password' => $request->input('password')])) {
            // Cek apakah pengguna mencoba dengan username
            if (!$token = auth()->guard('api')->attempt(['username' => $loginField, 'password' => $request->input('password')])) {
                // Response login "failed"
                return response()->json([
                    'success' => false,
                    'message' => 'Email or Username or Password is incorrect'
                ], 400);
            }
        }
        //

        // Response login "success" dengan Token yang dibuat
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user()->only(['name', 'email']),
            'token' => $token
        ], 200);
    }


    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        //remove "token" JWT
        JWTAuth::invalidate(JWTAuth::getToken());

        //response "success" logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}
