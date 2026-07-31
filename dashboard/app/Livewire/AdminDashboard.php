<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Organization;
use App\Models\Device;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function mount(): void
    {
        if (!auth()->check()) {
            redirect()->route('login');
        }
    }

    public function toggleUserAdmin(int $id): void
    {
        $user = User::findOrFail($id);
        $user->is_admin = !$user->is_admin;
        $user->save();
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        if (auth()->id() === $user->id) {
            return;
        }
        $user->delete();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $organizations = Organization::withCount(['users', 'devices'])->orderBy('id', 'desc')->get();
        $users = User::with('organization')->orderBy('id', 'desc')->get();
        $devices = Device::with('organization')->orderBy('id', 'desc')->get();

        return view('livewire.admin-dashboard', [
            'organizations' => $organizations,
            'users' => $users,
            'devices' => $devices
        ])->layout('layouts.app');
    }
}
