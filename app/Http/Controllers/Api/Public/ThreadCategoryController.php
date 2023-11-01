<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\ThreadCategory;
use Illuminate\Http\Request;

class ThreadCategoryController extends Controller
{
    public function index(){
        $thread_categories = ThreadCategory::all()->sortBy('name', 'asc');
        return new ApiResource(true, 'Threads Categories Loaded!', $thread_categories);
    }

    public function show($slug){
        $thread_categories = ThreadCategory::where('slug', $slug)->with('threads')->first();
        return new ApiResource(true, 'Threads ' + $thread_categories->name, $thread_categories);
    }


}
