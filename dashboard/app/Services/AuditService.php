<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * AuditService — write-only audit trail.
 *
 * SEPARATION OF CONCERNS NOTE:
 * - activity_logs = domain state changes (used for shift handover, business reporting)
 * - audit_logs    = security/compliance log of all mutations (who changed what, with IP + diff)
 *
 * These are intentionally separate tables. The audit log would be shipped to a SIEM
 * at scale; the activity_logs table is a first-class business entity.
 *
 * BIO CAPTURE NOTE:
 * All actor fields (name, role, IP) are captured from server-side auth session and
 * Request::ip() — never from client-submitted data. This prevents bio spoofing by
 * a malicious actor injecting a different user's name in the request body.
 */
final class AuditService
{
    public function log(
        Model $subject,
        string $event,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        /** @var User|null $actor */
        $actor = Auth::user();

        return AuditLog::create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'System',
            'actor_role' => $actor?->role,
            'actor_ip' => Request::ip(),
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
