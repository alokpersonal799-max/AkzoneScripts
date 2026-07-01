<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ManualController extends Controller
{
    /**
     * Show the installation & business setup manual for the script.
     */
    public function index(): View
    {
        $base = base_path();
        $php = PHP_BINARY && ! str_contains(strtolower(PHP_BINARY), 'fpm') ? PHP_BINARY : 'php';

        return view('admin.manual.index', [
            'basePath' => $base,
            'appUrl' => config('app.url'),
            'cronCommand' => '* * * * * cd '.$base.' && '.$php.' artisan schedule:run >> /dev/null 2>&1',
        ]);
    }
}
