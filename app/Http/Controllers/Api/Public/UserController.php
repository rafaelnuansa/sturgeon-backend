<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Models\Thread;
use App\Models\User;

class UserController extends Controller
{

    // public function index()
    // {
    //     $users = User::latest()->take(10)->get();
    //     return new ApiResource(true, 'users berhasil diload', $users);
    // }

    public function index()
    {
        $users = User::latest()->take(10)->get();

        // $users = User::all();
        $responseData = [
            'success' => true,
            'message' => 'users berhasil diload',
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'phone' => $user->phone,
                    'bio' => $user->bio,
                    // 'created_at' => $user->created_at,
                    // 'updated_at' => $user->updated_at,
                ];
            }),
        ];

        return response()->json($responseData, 200, [], JSON_PRETTY_PRINT);
    }

    public function username($username)
    {
        $users = User::where('username', $username)->first();

        // $responseData = [
        //     'success' => true,
        //     'message' => 'users berhasil diload',
        //     'data' => $users->map(function ($user) {
        //         return [
        //             'id' => $user->id,
        //             'name' => $user->name,
        //             'username' => $user->username,
        //             'avatar' => $user->avatar,
        //             'phone' => $user->phone,
        //             'bio' => $user->bio,
        //         ];
        //     }),
        // ];

        // return response()->json($responseData, 200, [], JSON_PRETTY_PRINT);


        return new ApiResource(true, 'Data User', $users);
    }

    public function threads($username, Request $request) {
        $user = User::where('username', $username)->first();

        if (!$user) {
            $userNotFound = [
                'success' => false,
                'message' => 'User not found',
            ];
            return response()->json($userNotFound, 404, [], JSON_PRETTY_PRINT);
        }

        // Get the search query from the request
        $searchQuery = $request->input('q'); // Assuming you are passing the search query as 'q' parameter in the request

        // Query threads based on the user and search query
        $query = Thread::where('user_id', $user->id);

        if ($searchQuery) {
            $query->where(function ($subquery) use ($searchQuery) {
                $subquery->whereRaw("LOWER(title) LIKE ?", ["%" . strtolower($searchQuery) . "%"])
                        ->orWhereRaw("LOWER(content) LIKE ?", ["%" . strtolower($searchQuery) . "%"]);
            });
        }

        $threads = $query->with('thread_category')->paginate(10);

        return new ApiResource(true, 'Threads loaded by user successfully', $threads);
    }


}
