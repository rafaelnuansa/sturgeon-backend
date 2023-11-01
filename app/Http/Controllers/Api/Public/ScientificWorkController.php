<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\ScientificWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScientificWorkController extends Controller
{
    public function index()
    {

        $scientificWork = ScientificWork::when(request()->search, function ($query) {
            $query->where('title', 'like', '%' . request()->search . '%');
        })->with('user')->latest()->paginate(5);

        // Tambahkan query string 'search' ke tautan pagination
        $scientificWork->appends(['search' => request()->search]);
        return new ApiResource(true, 'Data Karya Ilmiah', $scientificWork);
    }

    public function homepage()
    {
        
        $scientificWork = ScientificWork::with('user')->latest()->take(10)->get();
        return new ApiResource(true, 'Data Home Karya Ilmiah', $scientificWork);
    }

    public function show($slug)
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:255',
            'content' => 'required',
            'attachment' => 'required|file|max:2048|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return new ApiResource(false, 'Request failed', $validator->errors(), 422);
        }

        $scientificWork = new ScientificWork();
        $scientificWork->title = $request->title ?? 'Undefined title';
        $scientificWork->content = $request->content;

        $slug = Str::slug($scientificWork->title);
        $originalSlug = $slug;

        $uniqueSlug = $slug . '-' . uniqid();

        // Handle cases where the generated slug already exists
        while (ScientificWork::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $originalSlug . '-' . uniqid();
        }

        $scientificWork->slug = $uniqueSlug;

        $attachment = $request->file('attachment');
        if ($attachment) {
            // Dapatkan nama asli file
            $originalFileName = $attachment->getClientOriginalName();
            $extension = $attachment->getClientOriginalExtension();

            // Buat nama file baru dengan format username_nama_asli_file_uniqid.extension
            $currentUser = auth()->guard('api')->user();
            $newFileName = Str::snake($currentUser->username) . '_' . Str::snake(pathinfo($originalFileName, PATHINFO_FILENAME)) . '_' . uniqid() . '.' . $extension;

            $attachment->storeAs('public/files/', $newFileName);
            $scientificWork->attachment = $newFileName;
        }


        $scientificWork->user_id = $currentUser->id;

        $scientificWork->save();
        return new ApiResource(true, 'Berhasil Membuat Karya Ilmiah', $scientificWork);
    }


    public function update(Request $request, ScientificWork $scientificWork)
    {
        // Pastikan pengguna yang mencoba memperbarui adalah pemilik karya ilmiah
        $currentUser = auth()->guard('api')->user();
        if ($scientificWork->user_id !== $currentUser->id) {
            return new ApiResource(false, 'Anda tidak memiliki izin untuk memperbarui karya ilmiah ini', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'title'   => 'required|max:255',
            'content' => 'required',
            'attachment' => 'nullable|file|max:2048|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return new ApiResource(false, 'Validasi gagal', $validator->errors(), 422);
        }

        $scientificWork->title = $request->title;
        $scientificWork->content = $request->content;

        // Update juga slug
        $slug = Str::slug($scientificWork->title);
        $originalSlug = $slug;

        $uniqueSlug = $slug . '-' . uniqid();

        // Handle cases where the generated slug already exists
        while (ScientificWork::where('slug', $uniqueSlug)->where('id', '!=', $scientificWork->id)->exists()) {
            $uniqueSlug = $originalSlug . '-' . uniqid();
        }

        $scientificWork->slug = $uniqueSlug;

        $newAttachment = $request->file('attachment');
        if ($newAttachment) {
            // Hapus lampiran lama jika ada
            if ($scientificWork->attachment) {
                $baseName = basename($scientificWork->attachment);
                Storage::delete('public/files/' . $baseName);
            }
            // Dapatkan nama asli file yang baru
            $originalFileName = $newAttachment->getClientOriginalName();
            $extension = $newAttachment->getClientOriginalExtension();

            // Buat nama file baru dengan format username_nama_asli_file_uniqid.extension
            $newFileName = Str::snake($currentUser->username) . '_' . Str::snake(pathinfo($originalFileName, PATHINFO_FILENAME)) . '_' . uniqid() . '.' . $extension;

            $newAttachment->storeAs('public/files/', $newFileName);
            $scientificWork->attachment = $newFileName;
        }

        $scientificWork->save();
        return new ApiResource(true, 'Karya ilmiah berhasil diperbarui', $scientificWork);
    }

    public function destroy(ScientificWork $scientificWork)
    {
        // Pastikan pengguna yang mencoba menghapus adalah pemilik karya ilmiah
        $currentUser = auth()->guard('api')->user();
        if ($scientificWork->user_id !== $currentUser->id) {
            return new ApiResource(false, 'Anda tidak memiliki izin untuk menghapus karya ilmiah ini', null, 403);
        }

        // Hapus lampiran jika ada
        if ($scientificWork->attachment) {
            $baseName = basename($scientificWork->attachment);
            Storage::delete('public/files/' . $baseName);
        }

        // Hapus karya ilmiah dari database
        $scientificWork->delete();

        return new ApiResource(true, 'Karya ilmiah berhasil dihapus', null);
    }
}
