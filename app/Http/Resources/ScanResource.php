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
            'id' => $this['scan']['id'],
            'nth' => $this['nth'],
            'image' => $this['scan']['slide_image'],
            'cytomine' => $this['scan']['image'],
            'laboratory' => $this['test']['laboratory']['title'],
            'testNumber' => $this['test']['id'],
            'testType' => $this['test']['testType']['title'],
            'progress' => $this['scan']['status'],
            'duration' => $this['test']['duration'],
        ];
    }
}
