<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestType extends Model
{
    use HasFactory;

    protected $fillable=[
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
}
