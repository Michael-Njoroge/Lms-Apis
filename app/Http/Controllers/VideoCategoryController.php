<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\VideoCategoryResource;
use App\Models\VideoCategory;

class VideoCategoryController extends Controller
{
    public function postVideoCategory(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:video_categories,title',
        ]);
        $data['slug'] = Str::slug($data['title']);
        $videocategory = VideoCategory::create($data);
        $createdVideoCategory = VideoCategory::findOrFail($videocategory->id);
        return $this->sendResponse(VideoCategoryResource::make($createdVideoCategory)
            ->response()
            ->getData(true),'Video category created successfully');
    }

    public function getAllVideoCategories()
    {
        $videoCategories = VideoCategory::paginate(20);
        return $this->sendResponse(VideoCategoryResource::collection($videoCategories)
            ->response()
            ->getData(true),'Video categories retrieved successfully');
    }

    public function getAVideoCategory(VideoCategory $videocategory)
    {
        return $this->sendResponse(VideoCategoryResource::make($videocategory)
            ->response()
            ->getData(true),'Video category retrieved successfully');
    }

    public function updateVideoCategory(VideoCategory $videocategory, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $videocategory->update($data);
        $updatedVideo = VideoCategory::findOrFail($videocategory->id);
        return $this->sendResponse(VideoCategoryResource::make($updatedVideo)
            ->response()
            ->getData(true),'Video category updated successfully');
    }

     public function deleteVideoCategory(VideoCategory $videocategory)
    {
        $videocategory->delete();
        return $this->sendResponse([],'Video category deleted successfully');
    }
}
