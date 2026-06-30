@php $isEdit = $category->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="max-w-2xl space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
        <div class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" required
                       class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            </div>
            <div>
                <label for="icon" class="block text-sm font-medium text-slate-300">Icon <span class="text-slate-500">(emoji)</span></label>
                <input id="icon" name="icon" type="text" value="{{ old('icon', $category->icon) }}" placeholder="📦" maxlength="100"
                       class="mt-2 w-32 rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-center text-2xl text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-slate-300">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">{{ old('description', $category->description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-white/20 bg-ink-900 text-brand-500 focus:ring-brand-400/30">
                Active (visible in storefront)
            </label>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded-xl bg-brand-400 px-5 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">{{ $isEdit ? 'Update category' : 'Create category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-white/10 px-5 py-2.5 font-medium text-slate-300 transition hover:bg-white/5">Cancel</a>
    </div>
</form>
