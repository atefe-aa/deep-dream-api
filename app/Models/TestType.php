<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function prices() : HasMany
    {
        return $this->hasMany(Price::class);
    }
}
