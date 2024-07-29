<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\ProjectCategoryResource;
use App\Models\ProjectCategory;

class ProjectCategoryController extends Controller
{
    public function postProjectCategory(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:project_categories,title',
        ]);
        $data['slug'] = Str::slug($data['title']);
        $projectcategory = ProjectCategory::create($data);
        $createdProjectCategory = ProjectCategory::findOrFail($projectcategory->id);
        return $this->sendResponse(ProjectCategoryResource::make($createdProjectCategory)
            ->response()
            ->getData(true),'Project category created successfully');
    }

    public function getAllProjectCategories()
    {
        $projectCategories = ProjectCategory::paginate(20);
        return $this->sendResponse(ProjectCategoryResource::collection($projectCategories)
            ->response()
            ->getData(true),'Project categories retrieved successfully');
    }

    public function getAProjectCategory(ProjectCategory $projectcategory)
    {
        return $this->sendResponse(ProjectCategoryResource::make($projectcategory)
            ->response()
            ->getData(true),'Project category retrieved successfully');
    }

    public function updateProjectCategory(ProjectCategory $projectcategory, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $projectcategory->update($data);
        $updatedProject = ProjectCategory::findOrFail($projectcategory->id);
        return $this->sendResponse(ProjectCategoryResource::make($updatedProject)
            ->response()
            ->getData(true),'Project category updated successfully');
    }

     public function deleteProjectCategory(ProjectCategory $projectcategory)
    {
        $projectcategory->delete();
        return $this->sendResponse([],'Project category deleted successfully');
    }
}
