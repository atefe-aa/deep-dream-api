<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingsCategory extends Model
{
    use HasFactory;

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'category_id');
    }

    public function scopeMagnificationAndCondenser(Builder $query, int $magId): Builder
    {
        // Always include ID 5 for the condenser settings
        $ids = [$magId, 5];

        return $query->whereIn('id', $ids)->with('settings');
    }
}
