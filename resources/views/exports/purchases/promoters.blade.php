@extends('layout.dompdf')
@section('content')
<h1>Promoter sales</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Sale Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->fullname}} </td>
            <td>{{ $value->email }} </td>
            <td>{{ $value->mobile }} </td>
            <td>{{ $value->sale_amount }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
