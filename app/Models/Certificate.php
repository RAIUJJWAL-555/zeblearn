<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_id',
        'certificate_number',
        'issued_at',
        'revoked_at',
        'revocation_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * The student who earned this certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for student relationship.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The course for which the certificate was issued.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The associated enrollment record.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Scope to valid (non-revoked) certificates.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('revoked_at');
    }

    /**
     * Scope to revoked certificates.
     */
    public function scopeRevoked(Builder $query): Builder
    {
        return $query->whereNotNull('revoked_at');
    }

    /**
     * Check if certificate is currently valid.
     */
    public function isValid(): bool
    {
        return $this->revoked_at === null;
    }

    /**
     * Check if certificate has been revoked.
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    /**
     * Revoke this certificate with a mandatory reason.
     */
    public function revoke(string $reason): void
    {
        $this->update([
            'revoked_at' => now(),
            'revocation_reason' => trim($reason),
        ]);
    }

    /**
     * Get the public verification URL.
     */
    public function verificationUrl(): string
    {
        return route('certificates.verify', ['certificate_number' => $this->certificate_number]);
    }
}
