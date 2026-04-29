@extends('layouts.app')

@section('content')
    <h1>Promo Codes</h1>
    <ul>
        @foreach ($promoCodes as $promoCode)
            <li>{{ $promoCode->code }} ({{ $promoCode->discount_type }})</li>
        @endforeach
    </ul>
@endsection
