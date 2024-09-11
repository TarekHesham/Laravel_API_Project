<?php

namespace App\Http\Resources;

use App\Http\Resources\Jobs\JobResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'status' => $this->status,
            'candidate' => new UserResource($this->candidate),
            'job' => new JobResource($this->job),
            'cv' => $this->type == 'cv' ? asset("cvs/{$this->cv->cv}") : null,
            'form' => $this->type == 'form' ? new ApplicationFormResource($this->form) : null,
            'applyed_at' => $this->created_at
        ];
    }
}
