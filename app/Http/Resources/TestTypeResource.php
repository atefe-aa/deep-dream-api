<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestTypeResource extends JsonResource
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
            "title" => $this->title,
            "code" => $this->code,
            "template" => $this->report_template_id,
            "gender" => $this->gender,
            "type" => $this->type,
            "numberOfLayers" => $this->num_layer,
            "microStep" => $this->micro_step,
            "step" => $this->step,
            "z" => $this->z_axis,
            "condenser" => $this->condenser,
            "brightness" => $this->brightness,
            "magnification" => $this->magnification,
            "description" => $this->description,
        ];
    }
}
