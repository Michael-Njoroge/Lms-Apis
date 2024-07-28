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
}
