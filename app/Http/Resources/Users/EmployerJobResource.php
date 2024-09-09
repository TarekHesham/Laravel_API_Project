<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\EmployerApplicationResource;
use App\Http\Resources\Jobs\JobResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerJobResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'job' => new JobResource($this),
            'applications' => EmployerApplicationResource::collection($this->applications),
        ];
    }
}
