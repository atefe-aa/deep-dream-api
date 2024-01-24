<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laboratory extends Model
{
    use HasFactory, SoftDeletes ;

    protected $fillable=[
        'user_id',
        'title',
        'address',
        'description'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasOne
    {
        return $this->hasOne(LaboratoryMedia::class,'lab_id');
    }

    public function prices() : HasMany
    {
        return $this->hasMany(Price::class,'lab_id');
    }
    public function counsellors() : HasMany
    {
        return $this->hasMany(Counsellor::class,'lab_id');
    }
}
