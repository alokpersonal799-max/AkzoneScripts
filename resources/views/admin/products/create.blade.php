@extends('layouts.admin')

@section('page-title', 'New product')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to products</a>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-ink-900">Create product</h2>
    </div>

    @include('admin.products._form')
@endsection
