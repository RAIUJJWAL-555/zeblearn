<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'course_name',
        'course_id',
        'source_url',
        'destination_url',
        'cta_type',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
    ];

    /**
     * Associated course if identified.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
