<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\TutorialResource;
use App\Models\Tutorial;
use App\Models\TutCategory;

class TutorialController extends Controller
{
    public function postTutorial(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:tutorials,title',
            'topic_name' => 'required|string|unique:tutorials,topic_name',
            'content' => 'required|string',
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
            'category' => 'required|uuid|exists:tut_categories,id',
        ]);

        $data['slug'] = Str::slug($data['title']);

        $tut = Tutorial::create($data);
        $createdTut = Tutorial::findOrFail($tut->id);
        $createdTut->load('tutCategory');
        return $this->sendResponse(TutorialResource::make($createdTut)
            ->response()
            ->getData(true),'Tutorial created successfully');
    }

    public function getAllTutorials()
    {
        $tutorials = Tutorial::with('tutCategory')->paginate(20);
        return $this->sendResponse(TutorialResource::collection($tutorials)
            ->response()
            ->getData(true),'Tutorials retrieved successfully');
    }

    public function getATutorial(Request $request, $slug, $type)
    {
        try {
            $category = TutCategory::where('slug', $type)->firstOrFail();
            $tutorial = Tutorial::where('slug', $slug)
                                ->where('category', $category->id)
                                ->firstOrFail();
            $tutorialTopics = Tutorial::where('category', $category->id)
                                      ->select('id', 'topic_name', 'title', 'slug', 'category')
                                      ->orderBy('created_at')
                                      ->get();
            return response()->json([
                'tutorial' => new TutorialResource($tutorial),
                'topics' => TutorialResource::collection($tutorialTopics)
            ]);
            
        } catch (Exception $e) {
            return $this->sendError('Tutorial not found');
        }
    }

    public function updateTutorial(Tutorial $tutorial, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $tutorial->update($data);
        $updatedTutorial = Tutorial::findOrFail($tutorial->id);
        $updatedTutorial->load('tutCategory');
        return $this->sendResponse(TutorialResource::make($updatedTutorial)
            ->response()
            ->getData(true),'Tutorial updated successfully');
    }

     public function deleteTutorial(Tutorial $tutorial)
    {
        $tutorial->delete();
        return $this->sendResponse([],'Tutorial deleted successfully');
    }
}
