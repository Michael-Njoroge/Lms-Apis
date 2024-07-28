<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\Course;

class LessonController extends Controller
{
    public function createLesson(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:lessons,title',
            'description' => 'required|string',
            'video_url' => 'nullable|string',
            'free_preview' => 'nullable|boolean',
        ]);
        $data['slug'] = Str::slug($data['title']);
        
        $lesson = Lesson::create($data);
        $course->lessons()->attach($lesson->id);
        $createdLesson = Lesson::findOrFail($lesson->id);
        
        return $this->sendResponse(LessonResource::make($createdLesson)
            ->response()
            ->getData(true), 'Lesson added to course successfully');
    }

    public function getLessons(Course $course)
    {
        $lessons = $course->lessons()->get();
        return $this->sendResponse(LessonResource::collection($lessons)
            ->response()
            ->getData(true), 'Lessons retrieved successfully');
    }

    public function getALesson(Course $course, Lesson $lesson)
    {
        if (!$course->lessons()->find($lesson->id)) {
            return $this->sendError('Lesson not found in the specified course');
        }

        return $this->sendResponse(LessonResource::make($lesson)
            ->response()
            ->getData(true), 'Lesson retrieved successfully');
    }

    public function updateLesson(Request $request,Course $course, Lesson $lesson)
    {
        if (!$course->lessons()->find($lesson->id)) {
            return $this->sendError('Lesson not found in the specified course');
        }

        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }
        $lesson->update($data);
        $updatedLesson = Lesson::findOrFail($lesson->id);

        return $this->sendResponse(LessonResource::make($updatedLesson)
            ->response()
            ->getData(true), 'Lesson updated successfully');
    }

    public function deleteLesson(Course $course, Lesson $lesson)
    {
        if (!$course->lessons()->find($lesson->id)) {
            return $this->sendError('Lesson not found in the specified course');
        }
        $course->lessons()->detach($lesson->id);
        $lesson->delete();

        return $this->sendResponse([], 'Lesson deleted successfully');
    }

}
