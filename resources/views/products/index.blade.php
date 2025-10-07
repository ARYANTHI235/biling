@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Products</h3>
  <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
</div>

<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>Code</th>
      <th>Name</th>
      <th>Stock</th>
      <th>Price</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $p)
    <tr>
      <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
      <td>{{ $p->code }}</td>
      <td>{{ $p->name }}</td>
      <td>{{ $p->stock }}</td>
      <td>{{ number_format($p->price,2) }}</td>
      <td>
        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
        <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Delete product?');">
          @csrf
          @method('DELETE')
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

{{ $products->links() }}

@endsection
