@extends('layouts.app')

@section('content')
<h3>Edit Bill #{{ $bill->bill_no }}</h3>

<form id="billForm" action="{{ route('bills.update', $bill) }}" method="POST">
  @csrf
  @method('PUT')
  <div class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Bill No</label>
      <input type="text" name="bill_no" value="{{ $bill->bill_no }}" readonly class="form-control">
    </div>
    <!-- <div class="col-md-6">
      <label class="form-label">Customer Name</label>
      <input name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name', $bill->customer_name) }}" required>
    </div> -->
  </div>

  <h5>Items</h5>
  <table class="table table-sm" id="itemsTable">
    <thead>
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Total</th>
        <th></th>
      </tr>
    </thead>
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

  <button class="btn btn-primary" type="submit">Update Bill</button>
  <a href="{{ route('bills.index') }}" class="btn btn-secondary">Cancel</a>
</form>

@push('scripts')
<script>
(function(){
  const productSelect = document.getElementById('productSelect');
  const qtyInput = document.getElementById('qtyInput');
  const addBtn = document.getElementById('addBtn');
  const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
  const grandTotalEl = document.getElementById('grandTotal');
  const billForm = document.getElementById('billForm');

  // Pre-fill items from server
  let items = @json($jsItems);

  function render(){
    itemsTable.innerHTML = '';
    let gt = 0;
    items.forEach((it, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <input type="hidden" name="items[${idx}][product_id]" value="${it.product_id}">
          <input type="hidden" name="items[${idx}][price]" value="${it.price}">
          ${it.name}
        </td>
        <td>₹${parseFloat(it.price).toFixed(2)}</td>
        <td>
          <input type="number" min="1" class="form-control form-control-sm qty-input" data-idx="${idx}" value="${it.quantity}">
          <input type="hidden" name="items[${idx}][quantity]" value="${it.quantity}">
        </td>
        <td>${(it.price*it.quantity).toFixed(2)}</td>
        <td><button type="button" class="btn btn-sm btn-danger" data-idx="${idx}">Remove</button></td>
      `;
      itemsTable.appendChild(tr);
      gt += it.price * it.quantity;
    });
    grandTotalEl.innerText = gt.toFixed(2);
  }

  // Add or update item
  addBtn.addEventListener('click', function(){
    const val = productSelect.value;
    if(!val) return alert('Select product');
    const [id, price] = val.split('|');
    const qty = parseInt(qtyInput.value) || 1;
    const name = productSelect.options[productSelect.selectedIndex].text.split(' (Stock')[0];

    const idx = items.findIndex(it => it.product_id == id);
    if(idx > -1){
      items[idx].quantity += qty;
    } else {
      items.push({ product_id: id, name, price: parseFloat(price), quantity: qty });
    }
    render();
    productSelect.value = '';
    qtyInput.value = '1';
  });

  // Remove item
  itemsTable.addEventListener('click', function(e){
    if(e.target.matches('button[data-idx]')){
      const idx = parseInt(e.target.getAttribute('data-idx'));
      items.splice(idx,1);
      render();
    }
  });

  // Update quantity live
  itemsTable.addEventListener('input', function(e){
    if(e.target.classList.contains('qty-input')){
      const idx = parseInt(e.target.getAttribute('data-idx'));
      let qty = parseInt(e.target.value);
      if(qty < 1) qty = 1;
      items[idx].quantity = qty;
      render();
    }
  });

  // On submit, create hidden inputs for all items
  billForm.addEventListener('submit', function(e){
    if(items.length === 0){
      e.preventDefault();
      return alert('Add at least one item to bill.');
    }
    // Remove old hidden inputs
    billForm.querySelectorAll('input[type=hidden][name^="items"]').forEach(el => el.remove());
    // Add new hidden inputs
    items.forEach((it, idx) => {
      ['product_id','quantity','price'].forEach(field => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `items[${idx}][${field}]`;
        inp.value = it[field];
        billForm.appendChild(inp);
      });
    });
  });

  // Initial render
  render();
})();
</script>
@endpush

@endsection