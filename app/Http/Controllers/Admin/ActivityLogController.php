<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::latest();

        if ($request->filled('action') && in_array($request->input('action'), ['created', 'updated', 'deleted'], true)) {
            $query->where('action', $request->input('action'));
        }
        if ($request->filled('type')) {
            $query->where('subject_type', $request->input('type'));
        }

        return view('admin.activity.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'types' => ActivityLog::query()->distinct()->orderBy('subject_type')->pluck('subject_type'),
            'filterAction' => $request->input('action'),
            'filterType' => $request->input('type'),
        ]);
    }

    /**
     * Clear the whole audit log.
     */
    public function clear(): RedirectResponse
    {
        ActivityLog::truncate();

        return back()->with('success', 'Activity log cleared.');
    }
}
