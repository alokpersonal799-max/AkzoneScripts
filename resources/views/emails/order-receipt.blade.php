@extends('emails.layout')

@section('email')
    <h1 style="font-size:20px;color:#0b1120;margin:0 0 8px;">Thanks for your purchase! 🎉</h1>
    <p style="margin:0 0 16px;">Hi {{ $order->billing_name }}, your order <strong>{{ $order->order_number }}</strong> is confirmed and your downloads are ready.</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        @foreach ($order->items as $item)
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">{{ $item->product_title }}</td>
                <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ config('marketplace.currency_symbol') }}{{ number_format($item->price, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="padding:12px 0;font-weight:700;color:#0b1120;">Total</td>
            <td style="padding:12px 0;font-weight:700;color:#2563eb;text-align:right;">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <a href="{{ route('dashboard.purchases') }}" style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:700;">Download your products</a>
@endsection
