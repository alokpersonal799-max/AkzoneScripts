@extends('layouts.admin')

@section('page-title', $page->exists ? 'Edit page' : 'New page')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.settings.show', 'pages') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to pages</a>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-ink-900">{{ $page->exists ? $page->title : 'Create page' }}</h2>
    </div>

    <form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="card max-w-3xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($page->exists) @method('PUT') @endif

        <div>
            <label for="title" class="label">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" required class="input">
            @if ($page->exists)
                <p class="mt-1 text-xs text-slate-400">URL: <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-brand-600 hover:underline">{{ url('/p/'.$page->slug) }}</a></p>
            @else
                <p class="mt-1 text-xs text-slate-400">A shareable URL is generated automatically from the title.</p>
            @endif
        </div>

        <div>
            <label for="content" class="label">Content</label>
            <textarea id="content" name="content" rows="14" class="input">{{ old('content', $page->content) }}</textarea>
            <p class="mt-1 text-xs text-slate-400">Plain text or basic HTML is supported.</p>
        </div>

        <div class="flex flex-wrap gap-5">
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Published</label>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $page->show_in_footer ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Show in footer</label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary btn-md">{{ $page->exists ? 'Update page' : 'Create page' }}</button>
            <a href="{{ route('admin.settings.show', 'pages') }}" class="btn-ghost btn-md">Cancel</a>
        </div>
    </form>
@endsection
