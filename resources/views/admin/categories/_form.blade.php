@php $isEdit = $category->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="max-w-2xl space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="card p-6">
        <div class="space-y-5">
            <div>
                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" required class="input">
            </div>
            <div>
                <label for="icon" class="label">Icon <span class="text-slate-400">(emoji)</span></label>
                <input id="icon" name="icon" type="text" value="{{ old('icon', $category->icon) }}" placeholder="📦" maxlength="100"
                       class="w-32 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-2xl focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
            </div>
            <div>
                <label for="description" class="label">Description</label>
                <textarea id="description" name="description" rows="3" class="input">{{ old('description', $category->description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                Active (visible in storefront)
            </label>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn-primary btn-md">{{ $isEdit ? 'Update category' : 'Create category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-ghost btn-md">Cancel</a>
    </div>
</form>
