<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\HoraService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HoraController extends Controller
{
    public function __construct(
        private HoraService $horaService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $validLocales = ['en', 'hi', 'ta', 'te', 'ml', 'mr'];
        $locale = in_array($request->query('lang'), $validLocales)
            ? $request->query('lang')
            : ($user?->preferred_language ?? 'en');

        $horaData = $this->horaService->getDailyHoras(user: $user);

        return view('customer.horoscope.hora', [
            'horaData' => $horaData,
            'locale' => $locale,
            'user' => $user,
        ]);
    }
}
