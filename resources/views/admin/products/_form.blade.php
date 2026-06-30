@php
    $isEdit = $product->exists;
    $tagsValue = old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '');
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_320px]">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Main fields --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-300">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $product->title) }}" required
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
                <div>
                    <label for="tagline" class="block text-sm font-medium text-slate-300">Tagline <span class="text-slate-500">(short summary)</span></label>
                    <input id="tagline" name="tagline" type="text" value="{{ old('tagline', $product->tagline) }}"
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300">Description</label>
                    <textarea id="description" name="description" rows="8" required
                              class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">{{ old('description', $product->description) }}</textarea>
                </div>
                <div>
                    <label for="tags" class="block text-sm font-medium text-slate-300">Tags <span class="text-slate-500">(comma separated)</span></label>
                    <input id="tags" name="tags" type="text" value="{{ $tagsValue }}" placeholder="laravel, api, tailwind"
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
            </div>
        </div>

        {{-- Files --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
            <h3 class="font-display text-base font-bold text-white">Media &amp; files</h3>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-slate-300">Thumbnail image</label>
                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail_url }}" alt="" class="mt-2 h-24 w-full rounded-lg object-cover">
                    @endif
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                           class="mt-2 block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-white/20">
                </div>
                <div>
                    <label for="product_file" class="block text-sm font-medium text-slate-300">Product package</label>
                    @if ($product->file_name)
                        <p class="mt-2 truncate rounded-lg bg-white/5 px-3 py-2 text-xs text-slate-300">📦 {{ $product->file_name }} ({{ $product->formatted_file_size }})</p>
                    @endif
                    <input id="product_file" name="product_file" type="file"
                           class="mt-2 block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-white/20">
                    <p class="mt-1 text-xs text-slate-500">Allowed: {{ implode(', ', config('marketplace.allowed_file_types')) }}. Stored privately.</p>
                </div>
            </div>
            <div class="mt-5">
                <label for="demo_url" class="block text-sm font-medium text-slate-300">Live demo URL</label>
                <input id="demo_url" name="demo_url" type="url" value="{{ old('demo_url', $product->demo_url) }}" placeholder="https://..."
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            </div>
        </div>
    </div>

    {{-- Sidebar settings --}}
    <div class="space-y-6">
        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
            <h3 class="font-display text-base font-bold text-white">Publish</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-300">Status</label>
                    <select id="status" name="status" class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400">
                        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $product->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-white/20 bg-ink-900 text-brand-500 focus:ring-brand-400/30">
                    Feature on homepage
                </label>
                <div>
                    <label for="version" class="block text-sm font-medium text-slate-300">Version</label>
                    <input id="version" name="version" type="text" value="{{ old('version', $product->version) }}" required
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
            <h3 class="font-display text-base font-bold text-white">Pricing &amp; category</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-300">Category</label>
                    <select id="category_id" name="category_id" required class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-300">Price ({{ config('marketplace.currency') }})</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? 0) }}" required
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-slate-300">Sale price <span class="text-slate-500">(optional)</span></label>
                    <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price) }}"
                           class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-xl bg-brand-400 px-4 py-3 font-semibold text-ink-900 transition hover:bg-brand-300">{{ $isEdit ? 'Update product' : 'Create product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-white/10 px-4 py-3 font-medium text-slate-300 transition hover:bg-white/5">Cancel</a>
        </div>
    </div>
</form>
