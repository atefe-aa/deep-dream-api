<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
