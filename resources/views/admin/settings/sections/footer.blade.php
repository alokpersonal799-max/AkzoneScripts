<div class="card p-6 sm:p-8">
    <form method="POST" action="{{ route('admin.settings.footer') }}" class="space-y-5">
        @csrf @method('PUT')
        <h2 class="font-display text-lg font-bold text-ink-900">Footer</h2>

        <div><label for="footer_about" class="label">About text</label><textarea id="footer_about" name="footer_about" rows="3" class="input">{{ old('footer_about', setting('footer_about')) }}</textarea></div>
        <div><label for="footer_copyright" class="label">Copyright line</label><input id="footer_copyright" name="footer_copyright" type="text" value="{{ old('footer_copyright', setting('footer_copyright')) }}" class="input"></div>

        <button type="submit" class="btn-primary btn-md">Save footer</button>
    </form>
</div>
