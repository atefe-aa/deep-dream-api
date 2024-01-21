<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryMedia extends Model
{
    use HasFactory;

    protected $fillable=[
        'lab_id',
        'avatar',
        'header',
        'footer',
        'signature'
    ];
}
