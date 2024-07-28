<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\CourseResource;
use App\Models\CourseCategory;
use App\Models\Course;

class CourseController extends Controller
{
    public function postCourse(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|min:3|max:350|unique:courses,title',
            'description' => 'required|string|min:3|max:5000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|uuid|exists:course_categories,id',
        ]);
        $data['slug'] = Str::slug($data['title']);
        $data['instructor_id'] = auth()->id();
        $course = Course::create($data);
        $createdCourse = Course::with(['instructor', 'category', 'lessons', 'ratings'])->findOrFail($course->id);
        return $this->sendResponse(CourseResource::make($createdCourse)
            ->response()
            ->getData(true),'Course created successfully');
    }

    public function getAllCourses()
    {
        $courses = Course::with(['instructor', 'category', 'lessons', 'ratings'])->paginate(20);
        return $this->sendResponse(CourseResource::collection($courses)
            ->response()
            ->getData(true),'Courses retrieved successfully');
    }

    public function getACourse(Course $course)
    {
        $course->load(['instructor', 'category', 'lessons', 'ratings']);
        return $this->sendResponse(CourseResource::make($course)
            ->response()
            ->getData(true),'Course retrieved successfully');
    }

    public function getAllCoursesByCategory(Request $request, $type)
    {
        try {
            $category = CourseCategory::where('slug', $type)->firstOrFail();
            
            $courses = Course::with(['instructor', 'category', 'lessons', 'ratings'])
                             ->where('category_id', $category->id)
                             ->paginate(20);
            
            return $this->sendResponse(CourseResource::collection($courses)
                                ->response()
                                ->getData(true), 'Courses by category retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Category not found or other error occurred', 404);
        }
    }

    public function getAllCoursesByInstructor()
    {
        try {
            
            $courses = Course::with(['instructor', 'category', 'lessons', 'ratings'])
                             ->where('instructor_id', auth()->id())
                             ->paginate(20);
            
            return $this->sendResponse(CourseResource::collection($courses)
                                ->response()
                                ->getData(true), 'Instructor Courses retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Something went wrong!', 404);
        }
    }

    public function updateCourse(Course $course, Request $request)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $course->update($data);
        $updatedVideo = Course::with(['instructor', 'category', 'lessons', 'ratings'])->findOrFail($course->id);
        return $this->sendResponse(CourseResource::make($updatedVideo)
            ->response()
            ->getData(true),'Course updated successfully');
    }

     public function deleteCourse(Course $course)
    {
        $course->delete();
        return $this->sendResponse([],'Course deleted successfully');
    }
}
