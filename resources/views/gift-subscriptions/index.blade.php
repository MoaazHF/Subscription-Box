@extends('layouts.app')

@section('content')
    <h1>Gift Subscriptions</h1>
    <ul>
        @foreach ($gifts as $gift)
            <li>{{ $gift->recipient_email }} - {{ $gift->status }}</li>
        @endforeach
    </ul>
@endsection
