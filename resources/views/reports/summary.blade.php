@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Summary Report</h3>
  <form class="row g-2" method="get" action="{{ route('reports.summary') }}">
    <div class="col-auto">
      <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-auto">
      <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Filter</button>
    </div>
  </form>
</div>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>#</th>
      <th>Code</th>
      <th>Product</th>
      <th>Rate</th>
      <th>In Stock</th>
      <th>Out Stock</th>
      <th>Sell Amount</th>
      <th>Remaining Amount</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $r)
    <tr>
      <td>{{ $r->id }}</td>
      <td>{{ $r->code }}</td>
      <td>{{ $r->name }}</td>
      <td>{{ number_format($r->rate,2) }}</td>
      <td>{{ $r->in_stock }}</td>
      <td>{{ $r->out_stock }}</td>
      <td>{{ number_format($r->sell_amount,2) }}</td>
      <td>{{ number_format($r->remaining_amount,2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th colspan="4">Totals</th>
      <th>{{ $totals['in_stock'] }}</th>
      <th>{{ $totals['out_stock'] }}</th>
      <th>{{ number_format($totals['sell_amount'],2) }}</th>
      <th>{{ number_format($totals['remaining_amount'],2) }}</th>
    </tr>
  </tfoot>
</table>

@endsection
