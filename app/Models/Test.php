<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable=[
      'patient_id',
      'lab_id',
      'sender_register_code',
      'test_type_id',
      'doctor_name',
      'price',
      'status',
      'num_slide',
        'duration',
        'description'
    ];
}
