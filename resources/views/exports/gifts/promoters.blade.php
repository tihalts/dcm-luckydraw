@extends('layout.dompdf')
@section('content')
<h1>Promoter Gift List</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Scratch Cards</th>
            <th>Gifts</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->fullname }}</td>
            <td>{{ $value->email }}</td>
            <td>{{ $value->mobile }}</td>
            <td>{{ $value->total_cards }}</td>
            <td>{{ $value->total_gifts }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
