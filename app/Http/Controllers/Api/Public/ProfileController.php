<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function index() {
        $user = auth()->guard('api')->user();
        $currentUser = User::where('id', $user->id)->first();
        return new ProfileResource(true, 'Data profile berhasil didapatkan', $currentUser);
    }

    public function show($username){
        $user = User::where('username', $username)->first();
        return;
    }

    public function getThreadCurrentUser(){
        $user = auth()->guard('api')->user();
        $thread = Thread::with('categories')->where('user_id', $user->id)->paginate(10);
        return new ProfileResource(true, 'getThread username : ' . $user->username, $thread);
    }

    public function update(Request $request){

        $user = auth()->guard('api')->user();

        $currentUser = User::where('id', $user->id)->first();

        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:255',
            'username' => 'required|min:5|max:12|unique:users,username,' . $currentUser->id,
            'phone'     => 'nullable|numeric|max_digits:12',
        ]);

        if ($validator->fails()) {
            return new ProfileResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        // Update atribut user sesuai dengan data yang dikirimkan
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->save();

        return new ProfileResource(true, 'Profile berhasil diperbarui', $user);
    }

    public function change_bio(Request $request){
        $user = auth()->guard('api')->user();

        $currentUser = User::where('id', $user->id)->first();

        $user = new User();
        $user->bio = $request->bio;
        $user->save();

        return new ProfileResource(true, 'Bio Berhasil diperbarui', $user);
    }

    public function change_avatar(Request $request){
        $user = auth()->guard('api')->user();

        $currentUser = User::where('id', $user->id)->first();
        // Hapus avatar lama jika ada
        $oldAvatarName = basename($currentUser->avatar);
        if ($oldAvatarName) {
            Storage::delete('public/avatar/' . $oldAvatarName);
        }

        // Validasi ukuran dan resolusi avatar
        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000|max:2048', // Sesuaikan dengan ukuran dan resolusi yang Anda inginkan
        ]);

        if ($validator->fails()) {
            return new ProfileResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        // Upload avatar baru jika ada
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('public/avatar');
            $user->avatar = basename($avatarPath);
            $user->save();
        }

        return new ProfileResource(true, 'Avatar berhasil diperbarui', $user);
    }

    public function change_password(Request $request){
        // Validasi form untuk mengganti password
        $validator = Validator::make($request->all(), [
            'old_password'     => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return new ProfileResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        $user = auth()->guard('api')->user();

        // Verifikasi bahwa old_password sesuai dengan password yang ada di database
        if (!password_verify($request->old_password, $user->password)) {
            return new ProfileResource(false, 'Password lama tidak sesuai', null, 422);
        }

        // Update password baru
        $user->password = bcrypt($request->new_password);
        $user->save();

        return new ProfileResource(true, 'Password berhasil diperbarui', $user);
    }
}
