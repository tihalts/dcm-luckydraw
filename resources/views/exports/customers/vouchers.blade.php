@extends('layout.dompdf')
@section('content')
<h1>Customer Voucher List</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>CPR</th>
            <th>Mobile</th>
            <th>Purchase Amount</th>
            <th>Total Vouchers</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->fullname}} </td>
            <td>{{ $value->email }} </td>
            <td>{{ $value->cpr }} </td>
            <td>{{ $value->mobile }} </td>
            <td>{{ $value->purchase_amount }} </td>
            <td>{{ $value->total_vouchers }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
