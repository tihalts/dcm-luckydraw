@extends('layout.dompdf')
@section('content')
<h1>Category sales</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Category Name</th>
            <th>Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->name}} </td>
            <td>{{ $value->total_amount }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
