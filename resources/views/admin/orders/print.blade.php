<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .title { font-size: 20px; font-weight: 800; }
        .muted { color: #475569; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 12px; }
        th { background: #f8fafc; text-transform: uppercase; letter-spacing: .08em; font-size: 11px; }
        .total { text-align: right; font-weight: 700; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Invoice #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="muted">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
        </div>
        <div class="muted">{{ config('app.name', 'Store') }}</div>
    </div>

    <div style="margin-top:16px; font-size:13px;">
        <div><strong>Customer:</strong> {{ $order->user?->name ?? 'Guest' }}</div>
        <div class="muted">{{ $order->user?->email }}</div>
        <div><strong>Shipping:</strong> {{ $order->shipping_address ?: 'N/A' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($order->order_status) }} | Payment: {{ ucfirst($order->payment_status) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Line total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $orderItem)
                @php $line = $orderItem->price * $orderItem->quantity; @endphp
                <tr>
                    <td>{{ $orderItem->item?->name ?? 'Item removed' }}</td>
                    <td>{{ $orderItem->quantity }}</td>
                    <td>${{ number_format($orderItem->price, 2) }}</td>
                    <td>${{ number_format($line, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total" style="margin-top:12px;">Total: ${{ number_format($order->total_amount, 2) }}</div>
</body>
</html>
