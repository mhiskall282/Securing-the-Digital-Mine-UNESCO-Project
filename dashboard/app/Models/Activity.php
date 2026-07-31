<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'recurrence',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'subject_id')
            ->where('subject_type', self::class);
    }

    // ──────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeDaily(Builder $query): void
    {
        $query->where('recurrence', 'daily');
    }

    // ──────────────────────────────────────────
    // Derived state helpers
    // ──────────────────────────────────────────

    /**
     * Get the latest log entry for a given date.
     * Used by the daily board to determine current status.
     */
    public function latestLogForDate(string $date): ?ActivityLog
    {
        return $this->logs()
            ->whereDate('date', $date)
            ->latest('id')
            ->first();
    }

    public function currentStatusForDate(string $date): string
    {
        return $this->latestLogForDate($date)?->status ?? 'pending';
    }
}
