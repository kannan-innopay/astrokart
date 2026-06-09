<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NativeAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Disable broadcasting inside NativePHP — no WebSocket server available on mobile
        if ($this->isRunningInNativePHP()) {
            config(['broadcasting.default' => 'log']);
        }
    }

    public function boot(): void
    {
        //
    }

    private function isRunningInNativePHP(): bool
    {
        return str_contains(base_path(), 'app_storage')
            || env('NATIVEPHP_RUNNING', false);
    }
}
