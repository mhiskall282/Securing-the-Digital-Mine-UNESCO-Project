<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureRole middleware.
 *
 * Usage in routes: middleware('role:admin') or middleware('role:admin,lead')
 *
 * This is a secondary guard. Authorization is ALSO enforced in Policies
 * on every controller action. The middleware provides early rejection at
 * the route group level for UX (shows 403 instead of passing request into
 * a controller that will then reject it).
 */
final class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, strict: true)) {
            abort(403, 'Insufficient permissions.');
        }

        return $next($request);
    }
}
