<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('admin.settings.show', 'pages');
    }

    public function create(): View
    {
        return view('admin.pages.edit', ['page' => new Page(['is_published' => true, 'show_in_footer' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Page::create($this->validatePage($request));

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validatePage($request));

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePage(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'show_in_footer' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['show_in_footer'] = $request->boolean('show_in_footer');

        return $data;
    }
}
