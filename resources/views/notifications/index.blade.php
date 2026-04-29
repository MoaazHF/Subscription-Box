@extends('layouts.app')

@section('content')
    <h1>Notifications</h1>
    <ul>
        @foreach ($notifications as $notification)
            <li>{{ $notification->event_type ?? 'event' }} - {{ $notification->status }}</li>
        @endforeach
    </ul>
@endsection
