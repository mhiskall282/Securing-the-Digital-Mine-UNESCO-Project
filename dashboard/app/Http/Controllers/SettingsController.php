<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function edit(Request $request): View
    {
        $this->authorize('update', $request->user());
        $user = $request->user();

        return view('settings.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:150'],
        ]);

        $old = $user->only(['name', 'email', 'phone', 'designation']);
        $user->update($validated);

        $this->auditService->log($user, 'profile_updated', $old, $validated);

        return redirect()->route('settings.edit')->with('success', 'Profile settings updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        $this->auditService->log($user, 'password_changed');

        return redirect()->route('settings.edit')->with('success', 'Password updated successfully.');
    }
}
