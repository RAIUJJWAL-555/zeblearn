<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogMigrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'source_url',
        'post_id',
        'slug',
        'title',
        'status', // discovered, imported, incomplete, duplicate, failed, skipped
        'reason',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'post_id' => 'integer',
    ];
}
