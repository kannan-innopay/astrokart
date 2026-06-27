<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAstrologerProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // An astrologer-role user may exist without a profile (verified OTP but
        // did not finish the application). Send them to complete it.
        if (! $request->user()?->astrologerProfile) {
            return redirect()->route('astrologer.register.profile');
        }

        return $next($request);
    }
}
