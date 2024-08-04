<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'published' => $this->published,
            'paid' => $this->paid,
            'instructor' => new UsersResource($this->whenLoaded('instructor')),
            'category' => new CourseCategoryResource($this->whenLoaded('category')),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'total_hours' => $this->total_hours,
            'enrolls' => $this->enrolls,
            'total_ratings' => $this->total_ratings,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
