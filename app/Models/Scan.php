<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public static function getFirstStatus($status)
    {
        return self::where('status', $status)->first();
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

}
