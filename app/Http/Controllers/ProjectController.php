<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\ProjectResource;
use App\Models\Project;

class ProjectController extends Controller
{
    public function postProject(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:projects,title',
            'category_id' => 'required|string|exists:project_categories,id',
            'description' => 'nullable|string',
            'price' => 'nullable|string',
            'links' => 'nullable|array',
            'images' => 'nullable|array',
            'keywords' => 'nullable|array',
            'tech_stack' => 'nullable|array',
        ]);
        $data['slug'] = Str::slug($data['title']);
        $project = Project::create($data);
        $createdProject = Project::with('projectCategory')->findOrFail($project->id);
        return $this->sendResponse(ProjectResource::make($createdProject)
            ->response()
            ->getData(true),'Project created successfully');
    }

    public function getAllProjects()
    {
        $project = Project::with('projectCategory')->paginate(20);
        return $this->sendResponse(ProjectResource::collection($project)
            ->response()
            ->getData(true),'Projects retrieved successfully');
    }

    public function getAProject(Project $project)
    {
        $project->load('projectCategory');
        return $this->sendResponse(ProjectResource::make($project)
            ->response()
            ->getData(true),'Project retrieved successfully');
    }

    public function updateProject(Project $project, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $project->update($data);
        $updatedProject = Project::with('projectCategory')->findOrFail($project->id);
        return $this->sendResponse(ProjectResource::make($updatedProject)
            ->response()
            ->getData(true),'Project updated successfully');
    }

     public function deleteProject(Project $project)
    {
        $project->delete();
        return $this->sendResponse([],'Project deleted successfully');
    }
}
