@extends('layouts.app')

@section('content')
    <h1>Retention Offers</h1>
    <ul>
        @foreach ($offers as $offer)
            <li>{{ $offer->offer_type }} - {{ $offer->accepted ? 'accepted' : 'pending' }}</li>
        @endforeach
    </ul>
@endsection
