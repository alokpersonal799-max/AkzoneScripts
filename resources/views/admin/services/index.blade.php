@extends('layouts.admin')

@section('page-title', 'Services')

@section('admin')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Services</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $services->count() }} service(s). These show on the public Services page.</p>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn-primary btn-md">Add service</a>
</div>

{{-- Visibility toggle --}}
<form method="POST" action="{{ route('admin.services.settings') }}" class="card mb-6 flex items-center justify-between gap-4 p-5">
    @csrf @method('PUT')
    <div>
        <p class="font-semibold text-ink-900">Show Services on the site</p>
        <p class="mt-0.5 text-sm text-slate-500">When off, the Services nav link is hidden and the page shows a "coming soon" message.</p>
    </div>
    <label class="relative inline-flex cursor-pointer items-center">
        <input type="checkbox" name="services_enabled" value="1" {{ $servicesEnabled ? 'checked' : '' }} class="peer sr-only" onchange="this.form.submit()">
        <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full"></div>
    </label>
</form>

<div class="card overflow-hidden">
    <div class="divide-y divide-slate-100">
        @forelse ($services as $service)
            <div class="flex items-center justify-between gap-4 p-4">
                <div class="flex items-center gap-3">
                    <img src="{{ $service->avatar_url }}" alt="" class="h-11 w-11 flex-shrink-0 rounded-xl object-cover">
                    <div>
                        <p class="font-semibold text-ink-900">{{ $service->name }}</p>
                        <p class="text-xs text-slate-400">{{ $service->provider_label }} · {{ ucfirst($service->provider_type) }} provider</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($service->is_active)
                        <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Active</span>
                    @else
                        <span class="chip bg-slate-100 text-slate-500">Hidden</span>
                    @endif
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn-ghost btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Delete this service?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-slate-400">No services yet. Click "Add service" to create one.</p>
        @endforelse
    </div>
</div>
@endsection
