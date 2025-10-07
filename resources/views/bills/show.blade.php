<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Receipt - {{ $bill->bill_no }}</title>
  <style>
    /* Thermal receipt styling for ~80mm width */
    body { font-family: monospace; }
    .receipt { width: 280px; margin: 0 auto; } /* 80mm ~ 280px at 96dpi */
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .small { font-size: 12px; }
    .items td { padding: 4px 0; }
    .line { border-top: 1px dashed #000; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; }
    .right { text-align: right; }
    @media print {
      @page { margin: 4mm; size: 80mm auto; }
      body { margin: 0; }
      .receipt { width: 80mm; }
    }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="center">
      <div class="bold" style="font-size:16px;">MEGA FIRECRACKER SHOP</div>
      <div class="line"></div>
    </div>

    <table class="small">
      <tr>
        <td>Bill No: <strong>{{ $bill->bill_no }}</strong></td>
        <td class="right">Date: {{ $bill->created_at->format('d-M-Y') }}</td>
      </tr>
      <tr>
        <td></td>
        <td class="right">Time: {{ $bill->created_at->format('h:i A') }}</td>
      </tr>
      <tr>
        <td colspan="2">Customer: {{ $bill->customer_name }}</td>
      </tr>
    </table>

    <div class="line"></div>

    <table class="items small">
      <thead>
        <tr><th>Item</th><th class="right">Qty</th><th class="right">Total</th></tr>
      </thead>
      <tbody>
        @foreach($bill->items as $it)
        <tr>
          <td>{{ \Illuminate\Support\Str::limit(@$it->product->name,20) }}</td>
          <td class="right">{{ $it->quantity }}</td>
          <td class="right">{{ number_format($it->total,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="line"></div>

    <table class="small">
      <tr>
        <td class="bold">TOTAL</td>
        <td class="right bold">₹ {{ number_format($bill->total_amount,2) }}</td>
      </tr>
    </table>

    <div class="line"></div>
    <div class="center small">
      Thank you for your purchase!
    </div>
  </div>

  <script>
    // Auto-open print dialog when page loads
    window.onload = function() { window.print(); }
  </script>
</body>
</html>
