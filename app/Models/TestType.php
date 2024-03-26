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
        'report_template_id',
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
     * @return null
     */
    public function getSettingsAttribute()
    {
        $categoryId = $this->getCategoryIdByMagnification($this->magnification);

        if ($categoryId) {
            return SettingsCategory::query()->MagnificationAndCondenser($categoryId)->get();

        }
        return null;
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
