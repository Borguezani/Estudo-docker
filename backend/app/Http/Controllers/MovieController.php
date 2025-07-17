<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Get popular movies
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 1);
            $movies = $this->tmdbService->getPopularMovies($page);
            
            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar filmes populares',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movie details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $movie = $this->tmdbService->getMovieDetails($id);
            
            return response()->json($movie);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar detalhes do filme',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search movies
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->query('q');
            $page = $request->query('page', 1);
            
            if (!$query) {
                return response()->json([
                    'error' => 'Parâmetro de busca é obrigatório'
                ], 400);
            }

            // Filtros opcionais
            $filters = [];
            if ($request->has('year')) {
                $filters['year'] = $request->query('year');
            }
            if ($request->has('genre')) {
                $filters['genre'] = $request->query('genre');
            }
            if ($request->has('sort_by')) {
                $filters['sort_by'] = $request->query('sort_by');
            }
            if ($request->has('vote_average_gte')) {
                $filters['vote_average_gte'] = $request->query('vote_average_gte');
            }
            if ($request->has('vote_average_lte')) {
                $filters['vote_average_lte'] = $request->query('vote_average_lte');
            }

            $movies = $this->tmdbService->searchMovies($query, $page, $filters);
            
            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao pesquisar filmes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Discover movies with filters
     */
    public function discover(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 1);
            
            // Filtros
            $filters = [];
            if ($request->has('genre')) {
                $filters['genre'] = $request->query('genre');
            }
            if ($request->has('year')) {
                $filters['year'] = $request->query('year');
            }
            if ($request->has('sort_by')) {
                $filters['sort_by'] = $request->query('sort_by');
            }
            if ($request->has('vote_average_gte')) {
                $filters['vote_average_gte'] = $request->query('vote_average_gte');
            }
            if ($request->has('vote_average_lte')) {
                $filters['vote_average_lte'] = $request->query('vote_average_lte');
            }
            if ($request->has('release_date_gte')) {
                $filters['release_date_gte'] = $request->query('release_date_gte');
            }
            if ($request->has('release_date_lte')) {
                $filters['release_date_lte'] = $request->query('release_date_lte');
            }

            $movies = $this->tmdbService->discoverMovies($filters, $page);
            
            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao descobrir filmes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending movies
     */
    public function trending(Request $request): JsonResponse
    {
        try {
            $timeWindow = $request->query('time_window', 'week'); // week or day
            $page = $request->query('page', 1);
            
            $movies = $this->tmdbService->getTrendingMovies($timeWindow, $page);
            
            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar filmes em alta',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top rated movies
     */
    public function topRated(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 1);
            $movies = $this->tmdbService->getTopRatedMovies($page);
            
            return response()->json($movies);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar filmes mais bem avaliados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movie genres
     */
    public function genres(): JsonResponse
    {
        try {
            $genres = $this->tmdbService->getGenres();
            
            return response()->json($genres);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar gêneros',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
