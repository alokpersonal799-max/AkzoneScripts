<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.integrations') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Integrations</h2>

        <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
            <input type="checkbox" name="tawk_enabled" value="1" {{ setting('tawk_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            Enable Tawk.to live chat
        </label>
        <div>
            <label for="tawk_embed" class="label">Tawk.to embed code</label>
            <textarea id="tawk_embed" name="tawk_embed" rows="6" class="input font-mono text-xs" placeholder="<!--Start of Tawk.to Script--> ... <!--End of Tawk.to Script-->">{{ old('tawk_embed', setting('tawk_embed')) }}</textarea>
            <p class="mt-1 text-xs text-slate-400">Paste the full embed script from your Tawk.to dashboard (Administration → Chat Widget).</p>
        </div>

        <button type="submit" class="btn-primary btn-md">Save integrations</button>
    </form>
</div>
