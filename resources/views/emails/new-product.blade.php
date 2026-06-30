@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">New on {{ setting('site_name', config('app.name')) }} 🚀</h1>
    <p style="margin:0 0 16px;">Check out our latest release: <strong>{{ $product->title }}</strong>.</p>
    @if ($product->tagline)<p style="margin:0 0 16px;color:#64748b;">{{ $product->tagline }}</p>@endif
    <a href="{{ route('products.show', $product) }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">View product</a>
@endsection
