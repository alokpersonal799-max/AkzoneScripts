@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Wishlist</h1>
        <p class="mt-1 text-slate-500">Products you've saved for later.</p>
    </div>

    @if ($products->isEmpty())
        <x-empty-state title="Your wishlist is empty"
                       message="Tap the heart on any product to save it here."
                       icon="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z">
            <x-slot:action>
                <a href="{{ route('products.index') }}" class="btn-primary btn-md">Browse products</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 gap-4 sm:gap-6 xl:grid-cols-3">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    @endif
@endsection
