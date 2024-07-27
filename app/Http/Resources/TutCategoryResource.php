<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutCategoryResource extends JsonResource
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
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], function ($value) {
            return !is_null($value);
        });
    }
}
