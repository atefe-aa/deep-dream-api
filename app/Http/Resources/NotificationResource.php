<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;
use Morilog\Jalali\Jalalian;

class NotificationResource extends JsonResource
{

    /**
     * @throws JsonException
     */
    public function toArray(Request $request): array
    {
        $createdAt = $this->created_at;
        $now = now();
        $diffInDays = $createdAt->diffInDays($now);

        if ($diffInDays < 7) {
            // For periods less than a week, use the diffForHumans() method
            $formattedTime = $createdAt->diffForHumans();
        } else {
            // For periods longer than a week, display the full date and time.
            // Customize the format as needed.
            $formattedTime = Jalalian::fromDateTime($this->created_at)->format('Y/m/d H:i');
        }

        return [
            "id" => $this->id,
            "data" => $this->data,
            "time" => $formattedTime,
        ];
    }
}
