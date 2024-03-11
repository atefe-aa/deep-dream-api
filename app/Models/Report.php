<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'user_id',
        'template_id',
        'data'
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

}
