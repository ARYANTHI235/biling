@extends('layouts.app')

@section('content')
<h3>Edit Product</h3>

<form action="{{ route('products.update', $product) }}" method="POST">
  @csrf
  @method('PUT')
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input name="name" class="form-control" value="{{ $product->name }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Stock</label>
    <input name="stock" type="number" class="form-control" value="{{ $product->stock }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Price</label>
    <input name="price" type="number" step="0.01" class="form-control" value="{{ $product->price }}" required>
  </div>
  <button class="btn btn-success">Update</button>
  <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
