<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.seo') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">SEO &amp; analytics</h2>
        <p class="text-sm text-slate-500">Control how your store appears in search engines and social shares.</p>

        <div><label for="seo_title" class="label">Meta title</label><input id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', setting('seo_title')) }}" class="input" placeholder="{{ setting('site_name') }} — Premium digital products"></div>
        <div><label for="seo_description" class="label">Meta description</label><textarea id="seo_description" name="seo_description" rows="3" class="input" placeholder="A short description for search results...">{{ old('seo_description', setting('seo_description')) }}</textarea></div>
        <div><label for="seo_keywords" class="label">Keywords <span class="text-slate-400">(comma separated)</span></label><input id="seo_keywords" name="seo_keywords" type="text" value="{{ old('seo_keywords', setting('seo_keywords')) }}" class="input" placeholder="scripts, php, laravel, templates"></div>
        <div><label for="analytics_id" class="label">Google Analytics ID</label><input id="analytics_id" name="analytics_id" type="text" value="{{ old('analytics_id', setting('analytics_id')) }}" class="input" placeholder="G-XXXXXXXXXX"></div>

        <div class="flex items-center gap-4">
            <span class="flex h-16 w-24 items-center justify-center overflow-hidden rounded-xl bg-slate-100">
                @if (setting('seo_og_image'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting('seo_og_image')) }}" alt="og" class="h-full w-full object-cover">
                @else
                    <span class="text-xs text-slate-400">OG image</span>
                @endif
            </span>
            <div>
                <label for="seo_og_image" class="label">Social share image (OG)</label>
                <input id="seo_og_image" name="seo_og_image" type="file" accept="image/*" class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
        </div>

        <button type="submit" class="btn-primary btn-md">Save SEO settings</button>
    </form>
</div>
