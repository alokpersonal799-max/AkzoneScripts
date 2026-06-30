<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Mark a notification read and redirect to its target.
     */
    public function read(AdminNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return redirect($notification->url ?: route('admin.dashboard'));
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll(): RedirectResponse
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
