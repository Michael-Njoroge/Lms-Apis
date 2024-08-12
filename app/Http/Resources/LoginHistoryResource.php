<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginHistoryResource extends JsonResource {
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array {
		return [
			'id' => $this->id,
			'ip_address' => $this->ip_address,
			'login_at' => $this->login_at,
			'user_agent' => $this->user_agent,
			'user' => $this->whenLoaded('user', function () {
				return new UsersResource($this->user);
			}),
			'created_at' => $this->created_at->toDateTimeString(),
			'updated_at' => $this->updated_at->toDateTimeString(),
		];
	}
}
