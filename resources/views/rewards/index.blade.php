@extends('layouts.app')

@section('content')
    <h1>Rewards</h1>
    <ul>
        @foreach ($rewards as $reward)
            <li>{{ $reward->type }} - {{ $reward->is_applied ? 'applied' : 'pending' }}</li>
        @endforeach
    </ul>
@endsection
