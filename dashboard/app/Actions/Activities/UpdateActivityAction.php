<?php

declare(strict_types=1);

namespace App\Actions\Activities;

use App\Models\Activity;
use App\Services\AuditService;

final class UpdateActivityAction
{
    public function __construct(private readonly AuditService $auditService) {}

    public function execute(Activity $activity, array $validated): Activity
    {
        $oldValues = $activity->only(array_keys($validated));

        $activity->update($validated);

        $this->auditService->log(
            subject: $activity,
            event: 'updated',
            oldValues: $oldValues,
            newValues: $validated,
        );

        return $activity;
    }
}
