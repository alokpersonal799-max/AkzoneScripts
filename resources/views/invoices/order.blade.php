<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.6;
            background: #fff;
        }
        .invoice-wrapper {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: right;
        }
        .site-name {
            font-size: 22px;
            font-weight: bold;
            color: #6366f1;
        }
        .site-logo {
            max-height: 50px;
            max-width: 180px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #334155;
            letter-spacing: -0.5px;
        }
        .invoice-number {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .details-row {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .details-col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .details-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .details-value {
            font-size: 13px;
            color: #334155;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
        .items-table thead th:last-child {
            text-align: right;
        }
        .items-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .items-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #e2e8f0;
        }
        .totals {
            width: 100%;
            display: table;
            margin-bottom: 30px;
        }
        .totals-spacer {
            display: table-cell;
            width: 55%;
        }
        .totals-content {
            display: table-cell;
            width: 45%;
        }
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        .totals-row .label {
            display: table-cell;
            text-align: left;
            color: #64748b;
            font-size: 12px;
            padding: 4px 0;
        }
        .totals-row .value {
            display: table-cell;
            text-align: right;
            font-size: 13px;
            color: #334155;
            padding: 4px 0;
        }
        .totals-row.total .label,
        .totals-row.total .value {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
        }
        .totals-row.discount .value {
            color: #16a34a;
        }
        .payment-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }
        .payment-info-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: bold;
        }
        .payment-info-value {
            font-size: 13px;
            color: #334155;
            text-transform: capitalize;
            margin-top: 2px;
        }
        .thank-you {
            text-align: center;
            padding: 25px 20px;
            background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%);
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .thank-you h3 {
            font-size: 18px;
            color: #4f46e5;
            margin-bottom: 8px;
        }
        .thank-you p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.7;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        .footer p {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                @if ($siteLogo)
                    <img src="{{ public_path('storage/' . $siteLogo) }}" alt="{{ $siteName }}" class="site-logo">
                @else
                    <div class="site-name">{{ $siteName }}</div>
                @endif
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $order->order_number }}</div>
            </div>
        </div>

        {{-- Billing Details --}}
        <div class="details-row">
            <div class="details-col">
                <div class="details-label">Billed To</div>
                <div class="details-value">
                    {{ $order->billing_name ?? $order->user->name ?? 'Customer' }}<br>
                    {{ $order->billing_email ?? $order->user->email ?? '' }}
                </div>
            </div>
            <div class="details-col" style="text-align: right;">
                <div class="details-label">Invoice Date</div>
                <div class="details-value">{{ ($order->paid_at ?? $order->created_at)->format('F j, Y') }}</div>
                <div class="details-label" style="margin-top: 10px;">Status</div>
                <div class="details-value" style="color: #16a34a; font-weight: 600;">{{ ucfirst($order->status) }}</div>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 70%;">Product</th>
                    <th style="width: 25%;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product_title }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            <div class="totals-spacer"></div>
            <div class="totals-content">
                <div class="totals-row">
                    <span class="label">Subtotal</span>
                    <span class="value">{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="totals-row discount">
                        <span class="label">Discount{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span>
                        <span class="value">-{{ $currencySymbol }}{{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                @if ($order->tax > 0)
                    <div class="totals-row">
                        <span class="label">Tax</span>
                        <span class="value">{{ $currencySymbol }}{{ number_format($order->tax, 2) }}</span>
                    </div>
                @endif
                <div class="totals-row total">
                    <span class="label">Total</span>
                    <span class="value">{{ $currencySymbol }}{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="payment-info">
            <span class="payment-info-label">Payment Method</span>
            <div class="payment-info-value">{{ $order->payment_method }}</div>
        </div>

        {{-- Thank You Message --}}
        <div class="thank-you">
            <h3>Thank you for your purchase!</h3>
            <p>
                We truly appreciate your trust in {{ $siteName }}. Your digital products are ready and waiting for you.<br>
                If you love what you got, we would be thrilled to have you back. Happy creating!
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>This invoice was generated automatically by {{ $siteName }}.</p>
            <p>Questions? Contact us at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
        </div>
    </div>
</body>
</html>
