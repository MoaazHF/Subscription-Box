@extends('layouts.app')

@section('content')
    <h1>Flash Sales</h1>
    <ul>
        @foreach ($flashSales as $flashSale)
            <li>{{ $flashSale->name }} - claimed: {{ $flashSale->claimed_count }}</li>
        @endforeach
    </ul>
@endsection
