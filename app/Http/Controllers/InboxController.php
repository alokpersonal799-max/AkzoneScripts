<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    /**
     * List the announcements delivered to the signed-in user.
     */
    public function index(Request $request): View
    {
        $recipients = AnnouncementRecipient::where('user_id', $request->user()->id)
            ->whereHas('announcement', fn ($q) => $q->where('status', 'sent'))
            ->with('announcement')
            ->join('announcements', 'announcements.id', '=', 'announcement_recipients.announcement_id')
            ->orderByDesc('announcements.sent_at')
            ->select('announcement_recipients.*')
            ->paginate(12);

        $unread = AnnouncementRecipient::where('user_id', $request->user()->id)->whereNull('read_at')->count();

        return view('dashboard.inbox.index', compact('recipients', 'unread'));
    }

    /**
     * Show a single announcement and its conversation.
     */
    public function show(Request $request, Announcement $announcement): View
    {
        $recipient = AnnouncementRecipient::where('user_id', $request->user()->id)
            ->where('announcement_id', $announcement->id)
            ->firstOrFail();

        // Mark as read the first time it is opened.
        if (! $recipient->read_at) {
            $recipient->update(['read_at' => now()]);
        }

        $announcement->load('product');
        $replies = $announcement->replies()->with('user')->oldest()->get();

        // The user's own existing reply (if any) so the UI can reflect it.
        $myReply = $announcement->replies()
            ->where('user_id', $request->user()->id)
            ->where('is_admin', false)
            ->latest()
            ->first();

        return view('dashboard.inbox.show', compact('announcement', 'replies', 'myReply'));
    }

    /**
     * Store the user's reply to an announcement (star / emoji / message / media).
     */
    public function reply(Request $request, Announcement $announcement): RedirectResponse
    {
        // Ensure the announcement was delivered to this user.
        AnnouncementRecipient::where('user_id', $request->user()->id)
            ->where('announcement_id', $announcement->id)
            ->firstOrFail();

        if (! $announcement->allow_reply) {
            return back()->with('error', 'Replies are not enabled for this announcement.');
        }

        $type = $request->input('type');

        if (! $announcement->allowsReplyType($type)) {
            return back()->with('error', 'That reply type is not allowed here.');
        }

        $rules = ['type' => ['required', 'in:star,emoji,message,media']];
        match ($type) {
            'star' => $rules['rating'] = ['required', 'integer', 'min:1', 'max:5'],
            'emoji' => $rules['emoji'] = ['required', 'string', 'max:16'],
            'message' => $rules['message'] = ['required', 'string', 'max:2000'],
            'media' => $rules['media'] = ['required', 'image', 'max:4096'],
            default => null,
        };
        $validated = $request->validate($rules);

        $payload = [
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'type' => $type,
            'rating' => $type === 'star' ? $validated['rating'] : null,
            'emoji' => $type === 'emoji' ? $validated['emoji'] : null,
            'message' => $type === 'message' ? $validated['message'] : null,
        ];

        if ($type === 'media' && $request->hasFile('media')) {
            $payload['media_path'] = $request->file('media')->store('announcement-replies', 'public');
        }

        $announcement->replies()->create($payload);

        \App\Models\AdminNotification::notifyAdmins(
            'announcement',
            'New reply to "'.$announcement->title.'"',
            $request->user()->name.' responded to your announcement.',
            route('admin.announcements.show', $announcement)
        );

        return back()->with('success', 'Thanks for your feedback!');
    }
}
