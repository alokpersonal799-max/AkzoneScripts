@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to tickets</a>
        <h1 class="mt-2 font-display text-2xl font-extrabold text-ink-900">Open a support ticket</h1>
    </div>

    <div class="card max-w-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label for="subject" class="label">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required class="input" placeholder="Briefly describe your issue">
            </div>
            <div>
                <label for="priority" class="label">Priority</label>
                <select id="priority" name="priority" class="input">
                    <option value="low">Low</option>
                    <option value="normal" selected>Normal</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div>
                <label for="message" class="label">Message</label>
                <textarea id="message" name="message" rows="6" required class="input" placeholder="Tell us what's going on...">{{ old('message') }}</textarea>
            </div>
            <div>
                <label for="attachment" class="label">Attachment <span class="text-slate-400">(optional, image or file, max 5MB)</span></label>
                <input id="attachment" name="attachment" type="file" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
            </div>
            <button type="submit" class="btn-primary btn-md">Submit ticket</button>
        </form>
    </div>
@endsection
