<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'key',
        'value',
        'unit',
        'default',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SettingsCategory::class, 'category_id');
    }
}
