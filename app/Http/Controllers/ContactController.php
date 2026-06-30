<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Show the public contact form (open to guests and members).
     */
    public function show(): View
    {
        return view('contact');
    }

    /**
     * Store a contact message and notify the admin.
     */
    public function store(Request $request): RedirectResponse
    {
        // Simple honeypot: bots fill hidden fields, humans don't.
        if (filled($request->input('website'))) {
            return back()->with('success', 'Thanks! Your message has been sent.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $data['ip'] = $request->ip();

        $contact = ContactMessage::create($data);

        AdminNotification::notifyAdmins(
            'contact',
            'New contact message',
            $contact->name.' — '.\Illuminate\Support\Str::limit($contact->subject ?: $contact->message, 50),
            route('admin.contacts.show', $contact)
        );

        return back()->with('success', 'Thanks for reaching out! We have received your message and will reply soon.');
    }
}
