@extends('layout.dompdf')
@section('content')
<h1>Gift List</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Total Cards</th>
            <th>Total Gifts</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->date }} </td>
            <td>{{ $value->total_cards }} </td>
            <td>{{ $value->total_winners }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
