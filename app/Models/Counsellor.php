<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Counsellor extends Model
{
    use HasFactory;

    protected $fillable=[
        'lab_id',
        'name',
        'phone'
    ];

    public function laboratory() : BelongsTo
    {
        return $this->belongsTo(Laboratory::class,'lab_id');
    }
}
