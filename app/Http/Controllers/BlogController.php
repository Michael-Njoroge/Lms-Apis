<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BlogResource;
use App\Models\Blog;

class BlogController extends Controller
{
     public function postBlog(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:videos,title',
            'description' => 'required|string',
            'category' => 'required|uuid|exists:blog_categories,id',
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $blog = Blog::create($data);
        $createdBlog = Blog::with('blogCategory')->findOrFail($blog->id);
        return $this->sendResponse(BlogResource::make($createdBlog)
            ->response()
            ->getData(true),'Blog created successfully');
    }

    public function getAllBlogs()
    {
        $blogCategories = Blog::with('blogCategory')->paginate(20);
        return $this->sendResponse(BlogResource::collection($blogCategories)
            ->response()
            ->getData(true),'Blogs retrieved successfully');
    }

    public function getABlog(Blog $blog)
    {
        $blog->load('blogCategory');
        return $this->sendResponse(BlogResource::make($blog)
            ->response()
            ->getData(true),'Blog retrieved successfully');
    }

    public function updateBlog(Blog $blog, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($data['title']);
        }
        $blog->update($data);

        $updatedBlog = Blog::with('blogCategory')->findOrFail($blog->id);
        return $this->sendResponse(BlogResource::make($updatedBlog)
            ->response()
            ->getData(true),'Blog updated successfully');
    }

     public function deleteBlog(Blog $blog)
    {
        $blog->delete();
        return $this->sendResponse([],'Blog deleted successfully');
    }
}
