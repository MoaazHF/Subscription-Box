@extends('layouts.app')

@section('content')
    <h1>Social Posts</h1>
    <ul>
        @foreach ($socialPosts as $post)
            <li>{{ $post->caption ?? 'No caption' }} - {{ $post->visibility }}</li>
        @endforeach
    </ul>
@endsection
