<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\ThreadImage;

class UploadController extends Controller
{
    public function thread_image_store(Request $request)
    {
            $validator = Validator::make($request->all(), [
           
                'image' => 'nullable|image|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000|image|max:2048|mimes:jpeg,png,gif,jpg,webp',
            ]);
    
            if ($validator->fails()) {
                return new ApiResource(false, 'Request failed', $validator->errors(), 422);
            }
    
    
            // Membuat instance baru dari model Thread
            $threadImage = new ThreadImage();
         
            $image = $request->file('image');
            if ($image) {
                $image->storeAs('public/threads', $image->hashName());
                $threadImage->image = $image->hashName();
            }
    
            // Menyimpan user_id berdasarkan pengguna yang saat ini diautentikasi
            $threadImage->user_id = auth()->guard('api')->user()->id;
    
            // save to db
            $threadImage->save();
    
            return new ApiResource(true, 'Upload Image Successfully', $threadImage);
    }
}
