<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.hero') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Hero section</h2>
        <p class="text-sm text-slate-500">Edit the headline shown at the top of your homepage.</p>

        <div><label for="hero_badge" class="label">Badge text</label><input id="hero_badge" name="hero_badge" type="text" value="{{ old('hero_badge', setting('hero_badge')) }}" class="input"></div>
        <div><label for="hero_title" class="label">Heading <span class="text-slate-400">(the product count is added automatically)</span></label><input id="hero_title" name="hero_title" type="text" value="{{ old('hero_title', setting('hero_title')) }}" required class="input"></div>
        <div><label for="hero_highlight" class="label">Highlighted words <span class="text-slate-400">(gradient — must appear in the heading)</span></label><input id="hero_highlight" name="hero_highlight" type="text" value="{{ old('hero_highlight', setting('hero_highlight')) }}" class="input"></div>
        <div><label for="hero_subtitle" class="label">Subtitle</label><textarea id="hero_subtitle" name="hero_subtitle" rows="3" class="input">{{ old('hero_subtitle', setting('hero_subtitle')) }}</textarea></div>

        <button type="submit" class="btn-primary btn-md">Save hero section</button>
    </form>
</div>
