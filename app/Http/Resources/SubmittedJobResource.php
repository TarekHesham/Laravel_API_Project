<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmittedJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'application_id' => $this->id,
            'job_title' => $this->job->job_title,
            'job_description' => $this->job->description,
            'job_type' => $this->job->work_type,
            'status' => $this->status,
        ];
    }
}
