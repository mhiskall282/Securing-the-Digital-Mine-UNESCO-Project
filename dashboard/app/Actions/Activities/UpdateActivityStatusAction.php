<?php

declare(strict_types=1);

namespace App\Actions\Activities;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Append a new status-change log entry (never update in place).
 *
 * BIO CAPTURE: actor name, role, designation, and IP are all derived
 * server-side from the authenticated session + request. They are NEVER
 * accepted from client input to prevent spoofing.
 */
final class UpdateActivityStatusAction
{
    public function __construct(private readonly AuditService $auditService) {}

    public function execute(
        Activity $activity,
        string $status,
        ?string $remark,
        string $date,
    ): ActivityLog {
        /** @var User $actor */
        $actor = Auth::user();

        $oldStatus = $activity->currentStatusForDate($date);

        // Append a new log row — never UPDATE an existing one
        $log = ActivityLog::create([
            'activity_id' => $activity->id,
            'date' => $date,
            'status' => $status,
            'remark' => $remark,
            'updated_by' => $actor->id,
            'actor_name' => $actor->name,       // server-side only
            'actor_role' => $actor->role,       // server-side only
            'actor_designation' => $actor->designation, // server-side only
            'actor_ip' => Request::ip(),      // server-side only
        ]);

        // Write domain audit trail
        $this->auditService->log(
            subject: $activity,
            event: 'status_changed',
            oldValues: ['status' => $oldStatus, 'date' => $date],
            newValues: ['status' => $status, 'remark' => $remark, 'date' => $date],
        );

        // Structured application log (distinct from audit trail — see architecture.md)
        logger()->channel('state_changes')->info('activity.status_changed', [
            'activity_id' => $activity->id,
            'date' => $date,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'actor_id' => $actor->id,
            'actor_ip' => Request::ip(),
        ]);

        return $log;
    }
}
