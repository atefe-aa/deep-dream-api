<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JsonException;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'nth',
        'sw_x',
        'sw_y',
        'ne_x',
        'ne_y',
    ];

    /**
     * @throws JsonException
     */
    public function toScanArray($nthSlide): array
    {
        $coordinates = [
            'sw' => ['x' => $this->sw_x, 'y' => $this->sw_y],
            'ne' => ['x' => $this->ne_x, 'y' => $this->ne_y],
        ];

        return [
            'slide_coordinates' => json_encode($coordinates, JSON_THROW_ON_ERROR),
            'nth_slide' => $nthSlide
        ];
    }
}
