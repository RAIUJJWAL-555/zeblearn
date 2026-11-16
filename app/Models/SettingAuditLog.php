<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingAuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'setting_key',
        'old_value',
        'new_value',
        'ip_address',
    ];

    /**
     * Get the administrator who updated this setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
