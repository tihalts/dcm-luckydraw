@extends('layout.dompdf')
@section('content')
<h1>Campaign wise sales</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Campaign Name</th>
            <th>Start At</th>
            <th>End At</th>
            <th>Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->name}} </td>
            <td>{{ $value->start_at }} </td>
            <td>{{ $value->end_at }} </td>
            <td>{{ $value->purchase_amount }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
