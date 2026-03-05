<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $order->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 300px; margin: 0 auto; padding: 10px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; }
        .header { margin-bottom: 10px; }
        .items { margin: 5px 0; }
        .item-name { font-weight: bold; }
        @media print {
            body { width: 80mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header center">
        <div class="bold" style="font-size: 16px;">{{ App\Models\SiteSetting::get('site_name', 'NedaPOS') }}</div>
        <div>{{ App\Models\SiteSetting::get('store_address', 'Jl. Contoh No. 123') }}</div>
        <div>Telp: {{ App\Models\SiteSetting::get('store_phone', '08123456789') }}</div>
    </div>

    <div class="line"></div>

    <div class="row">
        <span>No:</span>
        <span>{{ $order->invoice_number }}</span>
    </div>
    <div class="row">
        <span>Tanggal:</span>
        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row">
        <span>Kasir:</span>
        <span>{{ $order->user->name }}</span>
    </div>
    @if($order->customer)
    <div class="row">
        <span>Pelanggan:</span>
        <span>{{ $order->customer->name }}</span>
    </div>
    @endif

    <div class="line"></div>

    <div class="items">
        @foreach($order->items as $item)
            <div class="item-name">{{ $item->product_name }}</div>
            <div class="row">
                <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <div class="line"></div>

    <div class="row">
        <span>Subtotal:</span>
        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
    </div>
    @if($order->discount > 0)
    <div class="row">
        <span>Diskon:</span>
        <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
    </div>
    @endif
    @if($order->tax > 0)
    <div class="row">
        <span>Pajak:</span>
        <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
    </div>
    @endif
    <div class="line"></div>
    <div class="row bold" style="font-size: 14px;">
        <span>TOTAL:</span>
        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Bayar ({{ ucfirst($order->payment_method) }}):</span>
        <span>Rp {{ number_format($order->paid, 0, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Kembalian:</span>
        <span>Rp {{ number_format($order->change, 0, ',', '.') }}</span>
    </div>

    <div class="line"></div>

    <div class="center" style="margin-top: 10px;">
        <div>Terima Kasih!</div>
        <div>{{ App\Models\SiteSetting::get('receipt_footer', 'Barang yang sudah dibeli tidak dapat dikembalikan') }}</div>
    </div>

    <div class="center no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 20px; cursor: pointer; font-size: 14px;">
            🖨 Cetak Struk
        </button>
    </div>

    <script>
        window.onload = () => window.print();
    </script>
</body>
</html>
