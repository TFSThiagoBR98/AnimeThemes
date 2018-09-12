@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="jumbotron">
            <h1 class="display-3">AnimeThemes</h1>
            <p class="lead">A simple and consistent repository of anime opening and ending themes</p>
            <hr class="my-4">
            @include('layouts.announcements')
            @include('layouts.search')
            <p class="lead">
                Currently serving {{ $videoCount }} files
            </p>
        </div>
    </div>
    
@endsection

@section('footer')
    @include('layouts.footer')
@endsection