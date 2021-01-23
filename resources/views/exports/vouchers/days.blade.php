@extends('layout.dompdf')
@section('content')
<h1>Vouchers List</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Campaign Name</th>
            <th>Date</th>
            <th>Total Vouchers</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->campaign->name }}</td>
            <td>{{ $value->date }}</td>
            <td>{{ $value->total_vouchers }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
