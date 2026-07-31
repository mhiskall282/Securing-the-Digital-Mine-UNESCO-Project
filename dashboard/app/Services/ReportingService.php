<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ReportingService — date-range activity queries.
 *
 * QUERY DESIGN:
 * The primary query uses a composite index on activity_logs(date, activity_id)
 * defined in the migration. For each (activity, date) pair we retrieve all log
 * entries (append-only) so the caller can derive the latest status from the
 * highest-id row per group. We avoid a GROUP BY + MAX subquery in the main
 * query to keep it readable; instead we return all logs and let the Livewire
 * component group them in PHP — acceptable at the expected volume (<100 logs/day).
 *
 * AT SCALE (100x volume):
 * - Add a materialized read-model table `activity_daily_summary` refreshed
 *   on every write via a queued job or DB trigger.
 * - Or use a window function (ROW_NUMBER OVER PARTITION BY activity_id, date)
 *   which MariaDB 10.4+ supports.
 * - Pagination is already implemented here; at 10x users add Redis caching
 *   with a 60s TTL on the report query.
 */
final class ReportingService
{
    /**
     * Query activity logs across a date range.
     * Returns paginated ActivityLog records with activity + updater eager-loaded.
     *
     * @param  string  $from  Y-m-d
     * @param  string  $to  Y-m-d
     * @param  string|null  $status  'pending'|'done'|null (all)
     * @param  int|null  $activityId  filter by specific activity
     */
    public function query(
        string $from,
        string $to,
        ?string $status = null,
        ?int $activityId = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = ActivityLog::with(['activity', 'updater'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($activityId !== null) {
            $query->where('activity_id', $activityId);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Daily handover summary: all active activities for a date,
     * with ALL their log entries for that date (to show the full timeline).
     *
     * Returns a Collection of Activity models with a `daysLogs` attribute
     * (the subset of logs for the requested date) and `currentStatus` derived
     * from the latest log entry.
     *
     * EAGER LOADING: activity logs are eager-loaded to prevent N+1 on the board.
     */
    public function dailySummary(string $date): Collection
    {
        return Activity::with(['logs' => function ($query) use ($date) {
            $query->whereDate('date', $date)
                ->orderBy('id', 'asc');
        }])
            ->active()
            ->orderBy('title')
            ->get()
            ->map(function (Activity $activity) {
                $logsForDay = $activity->logs;
                $latestLog = $logsForDay->last();
                $currentStatus = $latestLog?->status ?? 'pending';

                $activity->setAttribute('day_logs', $logsForDay);
                $activity->setAttribute('current_status', $currentStatus);
                $activity->setAttribute('latest_log', $latestLog);

                return $activity;
            });
    }

    public function exportQuery(
        string $from,
        string $to,
        ?string $status = null,
        ?int $activityId = null,
    ): Collection {
        $query = ActivityLog::with(['activity', 'updater'])
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($activityId !== null) {
            $query->where('activity_id', $activityId);
        }

        return $query->get();
    }
}
