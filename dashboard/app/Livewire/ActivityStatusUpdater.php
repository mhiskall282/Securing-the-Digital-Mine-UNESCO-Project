<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Activities\UpdateActivityStatusAction;
use App\Models\Activity;
use Illuminate\View\View;
use Livewire\Component;

class ActivityStatusUpdater extends Component
{
    public Activity $activity;

    public string $date;

    public string $status = 'pending';

    public string $remark = '';

    public bool $showForm = false;

    public function mount(Activity $activity, string $date, string $currentStatus): void
    {
        $this->activity = $activity;
        $this->date = $date;
        $this->status = $currentStatus;
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
    }

    public function save(UpdateActivityStatusAction $action): void
    {
        $this->authorize('updateStatus', $this->activity);

        $this->validate([
            'status' => ['required', 'in:pending,done'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ]);

        $action->execute(
            activity: $this->activity,
            status: $this->status,
            remark: $this->remark ?: null,
            date: $this->date,
        );

        $this->showForm = false;
        $this->remark = '';

        // Tell the parent board to refresh
        $this->dispatch('status-updated');

        session()->flash('success', "Status updated to {$this->status}.");
    }

    public function render(): View
    {
        return view('livewire.activity-status-updater');
    }
}
