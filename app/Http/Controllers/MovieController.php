<?php

namespace App\Http\Controllers;

use App\Services\TMDbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $tmdbService;

    public function __construct(TMDbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $movies = $this->tmdbService->searchMovies($query);

        return response()->json($movies);
    }

    public function trending()
    {
        $trending = $this->tmdbService->getTrendingMovies();

        return view('pages.movies.trending', compact('trending'));
    }

    public function details($id)
    {
        $movie = $this->tmdbService->getMovieDetails($id);

        return response()->json($movie);
    }
}
