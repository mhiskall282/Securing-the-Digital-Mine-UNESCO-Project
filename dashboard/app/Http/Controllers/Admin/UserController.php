<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Notifications\AdminPasswordResetNotification;
use App\Notifications\WelcomeNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    /**
     * Store a new user and dispatch a Welcome notification with their
     * temporary password and a direct login link.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        // Preserve the plain-text password before hashing so we can
        // include it in the welcome email (one-time visibility).
        $temporaryPassword = $data['password'];
        $data['password'] = Hash::make($temporaryPassword);

        $user = User::create($data);

        $this->auditService->log(
            subject: $user,
            event: 'created',
            newValues: collect($data)->except('password')->toArray(),
        );

        // ── Welcome notification ─────────────────────────────────────────
        try {
            $user->notify(new WelcomeNotification(
                temporaryPassword: $temporaryPassword,
                loginUrl: route('login'),
            ));
        } catch (\Throwable) {
            // Mail failure must never abort the create flow.
            // The admin will see a secondary warning in the flash message.
            return redirect()
                ->route('admin.users.index')
                ->with('warning', "User \"{$user->name}\" created, but the welcome email could not be sent. Check your mail configuration.");
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created and a welcome email has been sent to {$user->email}.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT / UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $old = $user->only(['name', 'email', 'role', 'designation', 'phone']);
        $user->update($request->validated());

        $this->auditService->log(
            subject: $user,
            event: 'updated',
            oldValues: $old,
            newValues: $request->validated(),
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN-INITIATED PASSWORD RESET
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate a secure password-reset token for the target user and email
     * them a time-limited reset link.
     *
     * The admin NEVER sees the new password — the user sets it themselves.
     * Token validity: 60 minutes (configured in config/auth.php).
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Generate a secure password-reset token via Laravel's password broker.
        $token = Password::broker()->createToken($user);

        // Build the signed reset URL (same URL the forgot-password form uses).
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], absolute: false));

        // Notify the user via email.
        try {
            $user->notify(new AdminPasswordResetNotification(
                resetUrl: $resetUrl,
                adminName: auth()->user()->name,
            ));
        } catch (\Throwable) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "Could not send password-reset email to {$user->email}. Check mail configuration.");
        }

        $this->auditService->log(
            subject: $user,
            event: 'password_reset_requested',
            newValues: ['initiated_by' => auth()->user()->name],
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "A password-reset link has been sent to {$user->name} ({$user->email}). The link expires in 60 minutes.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->auditService->log(
            subject: $user,
            event: 'deleted',
            oldValues: $user->only(['name', 'email', 'role', 'designation']),
        );

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" has been removed.");
    }
}
