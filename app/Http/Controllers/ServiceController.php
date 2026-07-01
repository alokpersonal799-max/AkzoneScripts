<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\ContactMessage;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Public services listing page.
     */
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    /**
     * Handle a service inquiry — stored as a contact message for the admin.
     */
    public function inquiry(Request $request, Service $service): RedirectResponse
    {
        if (filled($request->input('website'))) { // honeypot
            return back()->with('success', 'Thanks! Your message has been sent.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => 'Service inquiry: '.$service->name,
            'message' => $data['message'],
            'ip' => $request->ip(),
        ]);

        AdminNotification::notifyAdmins(
            'contact',
            'New service inquiry',
            $contact->name.' — '.Str::limit($service->name, 50),
            route('admin.contacts.show', $contact)
        );

        return back()->with('success', 'Thanks for your inquiry! We will get back to you soon.');
    }
}
