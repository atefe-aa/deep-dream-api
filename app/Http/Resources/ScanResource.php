<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanResource extends JsonResource
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
            'nth' => $this->nth_slide,
            'slideImage' => $this->slide_image,
            'image' => $this->image,
            'laboratory' => $this->test ? $this->test->laboratory->title : null,
            'testNumber' => $this->test ? $this->test->id : null,
            'testType' => $this->test ? $this->test->testType->title : null,
            'duration' => $this->duration,
            'progress' => $this->status,
        ];
    }
}
