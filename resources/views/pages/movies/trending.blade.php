@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Trending Movies</h1>

    <div class="row">
        @foreach($trending['results'] as $movie)
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="https://image.tmdb.org/t/p/w500/{{ $movie['poster_path'] }}" class="card-img-top" alt="{{ $movie['title'] }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $movie['title'] }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
