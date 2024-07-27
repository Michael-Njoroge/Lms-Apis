<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorialResource extends JsonResource
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
            'topic_name' => $this->topic_name,
            'keywords' => !empty($this->keywords) ? $this->keywords : null,
            'category_slug' => $this->whenLoaded('tutCategory') ? $this->tutCategory->slug : null,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
