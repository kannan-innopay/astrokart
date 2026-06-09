<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DetectNativePHP
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isNative = $this->isRunningInNativePHP()
            || $request->hasHeader('X-NativePHP')
            || $request->has('_native')
            || str_contains($request->userAgent() ?? '', 'NativePHP');

        View::share('isNativeApp', $isNative);
        $request->attributes->set('isNativeApp', $isNative);

        return $next($request);
    }

    private function isRunningInNativePHP(): bool
    {
        return str_contains(base_path(), 'app_storage')
            || env('NATIVEPHP_RUNNING', false);
    }
}
