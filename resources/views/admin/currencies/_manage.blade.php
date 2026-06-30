<div class="space-y-6">
    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-5">
            <h2 class="font-display text-lg font-bold text-ink-900">Currencies &amp; exchange rates</h2>
            <p class="mt-1 text-sm text-slate-500">Rates are relative to the default (base) currency. The default currency is what customers see by default; they can switch on the storefront.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach ($currencies as $currency)
                <div x-data="{ edit: false }" class="p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 font-bold text-brand-600">{{ $currency->symbol }}</span>
                            <div>
                                <p class="font-semibold text-ink-900">{{ $currency->code }}
                                    @if ($currency->is_default)<span class="chip ml-1 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">Default</span>@endif
                                    <x-status-badge :status="$currency->is_active ? 'published' : 'archived'" class="ml-1" />
                                </p>
                                <p class="text-xs text-slate-400">{{ $currency->name }} · rate {{ rtrim(rtrim(number_format($currency->exchange_rate, 6), '0'), '.') }}</p>
                            </div>
                        </div>
                        <button type="button" @click="edit = !edit" class="btn-ghost btn-sm">Edit</button>
                    </div>

                    <div x-show="edit" x-cloak class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <div><label class="label">Code</label><input name="code" value="{{ $currency->code }}" class="input"></div>
                                <div><label class="label">Name</label><input name="name" value="{{ $currency->name }}" class="input"></div>
                                <div><label class="label">Symbol</label><input name="symbol" value="{{ $currency->symbol }}" class="input"></div>
                                <div><label class="label">Rate (per 1 base)</label><input name="exchange_rate" type="number" step="0.000001" value="{{ $currency->exchange_rate }}" class="input" {{ $currency->is_default ? 'readonly' : '' }}></div>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_default" value="1" {{ $currency->is_default ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Set as default (base)</label>
                                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_active" value="1" {{ $currency->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Active</label>
                                <button type="submit" class="btn-primary btn-sm ml-auto">Save changes</button>
                            </div>
                        </form>
                        @unless ($currency->is_default)
                            <form method="POST" action="{{ route('admin.currencies.destroy', $currency) }}" class="mt-3 border-t border-slate-200 pt-3" onsubmit="return confirm('Remove this currency?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete currency</button>
                            </form>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Add currency --}}
    <div class="card p-6">
        <h2 class="font-display text-base font-bold text-ink-900">Add a currency</h2>
        <form method="POST" action="{{ route('admin.currencies.store') }}" class="mt-4 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div><label class="label">Code</label><input name="code" required class="input" placeholder="CAD"></div>
                <div><label class="label">Name</label><input name="name" required class="input" placeholder="Canadian Dollar"></div>
                <div><label class="label">Symbol</label><input name="symbol" required class="input" placeholder="C$"></div>
                <div><label class="label">Rate (per 1 base)</label><input name="exchange_rate" type="number" step="0.000001" required class="input" placeholder="1.36"></div>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Active</label>
                <button type="submit" class="btn-primary btn-md ml-auto">Add currency</button>
            </div>
        </form>
    </div>
</div>
