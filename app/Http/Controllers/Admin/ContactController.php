<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * List contact messages (pruning any that have outlived the retention period).
     */
    public function index(): View
    {
        ContactMessage::pruneExpired();

        return view('admin.contacts.index', [
            'messages' => ContactMessage::latest()->paginate(15),
            'unreadCount' => ContactMessage::whereNull('read_at')->count(),
            'autoDeleteDays' => (int) Setting::get('contact_autodelete_days', 0),
        ]);
    }

    /**
     * Show a single message (and mark it read).
     */
    public function show(ContactMessage $contact): View
    {
        if (! $contact->isRead()) {
            $contact->update(['read_at' => now()]);
        }

        return view('admin.contacts.show', ['message' => $contact]);
    }

    /**
     * Delete a single message.
     */
    public function destroy(ContactMessage $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted.');
    }

    /**
     * Save the auto-delete retention period (in days; 0 disables it).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contact_autodelete_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
        ]);

        Setting::put('contact_autodelete_days', (string) ($data['contact_autodelete_days'] ?? 0), 'contact');

        ContactMessage::pruneExpired();

        return back()->with('success', 'Auto-delete settings saved.');
    }
}
