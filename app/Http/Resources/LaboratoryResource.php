<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
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
            "labName" => $this->title,
            "fullName" => $this->user->name,
            "phone" => $this->user->phone,
            "address" => $this->address,
            "description" => $this->description,
            "username" => $this->user->username,
            "avatar" => $this->media->avatar ? $this->prepareUrl($this->media->avatar) : null,
            "header" => $this->media->header ? $this->prepareUrl($this->media->header) : null,
            "footer" => $this->media->footer ? $this->prepareUrl($this->media->footer) : null,
            "signature" => $this->media->signature ? $this->prepareUrl($this->media->signature) : null,
            "prices" => PriceResource::collection($this->prices)
        ];
    }

    private function prepareUrl(string $path)
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
