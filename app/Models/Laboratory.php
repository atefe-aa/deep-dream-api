<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Laboratory extends Model
{
    use HasFactory;

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
}
