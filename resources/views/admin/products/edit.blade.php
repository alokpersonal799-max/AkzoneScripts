@extends('layouts.admin')

@section('page-title', 'Edit product')

@section('admin')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to products</a>
            <h2 class="mt-2 font-display text-2xl font-extrabold text-ink-900">{{ $product->title }}</h2>
        </div>
        <a href="{{ route('products.show', $product) }}" target="_blank" class="btn-ghost btn-sm">View live &nearr;</a>
    </div>

    @include('admin.products._form')
@endsection
