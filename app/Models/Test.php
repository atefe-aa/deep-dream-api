<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    public function laboratory() : BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }
    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }
}
