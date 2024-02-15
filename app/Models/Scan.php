<?php

namespace App\Models;

use App\Helpers\JsonHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'slide_number',
        'slide_coordinates',
        'status',
        'slide_image',
        'image',
        'duration',
        'estimated_duration'
    ];

    public static function getFirstStatus($status)
    {
        return self::where([['status', $status], ['is_processing', 1]])->first();
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function estimatedDuration(): float|int
    {
        $coordinates = JsonHelper::decodeJson($this->slide_coordinates);
        if (!$coordinates) {
            return 0; // Or handle the error as appropriate
        }

        $settings = Setting::where('category_id', 1)->get(); // settings for 2x magnification
        if (!$settings) {
            return 0; // Or handle the error as appropriate
        }

        $stepX = $settings->where('key', 'x')->first()->value ?? 0;
        $stepY = $settings->where('key', 'y')->first()->value ?? 0;
        $numLayer = $settings->where('key', 'number-of-layers')->first()->value ?? 0;

        $minX = $coordinates['sw']['x'] ?? 0;
        $minY = $coordinates['sw']['y'] ?? 0;
        $maxX = $coordinates['ne']['x'] ?? 0;
        $maxY = $coordinates['ne']['y'] ?? 0;

        $area = ($maxX - $minX) * ($maxY - $minY);
        $stepArea = $stepX * $stepY;

        // Ensure scanner speed is not zero to avoid division by zero error
        $scannerSpeed = $this->scannerSpeed ?: 1;

        $approximateTime = ($area / ($stepArea * $scannerSpeed)) * $numLayer;
        return $approximateTime;
    }

}
