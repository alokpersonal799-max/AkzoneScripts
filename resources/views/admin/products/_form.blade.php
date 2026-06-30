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
        <div class="card p-6">
            <div class="space-y-5">
                <div>
                    <label for="title" class="label">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $product->title) }}" required class="input">
                </div>
                <div>
                    <label for="tagline" class="label">Tagline <span class="text-slate-400">(short summary)</span></label>
                    <input id="tagline" name="tagline" type="text" value="{{ old('tagline', $product->tagline) }}" class="input">
                </div>
                <div>
                    <label for="description" class="label">Description</label>
                    <textarea id="description" name="description" rows="8" required class="input">{{ old('description', $product->description) }}</textarea>
                </div>
                <div>
                    <label for="tags" class="label">Tags <span class="text-slate-400">(comma separated)</span></label>
                    <input id="tags" name="tags" type="text" value="{{ $tagsValue }}" placeholder="laravel, api, tailwind" class="input">
                </div>
            </div>
        </div>

        {{-- Files --}}
        <div class="card p-6">
            <h3 class="font-display text-base font-bold text-ink-900">Media &amp; files</h3>
            <div class="mt-4 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="thumbnail" class="label">Thumbnail image</label>
                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail_url }}" alt="" class="mb-2 h-24 w-full rounded-lg object-cover">
                    @endif
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                           class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
                </div>
                <div>
                    <label for="product_file" class="label">Product package</label>
                    @if ($product->file_name)
                        <p class="mb-2 truncate rounded-lg bg-slate-100 px-3 py-2 text-xs text-slate-600">📦 {{ $product->file_name }} ({{ $product->formatted_file_size }})</p>
                    @endif
                    <input id="product_file" name="product_file" type="file"
                           class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
                    <p class="mt-1 text-xs text-slate-400">Allowed: {{ implode(', ', config('marketplace.allowed_file_types')) }}. Stored privately.</p>
                </div>
            </div>

            {{-- Gallery (max 6) --}}
            <div class="mt-5">
                <label class="label">Gallery images <span class="text-slate-400">(up to 6 — shown in the product slider)</span></label>
                @if ($product->gallery && count($product->gallery))
                    <div class="mb-3 grid grid-cols-3 gap-3 sm:grid-cols-6">
                        @foreach ($product->gallery as $image)
                            <label class="group relative block cursor-pointer overflow-hidden rounded-lg border border-slate-200">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image) }}" alt="" class="aspect-square w-full object-cover">
                                <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 bg-rose-600/90 py-1 text-[10px] font-semibold text-white opacity-0 transition group-hover:opacity-100">
                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image }}" class="h-3 w-3"> Remove
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <input id="gallery" name="gallery[]" type="file" accept="image/*" multiple
                       class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
            <div class="mt-5">
                <label for="demo_url" class="label">Live demo URL</label>
                <input id="demo_url" name="demo_url" type="url" value="{{ old('demo_url', $product->demo_url) }}" placeholder="https://..." class="input">
            </div>
        </div>
    </div>

    {{-- Sidebar settings --}}
    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-display text-base font-bold text-ink-900">Publish</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="status" class="label">Status</label>
                    <select id="status" name="status" class="input">
                        @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $product->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Feature on homepage
                </label>
                <div>
                    <label for="version" class="label">Version</label>
                    <input id="version" name="version" type="text" value="{{ old('version', $product->version) }}" required class="input">
                </div>
            </div>
        </div>

        <div class="card p-6" x-data="{ global: {{ old('use_global_contact', $product->use_global_contact ?? true) ? 'true' : 'false' }} }">
            <h3 class="font-display text-base font-bold text-ink-900">Sales &amp; contact</h3>
            <div class="mt-4 space-y-4">
                <label class="flex items-start gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="is_purchasable" value="1" {{ old('is_purchasable', $product->is_purchasable ?? true) ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    <span><span class="font-semibold text-ink-900">Available for direct sale</span><br><span class="text-xs text-slate-400">If off, Buy Now is hidden and checkout is blocked — buyers contact you via WhatsApp/Telegram instead.</span></span>
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="use_global_contact" value="1" x-model="global" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Use global contact (from System settings)
                </label>
                <div x-show="!global" x-cloak class="space-y-4">
                    <div>
                        <label for="contact_whatsapp" class="label">Product WhatsApp number</label>
                        <input id="contact_whatsapp" name="contact_whatsapp" type="text" value="{{ old('contact_whatsapp', $product->contact_whatsapp) }}" class="input" placeholder="14155552671">
                    </div>
                    <div>
                        <label for="contact_telegram" class="label">Product Telegram username</label>
                        <input id="contact_telegram" name="contact_telegram" type="text" value="{{ old('contact_telegram', $product->contact_telegram) }}" class="input" placeholder="yourusername">
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-display text-base font-bold text-ink-900">Pricing &amp; category</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="category_id" class="label">Category</label>
                    <select id="category_id" name="category_id" required class="input">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) old('category_id', $product->category_id) === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="label">Price ({{ config('marketplace.currency') }})</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? 0) }}" required class="input">
                </div>
                <div>
                    <label for="sale_price" class="label">Sale price <span class="text-slate-400">(optional)</span></label>
                    <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price) }}" class="input">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary btn-lg flex-1">{{ $isEdit ? 'Update product' : 'Create product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-ghost btn-lg">Cancel</a>
        </div>
    </div>
</form>
