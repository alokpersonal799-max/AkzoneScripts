@extends('layouts.admin')

@section('page-title', $page->exists ? 'Edit page' : 'New page')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.settings.show', 'pages') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to pages</a>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-ink-900">{{ $page->exists ? $page->title : 'Create page' }}</h2>
    </div>

    <form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="card max-w-4xl space-y-5 p-6 sm:p-8" x-data="pageEditor()" id="pageForm">
        @csrf
        @if ($page->exists) @method('PUT') @endif

        {{-- Content Type Toggle --}}
        <div>
            <label class="label mb-2">Content Type</label>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer rounded-lg border px-4 py-2 transition-all" :class="contentType === 'text' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                    <input type="radio" name="content_type_radio" value="text" x-model="contentType" class="sr-only">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span class="text-sm font-medium">Plain Text</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer rounded-lg border px-4 py-2 transition-all" :class="contentType === 'html' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">
                    <input type="radio" name="content_type_radio" value="html" x-model="contentType" class="sr-only">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span class="text-sm font-medium">HTML / Code</span>
                </label>
            </div>
            <input type="hidden" name="content_type" :value="contentType">
            <p class="mt-1.5 text-xs text-slate-400" x-show="contentType === 'text'">Content will be displayed as plain text with line breaks preserved.</p>
            <p class="mt-1.5 text-xs text-slate-400" x-show="contentType === 'html'">Raw HTML, CSS, and JavaScript will be rendered exactly as entered.</p>
        </div>

        {{-- Title --}}
        <div>
            <label for="title" class="label">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" required class="input" x-ref="titleInput">
            @if ($page->exists)
                <p class="mt-1 text-xs text-slate-400">URL: <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-brand-600 hover:underline">{{ url('/p/'.$page->slug) }}</a></p>
            @else
                <p class="mt-1 text-xs text-slate-400">A shareable URL is generated automatically from the title.</p>
            @endif
        </div>

        @if (!$page->exists)
        {{-- Template Selector (only for new pages) --}}
        <div>
            <label for="template" class="label">Start from Template</label>
            <select id="template" x-model="selectedTemplate" x-on:change="applyTemplate()" class="input">
                <option value="">-- Blank (start from scratch) --</option>
                <option value="privacy">Privacy Policy</option>
                <option value="refund">Refund Policy</option>
                <option value="terms">Terms of Service</option>
                <option value="about">About Us</option>
                <option value="faq">FAQ</option>
                <option value="contact">Contact Info</option>
            </select>
            <p class="mt-1 text-xs text-slate-400">Select a template to pre-fill with professional content you can customize.</p>
        </div>
        @endif

        {{-- Content Editor --}}
        <div>
            <label for="content" class="label">Content</label>
            <textarea id="content" name="content" rows="22" class="input font-mono text-sm leading-relaxed" x-ref="contentArea" :class="contentType === 'html' ? 'bg-slate-900 text-green-300 border-slate-700' : ''">{{ old('content', $page->content) }}</textarea>
            <p class="mt-1.5 text-xs text-slate-400" x-show="contentType === 'html'">
                <svg class="inline h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Full HTML/CSS/JS is supported. Use Tailwind classes for styling (loaded via CDN on the public site).
            </p>
        </div>

        {{-- Options --}}
        <div class="flex flex-wrap gap-5">
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Published</label>
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $page->show_in_footer ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30"> Show in footer</label>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-primary btn-md">{{ $page->exists ? 'Update page' : 'Create page' }}</button>
            <button type="button" class="btn-ghost btn-md" x-on:click="openPreview()">
                <svg class="h-4 w-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </button>
            <a href="{{ route('admin.settings.show', 'pages') }}" class="btn-ghost btn-md">Cancel</a>
        </div>
    </form>

    @include('admin.pages._templates_js')
@endsection
