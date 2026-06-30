@extends('layouts.admin')

@section('page-title', 'Edit category')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back to categories</a>
        <h2 class="mt-2 font-display text-2xl font-bold text-white">{{ $category->name }}</h2>
    </div>

    @include('admin.categories._form')
@endsection
