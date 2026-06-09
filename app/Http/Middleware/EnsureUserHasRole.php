<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role;

        if (! $userRole) {
            abort(403, 'Unauthorized.');
        }

        $allowed = array_map(fn (string $r) => UserRole::from($r), $roles);

        if (! in_array($userRole, $allowed)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
