<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create', ['service' => new Service(['provider_type' => 'admin', 'use_global_contact' => true, 'allow_inquiry' => true, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateService($request);
        $data = $this->handleAvatar($request, $data);

        $service = Service::create($data);

        AdminNotification::notifyAdmins('product', 'New service added', $service->name, route('admin.services.index'));

        return redirect()->route('admin.services.index')->with('success', 'Service "'.$service->name.'" created.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validateService($request);
        $data = $this->handleAvatar($request, $data, $service);

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->provider_avatar) {
            Storage::disk('public')->delete($service->provider_avatar);
        }
        $service->delete();

        return back()->with('success', 'Service deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateService(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:3000'],
            'provider_type' => ['required', 'in:admin,custom'],
            'provider_name' => ['nullable', 'string', 'max:150', 'required_if:provider_type,custom'],
            'provider_avatar' => ['nullable', 'image', 'max:2048'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'telegram' => ['nullable', 'string', 'max:100'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'discord' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'custom_label' => ['nullable', 'string', 'max:60'],
            'custom_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['use_global_contact'] = $request->boolean('use_global_contact');
        $data['allow_inquiry'] = $request->boolean('allow_inquiry');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function handleAvatar(Request $request, array $data, ?Service $service = null): array
    {
        if ($request->hasFile('provider_avatar')) {
            if ($service && $service->provider_avatar) {
                Storage::disk('public')->delete($service->provider_avatar);
            }
            $data['provider_avatar'] = $request->file('provider_avatar')->store('services', 'public');
        } else {
            unset($data['provider_avatar']);
        }

        return $data;
    }
}
