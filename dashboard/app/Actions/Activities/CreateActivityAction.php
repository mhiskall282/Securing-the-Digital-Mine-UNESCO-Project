<?php

declare(strict_types=1);

namespace App\Actions\Activities;

use App\Models\Activity;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

final class CreateActivityAction
{
    public function __construct(private readonly AuditService $auditService) {}

    public function execute(array $validated): Activity
    {
        $activity = Activity::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        $this->auditService->log(
            subject: $activity,
            event: 'created',
            newValues: $validated,
        );

        logger()->channel('state_changes')->info('activity.created', [
            'activity_id' => $activity->id,
            'title' => $activity->title,
            'actor_id' => Auth::id(),
        ]);

        return $activity;
    }
}
