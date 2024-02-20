<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'lab_id',
        'project_id',
        'sender_register_code',
        'test_type_id',
        'doctor_name',
        'status',
        'num_slide',
        'duration',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($test) {
            $test->calculatePrice();
        });
    }

    public function calculatePrice()
    {
        $priceModel = Price::where('lab_id', $this->lab_id)
            ->where('test_type_id', $this->test_type_id)
            ->first();

        if ($priceModel) {
            $this->price = $priceModel->price + (($this->num_slide - 1) * $priceModel->price_per_slide);
        }
    }

//    public function setPriceAttribute(): void
//    {
//        $priceModel = Price::where('lab_id', $this->lab_id)
//            ->where('test_type_id', $this->test_type_id)
//            ->first();
//        if ($priceModel) {
//            $this->attributes['price'] = $priceModel->price + (($this->num_slide - 1) * $priceModel->price_per_slide);
//        }
//    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

}
