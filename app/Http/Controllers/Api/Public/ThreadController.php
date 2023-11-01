<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Thread;
use App\Models\ThreadCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get threads with a search if provided
        $threads = Thread::with('thread_category', 'user')->when(request()->q, function ($query) {
            $searchQuery = strtolower(request()->q); // Convert the search query to lowercase
            $query->where(function ($subquery) use ($searchQuery) {
                $subquery->whereRaw("LOWER(title) LIKE ?", ["%" . $searchQuery . "%"])
                    ->orWhereRaw("LOWER(content) LIKE ?", ["%" . $searchQuery . "%"]);
            });
        })->latest()->paginate(5);

        // Append the 'search' query string to the pagination links
        $threads->appends(['search' => request()->q]);

        // Return the data using ApiResource
        return new ApiResource(true, 'Threads berhasil diload', $threads);
    }


    public function homepage()
    {
        $threads = Thread::with('thread_category', 'user')->latest()->take(10)->get();
        return new ApiResource(true, 'Threads home berhasil diload', $threads);
    }

    public function categories()
    {
        $categories = ThreadCategory::all();
        return new ApiResource(true, 'Categories fetched', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\ApiResource
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:255',
            'content' => 'required',
            'thread_category_id' => 'required',
            'image' => 'nullable|image|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000|image|max:2048|mimes:jpeg,png,gif,jpg,webp',
        ]);

        if ($validator->fails()) {
            return new ApiResource(false, 'Request failed', $validator->errors(), 422);
        }


        // Membuat instance baru dari model Thread
        $thread = new Thread();
        $thread->title  = $request->title ?? 'Undefined title';
        $thread->content = $request->content;
        $thread->thread_category_id = $request->thread_category_id;
        $slug = Str::slug($thread->title);
        $originalSlug = $slug;

        $uniqueSlug = $slug . '-' . uniqid();

        // Handle cases where the generated slug already exists
        while (Thread::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $originalSlug . '-' . uniqid();
        }

        $thread->slug = $uniqueSlug;
        //upload image
        $image = $request->file('image');
        if ($image) {
            $image->storeAs('public/threads', $image->hashName());
            $thread->image = $image->hashName();
        }

        // Menyimpan user_id berdasarkan pengguna yang saat ini diautentikasi
        $thread->user_id = auth()->guard('api')->user()->id;
        // $thread->user_id = $request->user_id;

        // save to db
        $thread->save();

        return new ApiResource(true, 'Berhasil Membuat Thread', $thread);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Thread  $thread
     * @return \App\Http\Resources\ApiResource
     */
    public function show($slug)
    {
        // Mengambil data thread dengan user terkait
        $thread = Thread::where('slug', $slug)->firstOrFail();
        $thread->load('user');
        return new ApiResource(true, 'Detail Thread', $thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \App\Http\Resources\ApiResource
     */
    public function update(Request $request, Thread $thread)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:255',
            'content' => 'required',
            'thread_category_id' => 'required',
            'image' => 'nullable|image|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000|image|max:2048|mimes:jpeg,png,gif,jpg,webp',

        ]);

        if ($validator->fails()) {
            return new ApiResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        // Periksa apakah pengguna memiliki izin untuk mengedit thread
        if ($thread->user_id !== auth()->guard('api')->user()->id) {
            return new ApiResource(false, 'Anda tidak memiliki izin untuk mengedit thread ini', null, 403);
        }

        // Perbarui atribut-atribut thread
        $thread->title   = $request->title;
        $thread->content = $request->content;
        $thread->thread_category_id = $request->category_id;

        // Update juga slug
        $slug = Str::slug($thread->title);
        $originalSlug = $slug;
        $uniqueSlug = $slug . '-' . uniqid();
        // Handle cases where the generated slug already exists
        while (Thread::where('slug', $uniqueSlug)->where('id', '!=', $thread->id)->exists()) {
            $uniqueSlug = $originalSlug . '-' . uniqid();
        }
        $thread->slug = $uniqueSlug;

        // Upload gambar jika ada
        $image = $request->file('image');
        if ($image) {
            if ($thread->image) {
                $oldImageName = basename($thread->image);
                Storage::delete('public/threads/' . $oldImageName);
            }
            $image->storeAs('public/threads', $image->hashName());
            $thread->image = $image->hashName();
        }

        // Menyimpan user_id berdasarkan pengguna yang saat ini diautentikasi
        $thread->user_id = auth()->guard('api')->user()->id;
        // $thread->user_id = $request->user_id;

        // Menyimpan perubahan ke database
        $thread->save();

        return new ApiResource(true, 'Thread berhasil diperbarui', $thread);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Thread $thread)
    {

        // Periksa apakah pengguna memiliki izin untuk menghapus thread
        if ($thread->user_id !== auth()->guard('api')->user()->id) {
            return new ApiResource(false, 'Anda tidak memiliki izin untuk menghapus thread ini', null, 403);
        }

        if ($thread->image) {
            $oldImageName = basename($thread->image);
            Storage::delete('public/threads/' . $oldImageName);
        }

        // Hapus thread
        $thread->delete();
        return response()->json(['success' => true, 'message' => 'Thread berhasil dihapus']);
    }
}
