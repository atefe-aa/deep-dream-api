<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable=[
        'lab_id',
        'test_type_id',
        'price',
        'description',
        'price_per_slide'
    ];
}