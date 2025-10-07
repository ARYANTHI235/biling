@extends('layouts.app')

@section('content')
<h3>Add Product</h3>

<form action="{{ route('products.store') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label for="code" class="form-label">Product Code</label>
    <input type="text" name="code" class="form-control" id="code" value="{{ old('code') }}" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input name="name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Stock</label>
    <input name="stock" type="number" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Price</label>
    <input name="price" type="number" step="0.01" class="form-control" required>
  </div>
  <button class="btn btn-success">Save</button>
  <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
