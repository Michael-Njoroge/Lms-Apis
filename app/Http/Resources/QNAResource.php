<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QNAResource extends JsonResource
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
            'featured' => $this->featured,
            'user' => new UsersResource($this->whenLoaded('user')),
            'question' => new QuestionResource($this->whenLoaded('question')),
            'answer' => new AnswerResource($this->whenLoaded('answer')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
        // return parent::toArray($request);
    }
}
