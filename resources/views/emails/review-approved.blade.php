@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">Your review is live ✅</h1>
    <p style="margin:0 0 16px;">Thanks {{ $review->user->name ?? 'there' }}! Your review for <strong>{{ $review->product->title ?? 'a product' }}</strong> has been approved and is now published.</p>
    @if ($review->product)
        <a href="{{ route('products.show', $review->product) }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">View product</a>
    @endif
@endsection
