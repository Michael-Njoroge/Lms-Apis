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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'topic_name' => $this->topic_name,
            'keywords' => $this->keywords,
            'category' => new TutCategoryResource($this->tutCategory),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
