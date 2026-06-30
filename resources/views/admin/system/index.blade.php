@extends('layouts.admin')

@section('page-title', 'System Information')

@section('admin')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">System Information</h1>
            <p class="mt-1 text-sm text-slate-500">Application and server details, plus tools to clear caches.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Application --}}
            <div class="card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-slate-100 p-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg>
                    </span>
                    <h2 class="font-display text-lg font-bold text-ink-900">Application</h2>
                </div>
                <dl class="divide-y divide-slate-100">
                    @foreach ($app as $label => $value)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <dt class="text-sm text-slate-500">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-ink-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Server --}}
            <div class="card overflow-hidden">
                <div class="flex items-center gap-2 border-b border-slate-100 p-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" /></svg>
                    </span>
                    <h2 class="font-display text-lg font-bold text-ink-900">Server details</h2>
                </div>
                <dl class="divide-y divide-slate-100">
                    @foreach ($server as $label => $value)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <dt class="text-sm text-slate-500">{{ $label }}</dt>
                            <dd class="max-w-[60%] truncate text-right text-sm font-semibold text-ink-900" title="{{ $value }}">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        {{-- System cache --}}
        <div class="card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-slate-100 p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </span>
                <h2 class="font-display text-lg font-bold text-ink-900">System cache</h2>
            </div>
            <div class="p-5">
                <ul class="space-y-2 text-sm text-slate-600">
                    @foreach ([
                        'Compiled views will be cleared',
                        'Application cache will be cleared',
                        'Route cache will be cleared',
                        'Configuration cache will be cleared',
                        'All other caches will be cleared',
                        'Error logs file will be cleared',
                    ] as $line)
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            {{ $line }}
                        </li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('admin.system.cache.clear') }}" class="mt-6"
                      onsubmit="return confirm('Clear all caches, compiled views and the error log?');">
                    @csrf
                    <button type="submit" class="btn-primary btn-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        Clear cache
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
