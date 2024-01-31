<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'gender',
        'type',
        'num_layer',
        'micro_step',
        'step',
        'z_axis',
        'condenser',
        'brightness',
        'magnification',
        'description',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * set default settings values for those null parameters
     *
     * @param $magnification
     * @return void
     */
    public function setDefaultValues($magnification): void
    {
        $categoryId = $this->getCategoryIdByMagnification($magnification);

        if ($categoryId) {
            $settings = Setting::where('category_id', $categoryId)->get()->keyBy('key');

            $this->z_axis = $this->z_axis ?? $settings['z']->value ?? null;
            $this->condenser = $this->condenser ?? $settings['condenser']->value ?? null;
            $this->brightness = $this->brightness ?? $settings['brightness']->value ?? null;
        }
    }

    /**
     * @param $magnification
     * @return int|null
     */
    public function getCategoryIdByMagnification($magnification): ?int
    {
        $categoryId = SettingsCategory::where('title', $magnification . 'x')->first();

        return $categoryId['id'] ?? null;
    }
}
