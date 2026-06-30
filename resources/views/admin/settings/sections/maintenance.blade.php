<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Maintenance mode</h2>
        <p class="text-sm text-slate-500">When enabled, visitors see a maintenance page. Admins can still access the site and admin panel.</p>

        <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 text-sm">
            <input type="checkbox" name="maintenance_enabled" value="1" {{ setting('maintenance_enabled') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            <span class="font-semibold text-ink-900">Enable maintenance mode</span>
        </label>

        <div><label for="maintenance_message" class="label">Maintenance message</label><textarea id="maintenance_message" name="maintenance_message" rows="3" class="input">{{ old('maintenance_message', setting('maintenance_message')) }}</textarea></div>

        <button type="submit" class="btn-primary btn-md">Save maintenance settings</button>
    </form>
</div>
