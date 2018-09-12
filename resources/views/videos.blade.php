@extends('layouts.app')

@section('content')
    <br>
    @foreach ($videos as $video)
    <p><a href="{{ route('video.show', ['alias' => $video->basename]) }}">{{ $video->filename }}</a></p>
    @endforeach

    <nav>{{ $videos->links() }}</nav>
@endsection