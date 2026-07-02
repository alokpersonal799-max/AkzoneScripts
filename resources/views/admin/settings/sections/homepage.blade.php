<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.homepage') }}" class="space-y-4">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Homepage sections</h2>
        <p class="text-sm text-slate-500">Show or hide each block on your homepage.</p>

        @php
            $toggles = [
                'home_show_categories' => 'Categories grid',
                'home_show_latest' => 'Latest items',
                'home_show_featured' => 'The world-leading marketplace (featured)',
                'home_show_bestselling' => 'Weekly best selling',
                'home_show_limited' => 'Limited Deal band (limited-time / low stock)',
                'home_show_free' => 'Free items band',
                'home_show_testimonials' => 'Testimonials',
            ];
        @endphp
        @foreach ($toggles as $key => $label)
            <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm">
                <input type="checkbox" name="{{ $key }}" value="1" {{ setting($key, '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                <span class="font-semibold text-ink-900">{{ $label }}</span>
            </label>
        @endforeach

        <button type="submit" class="btn-primary btn-md">Save homepage settings</button>
    </form>
</div>
