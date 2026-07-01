@csrf
<div class="space-y-6" x-data="{ ptype: '{{ old('provider_type', $service->provider_type ?? 'admin') }}' }">
    <div class="card p-6">
        <h2 class="font-display text-base font-bold text-ink-900">Service details</h2>
        <div class="mt-4 space-y-4">
            <div>
                <label for="name" class="label">Service name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $service->name) }}" required class="input" placeholder="e.g. Custom Laravel Development">
            </div>
            <div>
                <label for="subtitle" class="label">Subtitle <span class="text-slate-400">(optional)</span></label>
                <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle', $service->subtitle) }}" class="input" placeholder="e.g. Fast, clean, production-ready">
            </div>
            <div>
                <label for="description" class="label">Description</label>
                <textarea id="description" name="description" rows="4" class="input" placeholder="Describe the service...">{{ old('description', $service->description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-display text-base font-bold text-ink-900">Provider</h2>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 p-3 text-sm transition" :class="ptype === 'admin' ? 'border-brand-500 bg-brand-50' : 'border-slate-200'">
                <input type="radio" name="provider_type" value="admin" x-model="ptype" class="text-brand-600">
                <span><span class="font-semibold text-ink-900">Admin / Official</span><br><span class="text-xs text-slate-400">Use site identity &amp; global contacts</span></span>
            </label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 p-3 text-sm transition" :class="ptype === 'custom' ? 'border-brand-500 bg-brand-50' : 'border-slate-200'">
                <input type="radio" name="provider_type" value="custom" x-model="ptype" class="text-brand-600">
                <span><span class="font-semibold text-ink-900">Custom provider</span><br><span class="text-xs text-slate-400">Own name, photo &amp; links</span></span>
            </label>
        </div>

        {{-- Custom provider fields --}}
        <div x-show="ptype === 'custom'" x-cloak class="mt-4 space-y-4 border-t border-slate-100 pt-4">
            <div class="flex items-center gap-4">
                <img id="provider-avatar-preview" src="{{ $service->avatar_url ?? '' }}" alt="" class="h-14 w-14 rounded-xl object-cover {{ $service->provider_avatar ?? false ? '' : 'hidden' }}">
                <div>
                    <label for="provider_name" class="label">Provider name</label>
                    <input id="provider_name" name="provider_name" type="text" value="{{ old('provider_name', $service->provider_name) }}" class="input" placeholder="e.g. John Doe">
                </div>
            </div>
            <div>
                <label for="provider_avatar" class="label">Provider photo</label>
                <input id="provider_avatar" name="provider_avatar" type="file" accept="image/*" data-crop data-crop-ratio="1" data-crop-preview="#provider-avatar-preview" class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600">
                <p class="mt-1 text-xs text-slate-400">Suggested: square <strong>400&times;400&nbsp;px</strong>. You can crop after selecting.</p>
            </div>
        </div>

        {{-- Admin uses global contacts toggle --}}
        <label x-show="ptype === 'admin'" class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-4 text-sm font-semibold text-ink-900">
            <input type="checkbox" name="use_global_contact" value="1" {{ old('use_global_contact', $service->use_global_contact ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            Use global site contacts (WhatsApp, Telegram, social links from Settings)
        </label>
    </div>

    <div class="card p-6">
        <h2 class="font-display text-base font-bold text-ink-900">Contact buttons</h2>
        <p class="mt-1 text-xs text-slate-400">For custom providers, or to override global links. Leave blank to hide a button.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div><label class="label">WhatsApp number <span class="text-slate-400">(no +)</span></label><input name="whatsapp" type="text" value="{{ old('whatsapp', $service->whatsapp) }}" class="input" placeholder="14155550123"></div>
            <div><label class="label">Telegram username <span class="text-slate-400">(no @)</span></label><input name="telegram" type="text" value="{{ old('telegram', $service->telegram) }}" class="input" placeholder="username"></div>
            <div><label class="label">Instagram URL</label><input name="instagram" type="url" value="{{ old('instagram', $service->instagram) }}" class="input" placeholder="https://instagram.com/..."></div>
            <div><label class="label">Twitter URL</label><input name="twitter" type="url" value="{{ old('twitter', $service->twitter) }}" class="input" placeholder="https://x.com/..."></div>
            <div><label class="label">GitHub URL</label><input name="github" type="url" value="{{ old('github', $service->github) }}" class="input" placeholder="https://github.com/..."></div>
            <div><label class="label">Facebook URL</label><input name="facebook" type="url" value="{{ old('facebook', $service->facebook) }}" class="input" placeholder="https://facebook.com/..."></div>
            <div><label class="label">Discord URL</label><input name="discord" type="url" value="{{ old('discord', $service->discord) }}" class="input" placeholder="https://discord.gg/..."></div>
        </div>
        <div class="mt-4 grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2">
            <div><label class="label">Highlighted button label</label><input name="custom_label" type="text" value="{{ old('custom_label', $service->custom_label) }}" class="input" placeholder="e.g. Book a call"></div>
            <div><label class="label">Highlighted button URL</label><input name="custom_url" type="url" value="{{ old('custom_url', $service->custom_url) }}" class="input" placeholder="https://..."></div>
        </div>
    </div>

    <div class="card p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="sort_order" class="label">Sort order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $service->sort_order ?? 0) }}" class="input">
            </div>
            <div class="space-y-2 pt-6">
                <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <input type="checkbox" name="allow_inquiry" value="1" {{ old('allow_inquiry', $service->allow_inquiry ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Show "Send message" inquiry button
                </label>
                <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Active (visible on the site)
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary btn-lg">{{ $service->exists ? 'Save changes' : 'Create service' }}</button>
        <a href="{{ route('admin.services.index') }}" class="btn-ghost btn-lg">Cancel</a>
    </div>
</div>
