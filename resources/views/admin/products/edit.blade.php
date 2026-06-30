@extends('layouts.admin')

@section('page-title', 'Edit product')

@section('admin')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back to products</a>
            <h2 class="mt-2 font-display text-2xl font-bold text-white">{{ $product->title }}</h2>
        </div>
        <a href="{{ route('products.show', $product) }}" target="_blank" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-slate-300 hover:bg-white/5">View live &nearr;</a>
    </div>

    @include('admin.products._form')
@endsection
