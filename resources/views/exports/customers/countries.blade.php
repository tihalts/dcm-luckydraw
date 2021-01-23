@extends('layout.dompdf')
@section('content')
<h1>Country Wise Customer List</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Counrty Name</th>
            <th>Counrty Code</th>
            <th>Total Customers</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->name}} </td>
            <td>{{ $value->iso }} </td>
            <td>{{ $value->total_users }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
