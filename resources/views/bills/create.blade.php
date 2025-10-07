@extends('layouts.app')

@section('content')
<h3>Create Bill</h3>

<form id="billForm" action="{{ route('bills.store') }}" method="POST">
  @csrf
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Bill No</label>
      <input type="text" name="bill_no" value="{{ $nextNumber }}" readonly class="form-control">
    </div>
    <div class="col-md-6">
      <label class="form-label">Customer Name</label>
      <input name="customer_name" id="customer_name" class="form-control" required>
    </div>
  </div>

  <h5>Items</h5>
  <table class="table table-sm" id="itemsTable">
    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
    <tbody></tbody>
  </table>

  <div class="d-flex gap-2 mb-3">
    <select id="productSelect" class="form-select w-50">
      <option value="">-- Select product --</option>
      @foreach($products as $p)
        <option value="{{ $p->id }}|{{ $p->price }}">{{ $p->name }} (Stock: {{ $p->stock }})</option>
      @endforeach
    </select>
    <input id="qtyInput" type="number" class="form-control w-25" placeholder="Qty" min="1" value="1">
    <button type="button" id="addBtn" class="btn btn-success">Add</button>
  </div>

  <div class="mb-3">
    <h4>Total: ₹ <span id="grandTotal">0.00</span></h4>
  </div>

  <button class="btn btn-primary" type="submit">Create Bill</button>
  <a href="{{ route('bills.index') }}" class="btn btn-secondary">Cancel</a>
</form>

@push('scripts')
<!-- Canvas Confetti CDN -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
(function(){
  const productSelect = document.getElementById('productSelect');
  const qtyInput = document.getElementById('qtyInput');
  const addBtn = document.getElementById('addBtn');
  const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
  const grandTotalEl = document.getElementById('grandTotal');
  const billForm = document.getElementById('billForm');

  let items = [];

  function render(){
    itemsTable.innerHTML = '';
    let gt = 0;
    items.forEach((it, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${it.name}</td>
        <td>${it.price.toFixed(2)}</td>
        <td>${it.quantity}</td>
        <td>${(it.price*it.quantity).toFixed(2)}</td>
        <td><button type="button" class="btn btn-sm btn-danger" data-idx="${idx}">Remove</button></td>
      `;
      itemsTable.appendChild(tr);
      gt += it.price * it.quantity;
    });
    grandTotalEl.innerText = gt.toFixed(2);
  }

  addBtn.addEventListener('click', function(){
    const val = productSelect.value;
    if(!val) return alert('Select product');
    const [id, price] = val.split('|');
    const qty = parseInt(qtyInput.value) || 1;
    const name = productSelect.options[productSelect.selectedIndex].text.split(' (Stock')[0];

    items.push({ product_id: id, name, price: parseFloat(price), quantity: qty });
    render();
  });

  itemsTable.addEventListener('click', function(e){
    if(e.target.matches('button[data-idx]')){
      const idx = parseInt(e.target.getAttribute('data-idx'));
      items.splice(idx,1);
      render();
    }
  });

  billForm.addEventListener('submit', function(e){
    if(items.length === 0){
      e.preventDefault();
      return alert('Add at least one item to bill.');
    }

    // Fire confetti effect
    confetti({
      particleCount: 200,
      spread: 90,
      origin: { y: 0.6 },
      colors: ['#ff0a54','#ff477e','#ff7096','#ff85a1','#fbb1b9']
    });

    // Add hidden inputs for items
    items.forEach((it, idx) => {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = `items[${idx}][product_id]`;
      inp.value = it.product_id;
      billForm.appendChild(inp);

      const inp2 = document.createElement('input');
      inp2.type = 'hidden';
      inp2.name = `items[${idx}][quantity]`;
      inp2.value = it.quantity;
      billForm.appendChild(inp2);

      const inp3 = document.createElement('input');
      inp3.type = 'hidden';
      inp3.name = `items[${idx}][price]`;
      inp3.value = it.price;
      billForm.appendChild(inp3);
    });
  });

})();
</script>
@endpush

@endsection
