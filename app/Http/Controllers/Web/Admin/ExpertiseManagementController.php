<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expertise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExpertiseManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.expertises.index', [
            'expertises' => Expertise::withCount('astrologers')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.expertises.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expertises'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        Expertise::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.expertises.index')->with('success', 'Expertise created.');
    }

    public function edit(Expertise $expertise): View
    {
        return view('admin.expertises.edit', ['expertise' => $expertise]);
    }

    public function update(Request $request, Expertise $expertise): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expertises,name,' . $expertise->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $expertise->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.expertises.index')->with('success', 'Expertise updated.');
    }

    public function destroy(Expertise $expertise): RedirectResponse
    {
        $expertise->delete();

        return redirect()->route('admin.expertises.index')->with('success', 'Expertise deleted.');
    }
}
