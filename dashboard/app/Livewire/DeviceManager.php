<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Device;
use Illuminate\Support\Str;
use Livewire\Component;

class DeviceManager extends Component
{
    public string $name = '';

    public function registerDevice(): void
    {
        $this->validate([
            'name' => 'required|string|min:3|max:100',
        ]);

        $token = 'unesco_device_' . Str::random(32);

        Device::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $this->name,
            'api_token' => $token,
        ]);

        $this->name = '';
        session()->flash('message', 'Device registered successfully. Copy your API token: ' . $token);
    }

    public function deleteDevice(int $id): void
    {
        Device::where('organization_id', auth()->user()->organization_id)
            ->where('id', $id)
            ->delete();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $devices = Device::where('organization_id', auth()->user()->organization_id)
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.device-manager', [
            'devices' => $devices
        ])->layout('layouts.app');
    }
}
