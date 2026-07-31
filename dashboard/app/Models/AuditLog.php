<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Immutable audit trail entry.
 *
 * NEVER update or delete rows here. This table is write-once.
 * It is DISTINCT from activity_logs: activity_logs tracks domain state
 * changes (status + remark for the handover view); audit_logs tracks ALL
 * user-initiated mutations for security and compliance purposes.
 *
 * Split rationale: activity_logs is a domain concept the UI reads;
 * audit_logs is an ops/security concern that may be shipped to a SIEM.
 */
class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_name',
        'actor_role',
        'actor_ip',
        'subject_type',
        'subject_id',
        'event',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Audit logs have no updated_at — they are immutable
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
