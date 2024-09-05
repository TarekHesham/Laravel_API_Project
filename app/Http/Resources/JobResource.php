<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'job_title' => $this->job_title,
            'description' => $this->description,
            'experience_level' => $this->experience_level,
            'salary_from' => $this->salary_from,
            'salary_to' => $this->salary_to,
            'work_type' => $this->work_type,
            'deadline' => $this->deadline,
            'applications' => $this->number_of_applications,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'location' => $this->location->name,
            'employer' => new UserResource($this->employer),
            'comments' => CommentResource::collection($this->comments),
        ];
    }
}
