<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "totals" => [
                [
                    "title" => "",
                    "unit" => "",
                    "value" => "",
                ],
                [
                    "title" => "",
                    "unit" => "",
                    "value" => "",
                ],
            ],
            "series" => [
                [
                    "name" => "",
                    "data" => "",
                ]
            ],
            "xAxisCategories" => ["", ""],
        ];
    }
}
