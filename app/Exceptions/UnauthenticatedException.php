<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthenticatedException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // TODO: Anda dapat menambahkan log atau pelaporan ke layanan pelaporan kesalahan di sini jika diperlukan.
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'error' => 'Unauthenticated',
            'message' => 'Authentication failed. Please log in or provide valid credentials.',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
