<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstall
{
    public function handle(Request $request, Closure $next): Response
    {
        // Always ensure APP_KEY exists (needed for encrypter, cookies, sessions)
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=']);
        }

        $isInstalled = file_exists(base_path('.env')) && file_exists(storage_path('installed.lock'));
        $isInstallRoute = $request->routeIs('install.*');
        $isCompleteRoute = $request->routeIs('install.complete');

        // install.complete is read-only — always allow (safe after install)
        if ($isCompleteRoute) {
            config(['session.driver' => 'file']);
            return $next($request);
        }

        // Install routes — blocked after installation (security)
        if ($isInstallRoute) {
            if ($isInstalled) {
                abort(404);
            }
            config(['session.driver' => 'file']);
            return $next($request);
        }

        // Non-install routes — redirect to installer if not installed
        if (!$isInstalled) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}
