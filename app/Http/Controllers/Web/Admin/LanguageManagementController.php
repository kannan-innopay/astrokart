<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.languages.index', [
            'languages' => Language::withCount('astrologers')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.languages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:languages'],
            'code' => ['required', 'string', 'max:10', 'unique:languages'],
        ]);

        Language::create($validated);

        return redirect()->route('admin.languages.index')->with('success', 'Language created.');
    }

    public function edit(Language $language): View
    {
        return view('admin.languages.edit', ['language' => $language]);
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:languages,name,' . $language->id],
            'code' => ['required', 'string', 'max:10', 'unique:languages,code,' . $language->id],
        ]);

        $language->update($validated);

        return redirect()->route('admin.languages.index')->with('success', 'Language updated.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        $language->delete();

        return redirect()->route('admin.languages.index')->with('success', 'Language deleted.');
    }
}
