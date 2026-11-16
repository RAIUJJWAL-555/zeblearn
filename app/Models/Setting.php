<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public const GROUP_GENERAL = 'general';

    public const GROUP_BRANDING = 'branding';

    public const GROUP_PAYMENTS = 'payments';

    public const GROUP_EMAIL = 'email';

    public const GROUP_COURSES = 'courses';

    public const GROUP_CERTIFICATES = 'certificates';

    public const GROUP_SEO = 'seo';

    public const GROUP_MAINTENANCE = 'maintenance';

    public const GROUPS = [
        self::GROUP_GENERAL,
        self::GROUP_BRANDING,
        self::GROUP_PAYMENTS,
        self::GROUP_EMAIL,
        self::GROUP_COURSES,
        self::GROUP_CERTIFICATES,
        self::GROUP_SEO,
        self::GROUP_MAINTENANCE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_secret',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_secret' => 'boolean',
    ];

    /**
     * Scope query to a specific group.
     */
    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    /**
     * Get typed value.
     */
    public function getTypedValue(): mixed
    {
        if ($this->value === null) {
            return null;
        }

        return match ($this->type) {
            'boolean', 'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $this->value,
            'float', 'double' => (float) $this->value,
            'json', 'array' => json_decode($this->value, true) ?? [],
            default => $this->value,
        };
    }
}
