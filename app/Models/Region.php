<?php

namespace App\Models;

use App\Helpers\JsonHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JsonException;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'coordinates',
        'status',
        'image',
    ];
    private int $scannerSpeed;

    public function __construct()
    {
        $this->scannerSpeed = config('services.scanner.speed');
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * @throws JsonException
     */
    public function estimatedDuration(): float|int
    {
        $coordinates = JsonHelper::decodeJson($this->coordinates);
        if (!$coordinates) {
            return 0;
        }

        // Assuming $this->scan->test->testType->settings is a collection of categories
        // and each category contains a 'settings' array
        $allSettings = collect($this->scan->test->testType->settings);

        // Flatten all settings arrays into a single collection for easier searching
        $flattenedSettings = $allSettings->flatMap(function ($category) {
            return $category['settings'];
        });

        // Now search for specific settings within the flattened collection
        $stepX = $flattenedSettings->where('key', 'x')->first()['value'] ?? 0;
        $stepY = $flattenedSettings->where('key', 'y')->first()['value'] ?? 0;
        $numLayer = $flattenedSettings->where('key', 'number-of-layers')->first()['value'] ?? 0;

        // Coordinates processing remains the same
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
