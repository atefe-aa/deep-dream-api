<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $scannedScan = $this->scans ? $this->scans->filter(function ($scan) {
            return $scan->status === 'scanned';
        })->first() : null;

        return [
            "id" => $this->id,
            "name" => $this->patient->name,
            "nationalId" => $this->patient->national_id,
            "age" => $this->patient->age,
            "ageUnit" => $this->patient->age_unit,
            "gender" => $this->patient->gender,
            "date" => Jalalian::fromDateTime($this->created_at)->format('Y/m/d H:i'),
            "registrationCode" => $this->id,
            "project" => $this->project_id,
            "doctorName" => $this->doctor_name,
            "img" => $scannedScan ? $scannedScan->regions->first()?->cytomine_image_id : null,
            "senderRegistrationCode" => $this->sender_register_code,
            "testType" => $this->testType->title,
            "description" => $this->description,
            "laboratory" => $this->laboratory->title,
            "progress" => $this->status,
            "price" => $this->price,
            "numberOfSlides" => $this->num_slide,
            "durations" => $this->duration,
            "laboratoryId" => $this->laboratory?->id,
            "testTypeId" => $this->testType?->id,
        ];
    }
}
