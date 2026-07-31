<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Activities\CreateActivityAction;
use App\Actions\Activities\DeleteActivityAction;
use App\Actions\Activities\UpdateActivityAction;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Activity::class);

        $activities = Activity::with('creator')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('activities.index', compact('activities'));
    }

    public function create(): View
    {
        $this->authorize('create', Activity::class);

        return view('activities.create');
    }

    public function store(StoreActivityRequest $request, CreateActivityAction $action): RedirectResponse
    {
        // Authorization handled in StoreActivityRequest::authorize()
        $activity = $action->execute($request->validated());

        return redirect()
            ->route('activities.show', $activity)
            ->with('success', 'Activity created successfully.');
    }

    public function show(Activity $activity): View
    {
        $this->authorize('view', $activity);

        $activity->load(['creator', 'logs' => fn ($q) => $q->orderByDesc('id')->limit(20)]);

        // Calculate last 7 days status trend
        $last7Days = collect(range(6, 0))->map(fn ($days) => today()->subDays($days)->format('Y-m-d'));
        $recentLogs = $activity->logs()
            ->whereBetween('date', [today()->subDays(6)->format('Y-m-d'), today()->format('Y-m-d')])
            ->get();

        $chartLabels = [];
        $chartValues = [];

        foreach ($last7Days as $dateStr) {
            $formattedLabel = Carbon::parse($dateStr)->format('D, d M');
            $chartLabels[] = $formattedLabel;

            $dayLogs = $recentLogs->filter(fn ($l) => $l->date->format('Y-m-d') === $dateStr);
            $latest = $dayLogs->sortByDesc('id')->first();
            $chartValues[] = $latest?->status === 'done' ? 100 : 0;
        }

        $trendData = [
            'labels' => $chartLabels,
            'values' => $chartValues,
        ];

        return view('activities.show', compact('activity', 'trendData'));
    }

    public function edit(Activity $activity): View
    {
        $this->authorize('update', $activity);

        return view('activities.edit', compact('activity'));
    }

    public function update(
        UpdateActivityRequest $request,
        Activity $activity,
        UpdateActivityAction $action,
    ): RedirectResponse {
        $action->execute($activity, $request->validated());

        return redirect()
            ->route('activities.show', $activity)
            ->with('success', 'Activity updated.');
    }

    public function destroy(Activity $activity, DeleteActivityAction $action): RedirectResponse
    {
        $this->authorize('delete', $activity);

        $action->execute($activity);

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity deleted.');
    }
}
