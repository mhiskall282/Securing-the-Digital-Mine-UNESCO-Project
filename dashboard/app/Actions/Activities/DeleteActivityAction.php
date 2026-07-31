<?php

declare(strict_types=1);

namespace App\Actions\Activities;

use App\Models\Activity;
use App\Services\AuditService;

final class DeleteActivityAction
{
    public function __construct(private readonly AuditService $auditService) {}

    public function execute(Activity $activity): void
    {
        $this->auditService->log(
            subject: $activity,
            event: 'deleted',
            oldValues: $activity->toArray(),
        );

        $activity->delete(); // soft delete
    }
}
