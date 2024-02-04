<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlideResource extends JsonResource
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
            'nth' => $this->nth,
            'sw_x' => $this->sw_x,
            'sw_y' => $this->sw_y,
            'ne_x' => $this->ne_x,
            'ne_y' => $this->ne_y,
        ];
    }
}
