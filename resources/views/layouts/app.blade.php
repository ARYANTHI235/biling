<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Firecracker Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* small adjustments for desktop */
    body { font-family: Arial, sans-serif; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger mb-4">
  <div class="container">
    <a class="navbar-brand" href="{{ route('bills.index') }}">Shree Shivakashi Crackers</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('bills.index') }}">Bills</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('bills.create') }}">Create Bill</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
