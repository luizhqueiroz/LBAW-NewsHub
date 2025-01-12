<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDbService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
        $this->apiUrl = config('services.tmdb.url');
    }

    public function searchMovies($query)
    {
        return Http::get("{$this->apiUrl}/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
        ])->json();
    }

    public function getTrendingMovies()
    {
        return Http::get("{$this->apiUrl}/trending/movie/week", [
            'api_key' => $this->apiKey,
        ])->json();
    }

    public function getMovieDetails($movieId)
    {
        return Http::get("{$this->apiUrl}/movie/{$movieId}", [
            'api_key' => $this->apiKey,
        ])->json();
    }
}
