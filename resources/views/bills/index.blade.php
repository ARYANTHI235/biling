@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Bills</h3>
  <a href="{{ route('bills.create') }}" class="btn btn-primary">Create Bill</a>
</div>


<table class="table table-bordered" id="bills-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Bill No</th>
      <th>Customer</th>
      <th>Total</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($bills as $b)
    <tr>
      <td>{{ $b->id }}</td>
      <td>{{ $b->bill_no }}</td>
      <td>{{ $b->customer_name }}</td>
      <td>{{ number_format($b->total_amount,2) }}</td>
      <td>{{ $b->created_at->format('Y-m-d') }}</td>
      <td>
        <a href="{{ route('bills.show', $b) }}" class="btn btn-sm btn-info">View</a>
        <a href="{{ route('bills.edit', $b) }}" class="btn btn-sm btn-warning">Edit</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#bills-table').DataTable();
});
</script>
@endsection
