<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "username"=> $this->username,
            "laboratory"=> $this->laboratory ? $this->laboratory->id : null,
            "labName"=> $this->laboratory ? $this->laboratory->title : null,
            "name"=> $this->name,
            "phone"=> $this->phone,
            "roles"=>$this->roles->pluck('name'),
            "picture"=> $this->laboratory ? $this->laboratory->media->avatar : null,
        ];
    }
}
