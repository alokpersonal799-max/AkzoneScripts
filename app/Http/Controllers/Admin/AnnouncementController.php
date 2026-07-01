<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::withCount(['recipients', 'replies', 'recipients as read_count' => fn ($q) => $q->whereNotNull('read_at')])
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => Announcement::count(),
            'sent' => Announcement::where('status', 'sent')->count(),
            'scheduled' => Announcement::where('status', 'scheduled')->count(),
            'replies' => \App\Models\AnnouncementReply::where('is_admin', false)->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'themes' => Announcement::themes(),
            'users' => User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'theme' => ['required', 'string', 'in:'.implode(',', array_keys(Announcement::themes()))],
            'audience' => ['required', 'in:all,selected'],
            'user_ids' => ['required_if:audience,selected', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'action_url' => ['nullable', 'url', 'max:255'],
            'schedule' => ['required', 'in:now,later'],
            'scheduled_at' => ['required_if:schedule,later', 'nullable', 'date', 'after:now'],
            'allow_reply' => ['nullable', 'boolean'],
            'reply_types' => ['nullable', 'array'],
            'reply_types.*' => ['in:star,emoji,message,media'],
        ]);

        $allowReply = $request->boolean('allow_reply');

        $announcement = Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'theme' => $data['theme'],
            'audience' => $data['audience'],
            'status' => 'draft',
            'scheduled_at' => $data['schedule'] === 'later' ? $data['scheduled_at'] : null,
            'allow_reply' => $allowReply,
            'reply_types' => $allowReply ? ($data['reply_types'] ?? []) : [],
            'action_url' => $data['action_url'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        // Selected audience: attach chosen recipients now so scheduled sends know who to notify.
        if ($data['audience'] === 'selected') {
            $now = now();
            $rows = collect($data['user_ids'])->unique()->map(fn ($id) => [
                'announcement_id' => $announcement->id,
                'user_id' => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();
            AnnouncementRecipient::insertOrIgnore($rows);
        }

        if ($data['schedule'] === 'now') {
            $announcement->send();
            $msg = 'Announcement sent.';
        } else {
            $announcement->update(['status' => 'scheduled']);
            $msg = 'Announcement scheduled for '.$announcement->scheduled_at->format('M j, Y g:i A').'.';
        }

        return redirect()->route('admin.announcements.index')->with('success', $msg);
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load(['product', 'creator']);
        $announcement->loadCount(['recipients', 'recipients as read_count' => fn ($q) => $q->whereNotNull('read_at')]);

        $replies = $announcement->replies()->with('user')->latest()->paginate(30);

        return view('admin.announcements.show', compact('announcement', 'replies'));
    }

    /**
     * Admin posts a reply into the announcement thread (visible to recipients).
     */
    public function reply(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $announcement->replies()->create([
            'user_id' => $request->user()->id,
            'is_admin' => true,
            'type' => 'message',
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Reply posted.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
