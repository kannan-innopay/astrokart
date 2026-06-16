<?php

namespace App\Providers;

use App\Services\Contracts\OtpServiceInterface;
use App\Services\Contracts\SmsSenderInterface;
use App\Services\LogOtpService;
use App\Services\LogSmsSender;
use App\Services\Msg91OtpService;
use App\Services\Msg91SmsSender;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OtpServiceInterface::class, function () {
            return match (config('services.otp.driver')) {
                'msg91' => new Msg91OtpService,
                default => new LogOtpService,
            };
        });

        $this->app->bind(SmsSenderInterface::class, function () {
            return match (config('services.otp.driver')) {
                'msg91' => new Msg91SmsSender,
                default => new LogSmsSender,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('featureAstrologers', config('app.features.astrologers'));
        View::share('companyName', config('app.company'));
        View::share('supportEmail', config('app.support_email'));
    }
}
