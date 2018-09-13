@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Video List</h1>
        <br>
        <div class="list-group">
            @foreach ($videos as $video)
            <a href="{{ route('video.show', ['alias' => $video->basename]) }}" class="list-group-item list-group-item-action">
                {{ $video->filename }}
            </a>
            @endforeach
        </div>
        <nav>{{ $videos->links() }}</nav>
    </div>
@endsection