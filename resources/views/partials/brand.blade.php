@php $logo = setting('site_logo'); $name = setting('site_name', 'AkzoneScripts'); @endphp
@if ($logo)
    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo) }}" alt="{{ $name }}" class="h-9 w-auto">
@else
    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-lg font-extrabold text-white">{{ strtoupper(substr($name, 0, 1)) }}</span>
    <span class="font-display text-xl font-extrabold {{ $brandTextClass ?? 'text-ink-900' }}">{{ $name }}</span>
@endif
