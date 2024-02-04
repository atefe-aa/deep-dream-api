<?php

namespace App\Models;

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
}
