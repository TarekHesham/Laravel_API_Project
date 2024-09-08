<?php

namespace App\Http\Resources\Dependency;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "image" => Str::startsWith($this->image, 'http') ? $this->image : asset("images/jobs/{$this->image}"),
        ];
    }
}
