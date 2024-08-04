<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'author' => $this->author,
            'price' => $this->price,
            'price_after_discount' => $this->price_after_discount,
            'keywords' => $this->keywords,
            'tech_stack' => $this->tech_stack,
            'images' => $this->images,
            'links' => $this->links,
            'category' => new ProjectCategoryResource($this->whenLoaded('projectCategory')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ], function ($value) {
            return !is_null($value) && $value !== [];
        });
    }
}
