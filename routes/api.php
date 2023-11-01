<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//route login
Route::post('login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);
Route::post('register', [App\Http\Controllers\Api\Auth\RegisterController::class, 'index']);

Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Api\Auth\VerificationController::class, 'verify'])
                ->middleware(['auth:api',  'throttle:6,1'])
                ->name('verification.verify');
Route::post('email/resend', [App\Http\Controllers\Api\Auth\VerificationController::class, 'resend'])->name('verification.resend')->middleware('auth:api');

Route::post('password/forgot', [App\Http\Controllers\Api\Auth\PasswordResetController::class, 'forgotPassword'])->name('password.email');
Route::post('password/reset', [App\Http\Controllers\Api\Auth\PasswordResetController::class, 'resetPassword'])->name('password.store');


Route::post('/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function() {
//logout
Route::get('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);

});


Route::group(['middleware' => 'auth:api', 'verifiedUser'], function () {
Route::post('/threads/image/upload', [App\Http\Controllers\Api\UploadController::class, 'store']);
Route::get('/threads', [App\Http\Controllers\Api\Public\ThreadController::class, 'index']);
Route::post('/threads', [App\Http\Controllers\Api\Public\ThreadController::class, 'store']);

Route::patch('/threads/{thread}', [App\Http\Controllers\Api\Public\ThreadController::class, 'update']);
Route::get('/threads/{thread}', [App\Http\Controllers\Api\Public\ThreadController::class, 'show']);
Route::delete('/threads/{thread}', [App\Http\Controllers\Api\Public\ThreadController::class, 'destroy']);

Route::get('/profile', [App\Http\Controllers\Api\Public\ProfileController::class, 'index']);
Route::patch('/profile', [App\Http\Controllers\Api\Public\ProfileController::class, 'update']);
Route::patch('/profile/change-avatar', [App\Http\Controllers\Api\Public\ProfileController::class, 'change_avatar']);
Route::patch('/profile/change-bio', [App\Http\Controllers\Api\Public\ProfileController::class, 'change_bio']);
Route::patch('/profile/change-password', [App\Http\Controllers\Api\Public\ProfileController::class, 'change_password']);

Route::get('/scientific-works', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'index']);
Route::post('/scientific-works', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'store']);
Route::patch('/scientific-works/{scientificwork}', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'update']);
Route::get('/scientific-works/{scientificwork}', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'show']);
Route::delete('/scientific-works/{scientificwork}', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'delete']);
});

Route::prefix('admin')->group(function () {
    //group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {
        Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class);

    });
});


Route::prefix('public')->group(function () {
    Route::get('threads', [App\Http\Controllers\Api\Public\ThreadController::class, 'index']);
    Route::get('threads/category', [App\Http\Controllers\Api\Public\ThreadController::class, 'categories']);
    Route::get('threads/{username}', [App\Http\Controllers\Api\Public\UserController::class, 'threads']);
    Route::get('threads/{slug}', [App\Http\Controllers\Api\Public\ThreadController::class, 'show']);
    Route::get('scientific-works', [App\Http\Controllers\Api\Public\ScientificWorkController::class, 'homepage']);
    Route::get('threads-home', [App\Http\Controllers\Api\Public\ThreadController::class, 'homepage']);
    Route::get('users', [App\Http\Controllers\Api\Public\UserController::class, 'index']);
    Route::get('users/{username}', [App\Http\Controllers\Api\Public\UserController::class, 'username']);
});
