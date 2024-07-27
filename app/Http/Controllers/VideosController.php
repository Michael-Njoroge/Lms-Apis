<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\VideosResource;
use App\Models\Videos;

class VideosController extends Controller
{
    public function postVideo(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:videos,title',
            'description' => 'required|string',
            'video_url' => 'required|string',
            'category' => 'required|uuid|exists:video_categories,id',
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
        ]);
        $data['slug'] = Str::slug($data['title']);

        $video = Videos::create($data);
        $createdVideo = Videos::with('videoCategory')->findOrFail($video->id);

        return $this->sendResponse(VideosResource::make($createdVideo)
                ->response()
                ->getData(true), 'Video created successfully');
    }

    public function getVideos()
    {
        $videos = Videos::with('videoCategory')->paginate(20);
        return $this->sendResponse(VideosResource::collection($videos)
                ->response()
                ->getData(true), 'Videos retrieved successfully');
    }

    public function getAVideo(Videos $video)
    {
        $video->load('videoCategory');
        return $this->sendResponse(VideosResource::make($video)
                ->response()
                ->getData(true),'Video retrieved successfully');
    }

    public function updateVideo(Request $request, Videos $video)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($data['title']);
        }
        $video->update($data);
        $updatedVideo = Videos::with('videoCategory')->findOrFail($video->id);

        return $this->sendResponse(VideosResource::make($updatedVideo)
                ->response()
                ->getData(true),'Video updated successfully');
    }

    public function deleteVideo(Videos $video)
    {
        $video->delete();
        return $this->sendResponse([],'Video deleted successfully');
    }
}
