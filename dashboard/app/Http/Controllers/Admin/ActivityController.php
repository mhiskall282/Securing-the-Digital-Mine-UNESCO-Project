<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Activities\CreateActivityAction;
use App\Actions\Activities\DeleteActivityAction;
use App\Actions\Activities\UpdateActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $this->authorize('create', Activity::class);

        $activities = Activity::withTrashed()->with('creator')->orderByDesc('created_at')->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    public function create(): View
    {
        $this->authorize('create', Activity::class);

        return view('admin.activities.create');
    }

    public function store(StoreActivityRequest $request, CreateActivityAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('admin.activities.index')->with('success', 'Activity created.');
    }

    public function edit(Activity $activity): View
    {
        $this->authorize('update', $activity);

        return view('admin.activities.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity, UpdateActivityAction $action): RedirectResponse
    {
        $action->execute($activity, $request->validated());

        return redirect()->route('admin.activities.index')->with('success', 'Activity updated.');
    }

    public function destroy(Activity $activity, DeleteActivityAction $action): RedirectResponse
    {
        $this->authorize('delete', $activity);
        $action->execute($activity);

        return redirect()->route('admin.activities.index')->with('success', 'Activity deleted.');
    }
}
