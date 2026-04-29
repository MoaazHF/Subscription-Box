@extends('layouts.app')

@section('content')
    <h1>Referrals</h1>
    <ul>
        @foreach ($referrals as $referral)
            <li>{{ $referral->referral_code }} - {{ $referral->status }}</li>
        @endforeach
    </ul>
@endsection
