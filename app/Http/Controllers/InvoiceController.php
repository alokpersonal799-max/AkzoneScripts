<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Download the PDF invoice for a given order.
     */
    public function download(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.product');

        $pdf = Pdf::loadView('invoices.order', [
            'order' => $order,
            'siteName' => setting('site_name', config('app.name', 'AkzoneScripts')),
            'siteLogo' => setting('site_logo'),
            'supportEmail' => setting('support_email', setting('contact_email', 'support@example.com')),
            'currencySymbol' => base_symbol(),
        ]);

        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
}
