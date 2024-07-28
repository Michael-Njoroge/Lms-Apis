<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\CourseCategoryResource;
use App\Models\CourseCategory;

class CourseCategoryController extends Controller
{
    public function postCourseCategory(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|unique:course_categories,title',
        ]);
        $data['slug'] = Str::slug($data['title']);
        $coursecategory = CourseCategory::create($data);
        $createdCourseCategory = CourseCategory::findOrFail($coursecategory->id);
        return $this->sendResponse(CourseCategoryResource::make($createdCourseCategory)
            ->response()
            ->getData(true),'Course category created successfully');
    }

    public function getAllCourseCategories()
    {
        $courseCategories = CourseCategory::paginate(20);
        return $this->sendResponse(CourseCategoryResource::collection($courseCategories)
            ->response()
            ->getData(true),'Course categories retrieved successfully');
    }

    public function getACourseCategory(CourseCategory $coursecategory)
    {
        return $this->sendResponse(CourseCategoryResource::make($coursecategory)
            ->response()
            ->getData(true),'Course category retrieved successfully');
    }

    public function updateCourseCategory(CourseCategory $coursecategory, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $coursecategory->update($data);
        $updatedVideo = CourseCategory::findOrFail($coursecategory->id);
        return $this->sendResponse(CourseCategoryResource::make($updatedVideo)
            ->response()
            ->getData(true),'Course category updated successfully');
    }

     public function deleteCourseCategory(CourseCategory $coursecategory)
    {
        $coursecategory->delete();
        return $this->sendResponse([],'Course category deleted successfully');
    }
}
