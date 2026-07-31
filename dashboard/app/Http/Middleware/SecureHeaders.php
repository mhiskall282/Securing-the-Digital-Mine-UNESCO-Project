<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecureHeaders middleware.
 *
 * Applied globally in bootstrap/app.php.
 *
 * INCLUDED (vs. spatie/laravel-csp):
 * - X-Frame-Options: Sameorigin — prevents clickjacking
 * - X-Content-Type-Options: nosniff — prevents MIME sniffing
 * - Referrer-Policy: strict-origin-when-cross-origin — limits referrer leak
 * - Permissions-Policy: restricts sensitive browser APIs
 *
 * DEFERRED:
 * - Content-Security-Policy: requires nonce injection for inline Tailwind/Alpine;
 *   deferred to next sprint. Livewire 3 uses SPA-style requests that interact
 *   with CSP — needs careful configuration per Livewire docs.
 * - HSTS: added at the web server (Nginx/Render) layer, not application layer.
 */
final class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
