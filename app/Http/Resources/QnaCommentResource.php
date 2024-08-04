<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QnaCommentResource extends JsonResource
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
            'user' => new UsersResource($this->whenLoaded('user')),
            'question' => new QuestionResource($this->whenLoaded('question')),
            'answer' => new AnswerResource($this->whenLoaded('answer')),
            'comment' => $this->comment,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}
