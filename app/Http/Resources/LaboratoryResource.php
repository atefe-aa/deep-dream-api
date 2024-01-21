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

            "id"=>$this->id ,
            "labName"=>$this->title,
            "fullName"=>$this->user->name,
            "phone"=>$this->user->phone,
            "address"=>$this->address,
            "description"=>$this->description,
            "username"=>$this->user->username,
            "picture"=>$this->media->avatar,
        ];
    }
}
