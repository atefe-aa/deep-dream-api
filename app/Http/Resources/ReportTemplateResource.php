<?php

namespace App\Http\Resources;

use App\Helpers\JsonHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class ReportTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws JsonException
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "testTitle" => $this->test_title,
            "note" => $this->note,
            "sections" => JsonHelper::decodeJson($this->data),
        ];
    }
}
