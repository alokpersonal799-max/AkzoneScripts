@php $crypto = json_decode(setting('manual_crypto', '[]'), true) ?: []; @endphp
<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.manual') }}" enctype="multipart/form-data" class="space-y-5"
          x-data="{ wallets: {{ count($crypto) ? json_encode($crypto) : '[{label:\'\',address:\'\'}]' }} }">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Manual payment methods</h2>
        <p class="text-sm text-slate-500">Shown to customers who choose manual payment. They pay, then submit a transaction ID + screenshot for you to verify.</p>

        <div><label for="manual_instructions" class="label">Instructions</label><textarea id="manual_instructions" name="manual_instructions" rows="3" class="input">{{ old('manual_instructions', setting('manual_instructions')) }}</textarea></div>

        <div><label for="manual_upi_id" class="label">UPI ID</label><input id="manual_upi_id" name="manual_upi_id" type="text" value="{{ old('manual_upi_id', setting('manual_upi_id')) }}" class="input" placeholder="yourname@bank"></div>

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

        <button type="submit" class="btn-primary btn-md">Save manual payment settings</button>
    </form>
</div>
