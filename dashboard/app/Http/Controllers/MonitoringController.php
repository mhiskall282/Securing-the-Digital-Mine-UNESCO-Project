<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SRE Monitoring Dashboard
 *
 * Provides a real-time operational overview for Admin and Lead roles:
 *   - System health metrics (DB connectivity, uptime, user counts)
 *   - Live audit log stream (recent mutations with actor and event details)
 *   - Activity completion trends by day and category
 *   - Alert surface for stale/pending activities
 *   - Per-user contribution breakdown
 */
class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Activity::class);

        // ── System Snapshot ─────────────────────────────────────────────
        $totalUsers = User::count();
        $totalActivities = Activity::count();
        $todayLogs = ActivityLog::whereDate('date', today())->count();
        $pendingToday = ActivityLog::whereDate('date', today())->where('status', 'pending')->count();
        $doneToday = ActivityLog::whereDate('date', today())->where('status', 'done')->count();

        // ── Audit Log Stream (paginated, newest first) ───────────────────
        $auditLogs = AuditLog::latest('created_at')->paginate(20);

        // ── 7-Day Activity Completion Trend ──────────────────────────────
        $trendDays = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->toDateString());
        $doneTrend = [];
        $pendTrend = [];
        foreach ($trendDays as $day) {
            $doneTrend[] = ActivityLog::whereDate('date', $day)->where('status', 'done')->count();
            $pendTrend[] = ActivityLog::whereDate('date', $day)->where('status', 'pending')->count();
        }
        $trendChart = [
            'labels' => $trendDays->map(fn ($d) => date('D d/m', strtotime($d)))->values()->toArray(),
            'done' => $doneTrend,
            'pending' => $pendTrend,
        ];

        // ── Category Breakdown (last 30 days) ───────────────────────────
        $categoryBreakdown = Activity::withCount([
            'logs as total_logs' => fn ($q) => $q->whereDate('date', '>=', now()->subDays(30)),
            'logs as done_count' => fn ($q) => $q->where('status', 'done')->whereDate('date', '>=', now()->subDays(30)),
        ])->get()->groupBy('category')->map(function ($group) {
            return [
                'total' => $group->sum('total_logs'),
                'done' => $group->sum('done_count'),
            ];
        });

        // ── Top Contributors (last 7 days by log count) ─────────────────
        $topContributors = ActivityLog::selectRaw('actor_name, actor_role, COUNT(*) as log_count')
            ->whereDate('date', '>=', now()->subDays(7))
            ->groupBy('actor_name', 'actor_role')
            ->orderByDesc('log_count')
            ->limit(5)
            ->get();

        // ── Stale Pending Alerts (pending for more than 1 day) ──────────
        $staleAlerts = ActivityLog::with('activity')
            ->whereDate('date', '<', today())
            ->where('status', 'pending')
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('activity_logs')
                    ->whereDate('date', '<', today())
                    ->groupBy('activity_id', 'date');
            })
            ->latest()
            ->limit(10)
            ->get();

        // ── Event Type Breakdown ─────────────────────────────────────────
        $eventBreakdown = AuditLog::selectRaw('event, COUNT(*) as count')
            ->groupBy('event')
            ->orderByDesc('count')
            ->get();

        return view('monitoring.index', compact(
            'totalUsers', 'totalActivities', 'todayLogs', 'pendingToday', 'doneToday',
            'auditLogs', 'trendChart', 'categoryBreakdown',
            'topContributors', 'staleAlerts', 'eventBreakdown'
        ));
    }
}
