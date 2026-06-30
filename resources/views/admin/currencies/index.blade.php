@extends('layouts.admin')

@section('page-title', 'Currencies')

@section('admin')
<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    {{-- List --}}
    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900">Currencies &amp; exchange rates</h2>
            <p class="mt-1 text-sm text-slate-500">Rates are relative to the default (base) currency. Customers can switch currency on the storefront.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Currency</th>
                        <th class="px-5 py-3 font-semibold">Rate</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Edit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($currencies as $currency)
                        <tr x-data="{ edit: false }">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 font-bold text-brand-600">{{ $currency->symbol }}</span>
                                    <div>
                                        <p class="font-semibold text-ink-900">{{ $currency->code }}
                                            @if ($currency->is_default)<span class="chip ml-1 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">Default</span>@endif
                                        </p>
                                        <p class="text-xs text-slate-400">{{ $currency->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ rtrim(rtrim(number_format($currency->exchange_rate, 6), '0'), '.') }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$currency->is_active ? 'published' : 'archived'" /></td>
                            <td class="px-5 py-3 text-right">
                                <button @click="edit = !edit" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Edit</button>
                            </td>

                            {{-- Inline edit row --}}
                            <td x-show="edit" x-cloak colspan="4" class="bg-slate-50 px-5 py-4">
                                <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="grid items-end gap-3 sm:grid-cols-5">
                                    @csrf @method('PUT')
                                    <div><label class="label">Code</label><input name="code" value="{{ $currency->code }}" class="input"></div>
                                    <div><label class="label">Name</label><input name="name" value="{{ $currency->name }}" class="input"></div>
                                    <div><label class="label">Symbol</label><input name="symbol" value="{{ $currency->symbol }}" class="input"></div>
                                    <div><label class="label">Rate</label><input name="exchange_rate" type="number" step="0.000001" value="{{ $currency->exchange_rate }}" class="input" {{ $currency->is_default ? 'readonly' : '' }}></div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn-primary btn-sm flex-1">Save</button>
                                    </div>
                                    <div class="sm:col-span-5 flex items-center gap-4">
                                        <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_default" value="1" {{ $currency->is_default ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Default (base)</label>
                                        <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_active" value="1" {{ $currency->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Active</label>
                                    </div>
                                </form>
                                @unless ($currency->is_default)
                                    <form method="POST" action="{{ route('admin.currencies.destroy', $currency) }}" class="mt-3" onsubmit="return confirm('Remove this currency?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete currency</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add --}}
    <div class="card h-fit p-6">
        <h2 class="font-display text-base font-bold text-ink-900">Add currency</h2>
        <form method="POST" action="{{ route('admin.currencies.store') }}" class="mt-4 space-y-4">
            @csrf
            <div><label class="label">Code (e.g. CAD)</label><input name="code" required class="input" placeholder="CAD"></div>
            <div><label class="label">Name</label><input name="name" required class="input" placeholder="Canadian Dollar"></div>
            <div><label class="label">Symbol</label><input name="symbol" required class="input" placeholder="C$"></div>
            <div><label class="label">Exchange rate (per 1 base)</label><input name="exchange_rate" type="number" step="0.000001" required class="input" placeholder="1.36"></div>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Active</label>
            <button type="submit" class="btn-primary btn-md w-full">Add currency</button>
        </form>
    </div>
</div>
@endsection
