<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BlogCategoryResource;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function postBlogCategory(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $blogcategory = BlogCategory::create($data);
        $createdBlogCategory = BlogCategory::findOrFail($blogcategory->id);
        return $this->sendResponse(BlogCategoryResource::make($createdBlogCategory)
            ->response()
            ->getData(true),'Blog category created successfully');
    }

    public function getAllBlogCategories()
    {
        $blogCategories = BlogCategory::paginate(20);
        return $this->sendResponse(BlogCategoryResource::collection($blogCategories)
            ->response()
            ->getData(true),'Blog categories retrieved successfully');
    }

    public function getABlogCategory(BlogCategory $blogcategory)
    {
        return $this->sendResponse(BlogCategoryResource::make($blogcategory)
            ->response()
            ->getData(true),'Blog category retrieved successfully');
    }

    public function updateBlogCategory(BlogCategory $blogcategory, Request $request)
    {
        $blogcategory->update($request->all());
        $updatedDocument = BlogCategory::findOrFail($blogcategory->id);
        return $this->sendResponse(BlogCategoryResource::make($updatedDocument)
            ->response()
            ->getData(true),'Blog category updated successfully');
    }

     public function deleteBlogCategory(BlogCategory $blogcategory)
    {
        $blogcategory->delete();
        return $this->sendResponse([],'Blog category deleted successfully');
    }
}
