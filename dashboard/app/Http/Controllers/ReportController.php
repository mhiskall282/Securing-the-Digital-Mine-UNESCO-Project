<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Mail\ActivityReportMail;
use App\Models\Activity;
use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportingService $reportingService) {}

    public function index(ReportRequest $request): View|StreamedResponse
    {
        $this->authorize('viewAny', Activity::class);

        $logs = null;
        $chartData = null;
        $activities = Activity::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        $validated = $request->validated();

        if (isset($validated['from']) && isset($validated['to'])) {

            // ── CSV Export ────────────────────────────────────────────────
            if ($request->query('export') === 'csv') {
                return $this->streamCsv($validated);
            }

            // ── Dedicated Print View (all records, standalone page) ───────
            if ($request->query('print') === 'true') {
                return $this->printView($request, $validated);
            }

            // ── Regular paginated view ────────────────────────────────────
            $logs = $this->reportingService->query(
                from: $validated['from'],
                to: $validated['to'],
                status: $validated['status'] ?? null,
                activityId: isset($validated['activity_id']) ? (int) $validated['activity_id'] : null,
            );

            $allMatchingLogs = $this->reportingService->exportQuery(
                from: $validated['from'],
                to: $validated['to'],
                status: $validated['status'] ?? null,
                activityId: isset($validated['activity_id']) ? (int) $validated['activity_id'] : null,
            );

            $statusCounts = $allMatchingLogs->groupBy('status')->map->count();
            $dateCounts = $allMatchingLogs->groupBy(fn ($log) => $log->date->format('Y-m-d'))->map->count()->sortKeys();

            $chartData = [
                'status' => [
                    'labels' => ['Done', 'Pending'],
                    'values' => [
                        $statusCounts->get('done', 0),
                        $statusCounts->get('pending', 0),
                    ],
                ],
                'timeline' => [
                    'labels' => $dateCounts->keys()->toArray(),
                    'values' => $dateCounts->values()->toArray(),
                ],
            ];
        }

        return view('reports.index', compact('logs', 'activities', 'users', 'chartData'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Print — standalone full-record view, triggers browser print dialog
    // ─────────────────────────────────────────────────────────────────────────
    private function printView(ReportRequest $request, array $validated): View
    {
        $allLogs = $this->reportingService->exportQuery(
            from: $validated['from'],
            to: $validated['to'],
            status: $validated['status'] ?? null,
            activityId: isset($validated['activity_id']) ? (int) $validated['activity_id'] : null,
        );

        $statusCounts = $allLogs->groupBy('status')->map->count();
        $doneCount = $statusCounts->get('done', 0);
        $pendingCount = $statusCounts->get('pending', 0);
        $total = $allLogs->count();
        $completionPct = $total > 0 ? round($doneCount / $total * 100) : 0;

        // Group by date for sectioned table output
        $byDate = $allLogs->groupBy(fn ($log) => $log->date->format('Y-m-d'))->sortKeys();

        $activityFilter = isset($validated['activity_id'])
            ? Activity::find((int) $validated['activity_id'])?->title
            : null;

        return view('reports.print', [
            'from' => $validated['from'],
            'to' => $validated['to'],
            'statusFilter' => $validated['status'] ?? null,
            'activityFilter' => $activityFilter,
            'byDate' => $byDate,
            'total' => $total,
            'doneCount' => $doneCount,
            'pendingCount' => $pendingCount,
            'completionPct' => $completionPct,
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now()->format('d M Y, H:i'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CSV Export
    // ─────────────────────────────────────────────────────────────────────────
    private function streamCsv(array $validated): StreamedResponse
    {
        $exportLogs = $this->reportingService->exportQuery(
            from: $validated['from'],
            to: $validated['to'],
            status: $validated['status'] ?? null,
            activityId: isset($validated['activity_id']) ? (int) $validated['activity_id'] : null,
        );

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=support_activity_report_'.today()->format('Y-m-d').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($exportLogs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Activity Title', 'Category', 'Recurrence', 'Status', 'Updated By', 'Role', 'Remark', 'Time']);

            foreach ($exportLogs as $log) {
                fputcsv($file, [
                    $log->date->format('Y-m-d'),
                    $log->activity?->title ?? '—',
                    $log->activity?->category ?? '—',
                    $log->activity?->recurrence ?? '—',
                    $log->status,
                    $log->actor_name,
                    $log->actor_role ?? '—',
                    $log->remark ?? '—',
                    $log->created_at->format('H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Email dispatch
    // ─────────────────────────────────────────────────────────────────────────
    public function email(ReportRequest $request): RedirectResponse
    {
        $this->authorize('viewAny', Activity::class);
        $validated = $request->validated();

        if (isset($validated['from']) && isset($validated['to'])) {
            $exportLogs = $this->reportingService->exportQuery(
                from: $validated['from'],
                to: $validated['to'],
                status: $validated['status'] ?? null,
                activityId: isset($validated['activity_id']) ? (int) $validated['activity_id'] : null,
            );

            $recipients = $validated['recipients'] ?? User::pluck('email')->toArray();

            if (! empty($recipients)) {
                Mail::to($recipients)->send(
                    new ActivityReportMail(
                        $exportLogs,
                        $validated['from'],
                        $validated['to'],
                        $validated['subject'] ?? null,
                        $validated['message'] ?? null,
                    )
                );
            }

            return redirect()->back()->with('success', 'Report emailed to '.count($recipients).' recipient(s) successfully.');
        }

        return redirect()->back()->with('error', 'Please apply a date range before sending the report.');
    }
}
