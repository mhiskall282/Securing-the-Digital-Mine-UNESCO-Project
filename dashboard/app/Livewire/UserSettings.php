<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Device;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserSettings extends Component
{
    public string $name = '';
    public string $email = '';
    public string $org_name = '';
    public string $phone = '';
    public string $designation = '';
    
    // Password update fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    
    // Status feedback
    public string $status_message = '';
    public string $status_type = 'success';

    public function mount(): void
    {
        if (!auth()->check()) {
            redirect()->route('login');
            return;
        }

        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->designation = $user->designation ?? '';

        if ($user->organization) {
            $this->org_name = $user->organization->name;
        }
    }

    public function updateProfile(): void
    {
        $user = auth()->user();
        
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'org_name' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'designation' => $this->designation,
        ]);

        if ($user->organization) {
            $user->organization->update([
                'name' => $this->org_name
            ]);
        }

        $this->status_message = 'Profile and organization details updated successfully.';
        $this->status_type = 'success';
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The provided password does not match your current password.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->status_message = 'Password changed successfully.';
        $this->status_type = 'success';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $user = auth()->user();
        $organization = $user ? $user->organization : null;
        $deviceCount = $organization ? Device::where('organization_id', $organization->id)->count() : 0;

        return view('livewire.user-settings', [
            'user' => $user,
            'organization' => $organization,
            'deviceCount' => $deviceCount,
        ])->layout('layouts.app');
    }
}
