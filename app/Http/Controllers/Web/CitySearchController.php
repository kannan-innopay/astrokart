<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CitySearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
            'country' => ['nullable', 'string', 'size:2'],
        ]);

        $query = City::where('name', 'like', $request->string('q') . '%')
            ->when($request->country, fn ($q, $country) => $q->where('country_code', $country))
            ->orderByRaw("CASE WHEN country_code = 'IN' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'state_name', 'country_code', 'latitude', 'longitude']);

        return response()->json($query);
    }
}
