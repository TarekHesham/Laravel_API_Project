<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => [
                'name' => $this->user->name,
                'avatar' => asset($this->user->profile_image),
            ],
            'job' => [
                'title' => $this->job->job_title,
                'slug' => $this->job->slug,
            ],
        ];
    }
}
