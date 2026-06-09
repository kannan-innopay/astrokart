<?php

use App\Http\Controllers\Web\Admin\AstrologerManagementController as AdminAstrologerController;
use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\ExpertiseManagementController;
use App\Http\Controllers\Web\Admin\LanguageManagementController;
use App\Http\Controllers\Web\Admin\UserManagementController;
use App\Http\Controllers\Web\Astrologer\AvailabilityController;
use App\Http\Controllers\Web\Astrologer\ConsultationController as AstrologerConsultationController;
use App\Http\Controllers\Web\Astrologer\DashboardController as AstrologerDashboardController;
use App\Http\Controllers\Web\Astrologer\OnlineStatusController;
use App\Http\Controllers\Web\Astrologer\ProfileController as AstrologerProfileController;
use App\Http\Controllers\Web\Auth\AdminLoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\Auth\OtpLoginController;
use App\Http\Controllers\Web\Customer\AstrologerBrowseController;
use App\Http\Controllers\Web\Customer\ConsultationController;
use App\Http\Controllers\Web\Customer\HomeController;
use App\Http\Controllers\Web\Customer\HoraController;
use App\Http\Controllers\Web\Customer\OnboardingController;
use App\Http\Controllers\Web\Customer\ProfileController;
use App\Http\Controllers\Web\Customer\HoroscopeController;
use App\Http\Controllers\Web\Customer\TransitController;
use App\Http\Controllers\Web\CitySearchController;
use App\Http\Controllers\Web\Customer\AnalysisController;
use App\Http\Controllers\Web\Customer\CompatibilityController;
use App\Http\Controllers\Web\Customer\DailyPredictionController;
use App\Http\Controllers\Web\Customer\MonthlyForecastController;
use App\Http\Controllers\Web\Customer\MuhurthamController;
use App\Http\Controllers\Web\Customer\SubscriptionController;
use App\Http\Controllers\Web\Customer\WalletController;
use Illuminate\Support\Facades\Route;

// --- Auth ---
Route::middleware('guest')->group(function () {
    Route::get('login', [OtpLoginController::class, 'showForm'])->name('login');
    Route::post('login/otp/request', [OtpLoginController::class, 'requestOtp'])->name('login.otp.request');
    Route::post('login/otp/verify', [OtpLoginController::class, 'verifyOtp'])->name('login.otp.verify');

    Route::get('admin/login', [AdminLoginController::class, 'showForm'])->name('admin.login');
    Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
});

Route::post('logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

// Payment callback (CSRF-exempt, called by SwitchPay)
Route::match(['get', 'post'], 'wallet/callback', [WalletController::class, 'callback'])->name('wallet.callback');

// --- Customer (/) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('astrologers', [AstrologerBrowseController::class, 'index'])->name('astrologers.index');
Route::get('astrologers/{astrologer}', [AstrologerBrowseController::class, 'show'])->name('astrologers.show');

Route::get('cities/search', CitySearchController::class)->name('cities.search');

Route::middleware('auth')->group(function () {
    Route::get('onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet/recharge', [WalletController::class, 'recharge'])->name('wallet.recharge');
    Route::get('horoscope', [HoroscopeController::class, 'show'])->name('horoscope.show');
    Route::post('horoscope/regenerate', [HoroscopeController::class, 'regenerate'])->name('horoscope.regenerate');
    Route::get('horoscope/transits', [TransitController::class, 'index'])->name('horoscope.transits');
    Route::get('horoscope/hora', [HoraController::class, 'index'])->name('horoscope.hora');
    Route::get('horoscope/analysis', [AnalysisController::class, 'show'])->name('horoscope.analysis');

    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('subscription', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Muhurtham Finder
    Route::get('muhurtham', [MuhurthamController::class, 'index'])->name('muhurtham.index');
    Route::post('muhurtham', [MuhurthamController::class, 'search'])->name('muhurtham.search');
    Route::get('muhurtham/{muhurthamRequest}', [MuhurthamController::class, 'show'])->name('muhurtham.show');

    // Predictions
    Route::get('predictions/daily', [DailyPredictionController::class, 'show'])->name('predictions.daily');
    Route::get('predictions/monthly', [MonthlyForecastController::class, 'show'])->name('predictions.monthly');

    // Compatibility
    Route::get('compatibility', [CompatibilityController::class, 'index'])->name('compatibility.index');
    Route::post('compatibility', [CompatibilityController::class, 'match'])->name('compatibility.match');
    Route::get('compatibility/{compatibilityReport}', [CompatibilityController::class, 'show'])->name('compatibility.show');

    // Consultations
    Route::post('consultation/start/{astrologer}', [ConsultationController::class, 'start'])->name('consultation.start');
    Route::get('consultation/{consultation}', [ConsultationController::class, 'chat'])->name('consultation.chat');
    Route::post('consultation/{consultation}/end', [ConsultationController::class, 'end'])->name('consultation.end');
    Route::post('consultation/{consultation}/send', [ConsultationController::class, 'send'])->name('consultation.send');
    Route::post('consultation/{consultation}/request-chart', [ConsultationController::class, 'requestChart'])->name('consultation.request-chart');
    Route::post('consultation/{consultation}/share-chart', [ConsultationController::class, 'shareChart'])->name('consultation.share-chart');
    Route::get('consultation/{consultation}/chart-view/{message}', [ConsultationController::class, 'chartView'])->name('consultation.chart-view');
    Route::post('consultation/{consultation}/rate', [ConsultationController::class, 'rate'])->name('consultation.rate');
    Route::get('consultations', [ConsultationController::class, 'history'])->name('consultations.history');
});

// --- Astrologer (/astrologer) ---
Route::prefix('astrologer')->name('astrologer.')->middleware(['auth', 'role:astrologer'])->group(function () {
    Route::get('dashboard', [AstrologerDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [AstrologerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [AstrologerProfileController::class, 'update'])->name('profile.update');
    Route::get('availability', [AvailabilityController::class, 'edit'])->name('availability.edit');
    Route::put('availability', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::post('go-online', [OnlineStatusController::class, 'goOnline'])->name('go-online');
    Route::post('go-offline', [OnlineStatusController::class, 'goOffline'])->name('go-offline');
    Route::post('consultation/{consultation}/accept', [AstrologerConsultationController::class, 'accept'])->name('consultation.accept');
    Route::post('consultation/{consultation}/reject', [AstrologerConsultationController::class, 'reject'])->name('consultation.reject');
});

// --- Admin (/admin) ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super_admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.update-status');

    Route::get('astrologers', [AdminAstrologerController::class, 'index'])->name('astrologers.index');
    Route::get('astrologers/{astrologer}', [AdminAstrologerController::class, 'show'])->name('astrologers.show');
    Route::patch('astrologers/{astrologer}/status', [AdminAstrologerController::class, 'updateStatus'])->name('astrologers.update-status');

    Route::resource('expertises', ExpertiseManagementController::class)->except(['show']);
    Route::resource('languages', LanguageManagementController::class)->except(['show']);
});
