<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display a published custom page by slug.
     */
    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('pages.show', compact('page'));
    }
}
