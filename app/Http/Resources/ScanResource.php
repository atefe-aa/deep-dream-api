<?php

namespace App\Http\Resources;

use App\Helpers\JsonHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class ScanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws JsonException
     */
    public function toArray(Request $request): array
    {
        // Calculate the expected completion time by adding the estimated duration to the updated_at timestamp
        $expectedCompletionTime = $this->updated_at ? $this->updated_at->addSeconds($this->estimated_duration) : null;

        // Calculate seconds left by subtracting the current time from the expected completion time
        $secondsLeft = $this->updated_at ? now()->diffInSeconds($expectedCompletionTime, false) : null; // Use 'false' to allow negative values

        return [
            'id' => $this->id,
            'nth' => $this->nth_slide,
            'slideImage' => $this->slide_image,
            'slideNumber' => $this->slide_number,
            'image' => $this->image,
            'laboratory' => $this->test ? $this->test->laboratory->title : null,
            'testNumber' => $this->test ? $this->test->id : null,
            'testType' => $this->test ? $this->test->testType->title : null,
            'duration' => $this->duration,
            'progress' => $this->status,
            'secondsLeft' => $secondsLeft,
            'coordinates' => JsonHelper::decodeJson($this->slide_coordinates)
        ];
    }
}
