<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Packing Sticker #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .sticker { width: 4in; height: 6in; padding: 16px; box-sizing: border-box; }
        .box { border: 2px dashed #0f172a; height: 100%; padding: 12px; border-radius: 8px; }
        .title { font-size: 18px; font-weight: 800; margin-bottom: 4px; }
        .muted { color: #475569; font-size: 12px; }
        .label { margin-top: 12px; font-size: 13px; }
        .barcode { margin-top: 16px; height: 48px; background: repeating-linear-gradient(90deg, #0f172a, #0f172a 2px, transparent 2px, transparent 4px); }
    </style>
</head>
<body>
    <div class="sticker">
        <div class="box">
            <div class="title">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="muted">{{ $order->created_at?->format('Y-m-d') }}</div>
            <div class="label"><strong>To:</strong> {{ $order->user?->name ?? 'Guest' }}</div>
            <div class="label">{{ $order->shipping_address ?: 'N/A' }}</div>
            <div class="label"><strong>Status:</strong> {{ ucfirst($order->order_status) }}</div>
            <div class="label"><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</div>
            <div class="barcode"></div>
        </div>
    </div>
</body>
</html>
