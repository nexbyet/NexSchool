<?php

// NexSchool - CheckRole Middleware
// Verify user has the required role (admin/teacher/staff)
// Example: Route::middleware('role:admin')->group(...)

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()->role !== $role) {
            abort(403, 'ફક્ત એડમિન જ આ પેજ જોઈ શકે છે.');
        }
        return $next($request);
    }
}
