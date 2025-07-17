<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    private string $apiKey;
    private string $baseUrl;
    private string $imageBaseUrl;

    public function __construct()
    {
        $this->apiKey = config("services.tmdb.api_key");
        $this->baseUrl = "https://api.themoviedb.org/3";
        $this->imageBaseUrl = "https://image.tmdb.org/t/p";
    }

    /**
     * Get popular movies
     */
    public function getPopularMovies(int $page = 1): array
    {
        $cacheKey = "popular_movies_page_{$page}";

        $client = new Client;

        $response = $client->request('GET', "{$this->baseUrl}/movie/popular", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,

                'accept' => 'application/json',
            ],
            'query' => [
                'page' => $page,
                "language" => "pt-BR",
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to fetch popular movies from TMDB API");
    }

    /**
     * Get movie details by ID
     */
    public function getMovieDetails(int $movieId): array
    {

        $client = new Client;

        $response = $client->request('GET', "{$this->baseUrl}/movie/{$movieId}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json'
            ],
            'query' => [
                "language" => "pt-BR",
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to fetch movie details for ID {$movieId} from TMDB API");
    }

    /**
     * Search movies
     */
    public function searchMovies(string $query, int $page = 1, array $filters = []): array
    {
        $params = [
            "api_key" => $this->apiKey,
            "language" => "pt-BR",
            "query" => $query,
            "page" => $page,
            "include_adult" => false,
        ];

        // Apply filters
        if (isset($filters["year"])) {
            $params["year"] = $filters["year"];
        }

        if (isset($filters["genre"])) {
            $params["with_genres"] = $filters["genre"];
        }

        if (isset($filters["sort_by"])) {
            $params["sort_by"] = $filters["sort_by"];
        }

        if (isset($filters["vote_average_gte"])) {
            $params["vote_average.gte"] = $filters["vote_average_gte"];
        }

        if (isset($filters["vote_average_lte"])) {
            $params["vote_average.lte"] = $filters["vote_average_lte"];
        }

        $client = new Client;

        $response = $client->request('GET', "{$this->baseUrl}/search/movie", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json',
            ],
            'query' => $params
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to search movies from TMDB API");
    }

    /**
     * Discover movies with filters
     */
    public function discoverMovies(array $filters = [], int $page = 1): array
    {
        $params = [
            "api_key" => $this->apiKey,
            "language" => "pt-BR",
            "page" => $page,
            "include_adult" => false,
            "sort_by" => $filters["sort_by"] ?? "popularity.desc",
        ];

        // Apply filters
        if (isset($filters["genre"])) {
            $params["with_genres"] = $filters["genre"];
        }

        if (isset($filters["year"])) {
            $params["year"] = $filters["year"];
        }

        if (isset($filters["vote_average_gte"])) {
            $params["vote_average.gte"] = $filters["vote_average_gte"];
        }

        if (isset($filters["vote_average_lte"])) {
            $params["vote_average.lte"] = $filters["vote_average_lte"];
        }

        if (isset($filters["release_date_gte"])) {
            $params["release_date.gte"] = $filters["release_date_gte"];
        }

        if (isset($filters["release_date_lte"])) {
            $params["release_date.lte"] = $filters["release_date_lte"];
        }

        $client = new Client;

        $response = $client->request('GET', "{$this->baseUrl}/discover/movie", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json',
            ],
            'query' => $params
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to discover movies from TMDB API");
    }

    /**
     * Get movie genres
     */
    public function getGenres(): array
    {

        $client = new Client;

        $response = $client->request('GET', "{$this->baseUrl}/genre/movie/list", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json'
            ],
            'query' => [
                "language" => "pt-BR",
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }


        throw new \Exception("Failed to fetch genres from TMDB API");
    }

    /**
     * Get trending movies
     */
    public function getTrendingMovies(string $timeWindow = "week", int $page = 1): array
    {
        $client = new Client;
        $response = $client->request('GET', "{$this->baseUrl}/trending/movie/{$timeWindow}", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json'
            ],
            'query' => [
                "language" => "pt-BR",
                "page" => $page,
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to fetch trending movies from TMDB API");
    }

    /**
     * Get top rated movies
     */
    public function getTopRatedMovies(int $page = 1): array
    {
        $client = new Client;
        $response = $client->request('GET', "{$this->baseUrl}/movie/top_rated", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json'
            ],
            'query' => [
                "language" => "pt-BR",
                "page" => $page,
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new \Exception("Failed to fetch top rated movies from TMDB API");
    }

    /**
     * Get image URL
     */
    public function getImageUrl(string $path, string $size = "w500"): string
    {
        return "{$this->imageBaseUrl}/{$size}{$path}";
    }
}
