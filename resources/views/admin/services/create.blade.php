@extends('layouts.admin')

@section('page-title', 'Add service')

@section('admin')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.services.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to services</a>
        <h1 class="mt-2 font-display text-2xl font-extrabold text-ink-900">Add service</h1>
    </div>
    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
        @include('admin.services._form')
    </form>
</div>
@endsection
