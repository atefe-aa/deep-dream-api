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
        $id = $this->template_id ?: $this->id;
        return [
            "id" => $id,
            "testTitle" => $this->test_title ?: $this->template?->test_title,
            "note" => $this->note ?: $this->template?->note,
            "sections" => JsonHelper::decodeJson($this->data),
        ];
    }
}
