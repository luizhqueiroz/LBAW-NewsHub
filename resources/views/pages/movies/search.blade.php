@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search Results</h1>
    @if(isset($movies['results']))
        @foreach($movies['results'] as $movie)
            <div class="movie">
                <h2>{{ $movie['title'] }}</h2>
                <img src="https://image.tmdb.org/t/p/w200/{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}">
                <p>{{ $movie['overview'] }}</p>
            </div>
        @endforeach
    @else
        <p>No results found.</p>
    @endif
</div>
@endsection
