<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->transaction_id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 14px; line-height: 1.5; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #06b6d4; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #06b6d4; text-transform: uppercase; letter-spacing: -1px; }
        .info { margin-bottom: 40px; }
        .info table { width: 100%; }
        .info td { vertical-align: top; }
        .bill-to { width: 50%; }
        .order-info { width: 50%; text-align: right; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { background: #f4f4f5; text-align: left; padding: 12px; border-bottom: 1px solid #e4e4e7; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
        .items-table td { padding: 12px; border-bottom: 1px solid #e4e4e7; }
        .total-section { margin-top: 40px; text-align: right; }
        .total-row { display: inline-block; width: 250px; }
        .total-row div { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .grand-total { font-size: 20px; font-weight: bold; color: #06b6d4; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e4e4e7; }
        .footer { margin-top: 100px; text-align: center; font-size: 10px; color: #a1a1aa; border-top: 1px solid #e4e4e7; padding-top: 20px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-paid { background: #ecfeff; color: #0891b2; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">{{ $settings['site_name'] ?? 'NEXACODE' }}</div>
            <div class="badge badge-paid">INVOICE {{ strtoupper($order->status->label()) }}</div>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td class="bill-to">
                        <strong>BILL TO:</strong><br>
                        {{ $buyer->name }}<br>
                        {{ $buyer->email }}<br>
                        IP: {{ $order->ip_address ?? 'N/A' }}
                    </td>
                    <td class="order-info">
                        <strong>INVOICE NO:</strong> #{{ $order->transaction_id }}<br>
                        <strong>DATE:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                        <strong>PAYMENT:</strong> {{ strtoupper($order->payment_method) }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Author</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <small style="color: #71717a">Digital License Asset</small>
                        </td>
                        <td>{{ $item->product->author->name }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell;">Subtotal:</div>
                    <div style="display: table-cell; text-align: right;">Rp {{ number_format($order->total_amount + ($order->discount_amount ?? 0), 0, ',', '.') }}</div>
                </div>
                @if($order->discount_amount > 0)
                <div style="display: table; width: 100%; color: #ef4444;">
                    <div style="display: table-cell;">Discount:</div>
                    <div style="display: table-cell; text-align: right;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</div>
                </div>
                @endif
                <div style="display: table; width: 100%;" class="grand_total">
                    <div style="display: table-cell; font-weight: 800; font-size: 1.2rem; padding-top: 10px; border-top: 1.5px solid #000;">TOTAL:</div>
                    <div style="display: table-cell; font-weight: 800; font-size: 1.2rem; text-align: right; padding-top: 10px; border-top: 1.5px solid #000; color: #06b6d4;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $settings['site_name'] ?? 'NEXACODE' }} for your digital needs.</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            @if(isset($settings['site_address']))
                <p>{{ $settings['site_address'] }}</p>
            @endif
        </div>
    </div>
</body>
</html>
