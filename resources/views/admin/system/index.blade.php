@extends('layouts.admin')

@section('page-title', 'System Information')

@section('admin')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">System Information</h1>
            <p class="mt-1 text-sm text-slate-500">Application and server details, health monitoring, and system tools.</p>
        </div>

        {{-- Health Monitoring --}}
        <div class="card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-slate-100 p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-ink-900">Health Monitoring</h2>
                    <p class="text-xs text-slate-400">Real-time system health checks</p>
                </div>
                @php
                    $okCount = collect($health)->where('status', 'ok')->count();
                    $totalCount = count($health);
                    $hasErrors = collect($health)->where('status', 'error')->isNotEmpty();
                    $hasWarnings = collect($health)->where('status', 'warning')->isNotEmpty();
                @endphp
                <span class="ml-auto inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $hasErrors ? 'bg-rose-50 text-rose-700' : ($hasWarnings ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                    <span class="h-2 w-2 rounded-full {{ $hasErrors ? 'bg-rose-500' : ($hasWarnings ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                    {{ $okCount }}/{{ $totalCount }} Healthy
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($health as $key => $check)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            @if ($check['status'] === 'ok')
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50">
                                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                </span>
                            @elseif ($check['status'] === 'warning')
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-50">
                                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                </span>
                            @else
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-rose-50">
                                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </span>
                            @endif
                            <dt class="text-sm font-medium text-ink-900">{{ $check['label'] }}</dt>
                        </div>
                        <dd class="text-sm {{ $check['status'] === 'ok' ? 'text-slate-600' : ($check['status'] === 'warning' ? 'text-amber-600' : 'text-rose-600') }}">{{ $check['detail'] }}</dd>
                    </div>
                @endforeach
            </div>
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

        {{-- Error Log Viewer --}}
        <div class="card overflow-hidden" x-data="{ showLog: false }">
            <div class="flex items-center gap-2 border-b border-slate-100 p-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" /></svg>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-ink-900">Error Log</h2>
                    <p class="text-xs text-slate-400">Size: {{ $errorLog['size'] }}</p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    @if ($errorLog['exists'])
                        <button @click="showLog = !showLog" class="btn-ghost btn-sm" x-text="showLog ? 'Hide Log' : 'View Log'">View Log</button>
                        <form method="POST" action="{{ route('admin.system.error-log.clear') }}" onsubmit="return confirm('Clear the error log file?');">
                            @csrf
                            <button type="submit" class="btn btn-sm bg-rose-500 text-white hover:bg-rose-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                Clear Log
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs text-emerald-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            No errors logged
                        </span>
                    @endif
                </div>
            </div>
            @if ($errorLog['exists'] && count($errorLog['last_entries']))
                <div x-show="showLog" x-cloak class="border-t border-slate-100 bg-slate-900 p-4">
                    <p class="mb-2 text-xs text-slate-400">Last 50 lines:</p>
                    <pre class="max-h-96 overflow-auto rounded-lg bg-slate-950 p-4 font-mono text-xs leading-5 text-slate-300">@foreach ($errorLog['last_entries'] as $line){{ e($line) }}
@endforeach</pre>
                </div>
            @endif
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
                        Clear all caches
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
