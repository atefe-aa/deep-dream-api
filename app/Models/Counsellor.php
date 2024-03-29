<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Counsellor extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_id',
        'cytomine_user_id',
        'name',
        'phone',
        'description'
    ];

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class, 'lab_id');
    }
}
