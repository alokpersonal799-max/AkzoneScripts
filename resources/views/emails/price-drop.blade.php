@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">Price drop on your wishlist 💸</h1>
    <p style="margin:0 0 16px;">Good news {{ $user->name }}! <strong>{{ $product->title }}</strong> from your wishlist just dropped in price.</p>
    <p style="margin:0 0 16px;font-size:22px;font-weight:800;color:#2563eb;">{{ config('marketplace.currency_symbol') }}{{ number_format($product->current_price, 2) }}</p>
    <a href="{{ route('products.show', $product) }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">Grab it now</a>
@endsection
