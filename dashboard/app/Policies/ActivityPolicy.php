<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /** Any authenticated user can list activities. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Any authenticated user can view a specific activity. */
    public function view(User $user, Activity $activity): bool
    {
        return true;
    }

    /** Only lead/admin can create activity definitions. */
    public function create(User $user): bool
    {
        return $user->canManageActivities();
    }

    /** Only lead/admin can edit activity definitions. */
    public function update(User $user, Activity $activity): bool
    {
        return $user->canManageActivities();
    }

    /** Only admin can delete. */
    public function delete(User $user, Activity $activity): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can restore soft-deleted activities. */
    public function restore(User $user, Activity $activity): bool
    {
        return $user->isAdmin();
    }

    /**
     * Any authenticated user can update status/remark.
     * Rationale: shift handover — the updating agent may not be the creator.
     */
    public function updateStatus(User $user, Activity $activity): bool
    {
        return true;
    }
}
