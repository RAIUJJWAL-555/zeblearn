<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalLinkingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_id',
        'action',
        'links_generated',
        'qualifying_score',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'links_generated' => 'integer',
        'qualifying_score' => 'integer',
    ];
}
