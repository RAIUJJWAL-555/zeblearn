<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalLinkOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'action',
        'custom_anchor_text',
        'notes',
    ];

    /**
     * Scope for pinned targets.
     */
    public function scopePinned($query)
    {
        return $query->where('action', 'pin');
    }

    /**
     * Scope for excluded targets.
     */
    public function scopeExcluded($query)
    {
        return $query->where('action', 'exclude');
    }
}
