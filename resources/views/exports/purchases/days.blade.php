@extends('layout.dompdf')
@section('content')
<h1>Day sales</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->date }} </td>
            <td>{{ $value->total_amount }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
