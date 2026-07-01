@php $crypto = json_decode(setting('manual_crypto', '[]'), true) ?: []; @endphp
<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.manual') }}" enctype="multipart/form-data" class="space-y-5"
          x-data="{ wallets: {{ count($crypto) ? json_encode($crypto) : '[{label:\'\',address:\'\'}]' }} }">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Manual payment methods</h2>
        <p class="text-sm text-slate-500">Shown to customers who choose manual payment. They pay, then submit a transaction ID + screenshot for you to verify.</p>

        <div><label for="manual_instructions" class="label">Instructions</label><textarea id="manual_instructions" name="manual_instructions" rows="3" class="input">{{ old('manual_instructions', setting('manual_instructions')) }}</textarea></div>

        <div><label for="manual_upi_id" class="label">UPI ID</label><input id="manual_upi_id" name="manual_upi_id" type="text" value="{{ old('manual_upi_id', setting('manual_upi_id')) }}" class="input" placeholder="yourname@bank"></div>

        {{-- Per-method visibility toggles --}}
        <div class="grid gap-3 rounded-xl border border-slate-200 p-4 sm:grid-cols-3">
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="manual_upi_enabled" value="1" {{ setting('manual_upi_enabled', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Show UPI / QR
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="manual_bank_enabled" value="1" {{ setting('manual_bank_enabled', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Show Bank
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                <input type="checkbox" name="manual_crypto_enabled" value="1" {{ setting('manual_crypto_enabled', '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Show Crypto
            </label>
        </div>

        <div class="flex items-center gap-4">
            <span class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                @if (setting('manual_qr'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting('manual_qr')) }}" alt="QR" class="h-full w-full object-contain">
                @else
                    <span class="text-xs text-slate-400">QR</span>
                @endif
            </span>
            <div>
                <label for="manual_qr" class="label">Payment QR code</label>
                <input id="manual_qr" name="manual_qr" type="file" accept="image/*" class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
        </div>

        <div><label for="manual_bank_details" class="label">Bank details</label><textarea id="manual_bank_details" name="manual_bank_details" rows="3" class="input" placeholder="Account name, number, IFSC/SWIFT, bank name...">{{ old('manual_bank_details', setting('manual_bank_details')) }}</textarea></div>

        {{-- Crypto QR --}}
        <div class="flex items-center gap-4">
            <span class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                @if (setting('manual_crypto_qr'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting('manual_crypto_qr')) }}" alt="Crypto QR" class="h-full w-full object-contain">
                @else
                    <span class="text-xs text-slate-400">QR</span>
                @endif
            </span>
            <div>
                <label for="manual_crypto_qr" class="label">Crypto QR code <span class="text-slate-400">(optional)</span></label>
                <input id="manual_crypto_qr" name="manual_crypto_qr" type="file" accept="image/*" class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
        </div>

        {{-- Crypto wallets (repeatable) --}}
        <div>
            <label class="label">Crypto wallets</label>
            <div class="space-y-2">
                <template x-for="(w, i) in wallets" :key="i">
                    <div class="flex gap-2">
                        <input :name="'crypto_label['+i+']'" x-model="w.label" class="input" placeholder="USDT (TRC20)">
                        <input :name="'crypto_address['+i+']'" x-model="w.address" class="input" placeholder="Wallet address">
                        <button type="button" @click="wallets.splice(i,1)" class="btn-ghost btn-sm flex-shrink-0">&times;</button>
                    </div>
                </template>
            </div>
            <button type="button" @click="wallets.push({label:'',address:''})" class="btn-ghost btn-sm mt-2">+ Add wallet</button>
        </div>

        {{-- Method icons (PNG only) --}}
        <div class="rounded-xl border border-slate-200 p-4">
            <p class="label">Method icons <span class="text-slate-400">(PNG only, optional)</span></p>
            <div class="mt-2 grid gap-4 sm:grid-cols-3">
                @foreach (['manual_upi_icon' => 'UPI / QR', 'manual_bank_icon' => 'Bank', 'manual_crypto_icon' => 'Crypto'] as $iconKey => $iconLabel)
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100">
                            @if (setting($iconKey))
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting($iconKey)) }}" alt="" class="h-full w-full object-contain">
                            @else
                                <span class="text-[10px] text-slate-400">PNG</span>
                            @endif
                        </span>
                        <div>
                            <label class="text-xs font-semibold text-ink-900">{{ $iconLabel }}</label>
                            <input name="{{ $iconKey }}" type="file" accept="image/png" class="mt-1 block w-full text-xs text-slate-500 file:mr-2 file:rounded file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-600">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn-primary btn-md">Save manual payment settings</button>
    </form>
</div>
