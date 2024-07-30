<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'description' => $this->description,
            'slug' => $this->slug,
            'votes' => QnaVoteResource::collection($this->whenLoaded('votes')),
            'vote_count' => $this->vote_count,
            'tag' => new QnaTagResource($this->whenLoaded('tag')),
            'comments' => QnaCommentResource::collection($this->whenLoaded('comments')),
            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
