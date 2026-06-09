<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ExpertiseResource;
use App\Models\Expertise;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpertiseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ExpertiseResource::collection(
            Expertise::where('is_active', true)->orderBy('name')->get(),
        );
    }
}
