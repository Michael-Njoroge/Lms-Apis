<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\TutCategoryResource;
use App\Models\TutCategory;

class TutCategoryController extends Controller
{
    public function postTutorial(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:tut_categories,title',
        ]);

        $data['slug'] = Str::slug($data['title']);

        $tut = TutCategory::create($data);
        $createdTut = TutCategory::findOrFail($tut->id);
        return $this->sendResponse(TutCategoryResource::make($createdTut)
            ->response()
            ->getData(true),'Tutorial category created successfully');
    }

    public function getAllTutCategories()
    {
        $tutorialCategories = TutCategory::paginate(20);
        return $this->sendResponse(TutCategoryResource::collection($tutorialCategories)
            ->response()
            ->getData(true),'Tutorial categories retrieved successfully');
    }

    public function getATutCategory(TutCategory $tutorial)
    {
        return $this->sendResponse(TutCategoryResource::make($tutorial)
            ->response()
            ->getData(true),'Tutorial category retrieved successfully');
    }

    public function updateTutCategory(TutCategory $tutorial, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $tutorial->update($data);
        $updatedTutorial = TutCategory::findOrFail($tutorial->id);
        return $this->sendResponse(TutCategoryResource::make($updatedTutorial)
            ->response()
            ->getData(true),'Tutorial category updated successfully');
    }

     public function deleteTutCategory(TutCategory $tutorial)
    {
        $tutorial->delete();
        return $this->sendResponse([],'Tutorial category deleted successfully');
    }
}
