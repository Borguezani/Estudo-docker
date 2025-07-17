<?php

namespace App\Http\Controllers;

use App\Models\MovieList;
use App\Models\MovieListItem;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MovieListController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->middleware('auth:api');
        $this->tmdbService = $tmdbService;
    }

    /**
     * Get all movie lists for authenticated user
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $lists = MovieList::with('items')
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($list) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'description' => $list->description,
                        'is_public' => $list->is_public,
                        'items_count' => $list->items->count(),
                        'created_at' => $list->created_at,
                        'updated_at' => $list->updated_at,
                    ];
                });

            return response()->json([
                'data' => $lists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar listas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get public movie lists
     */
    public function publicLists(): JsonResponse
    {
        try {
            $lists = MovieList::with(['items', 'user:id,name'])
                ->where('is_public', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($list) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'description' => $list->description,
                        'user' => $list->user->name,
                        'items_count' => $list->items->count(),
                        'created_at' => $list->created_at,
                    ];
                });

            return response()->json([
                'data' => $lists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar listas públicas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new movie list
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_public' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $list = MovieList::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'is_public' => $request->boolean('is_public', false),
            ]);

            return response()->json([
                'message' => 'Lista criada com sucesso',
                'data' => $list
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar lista',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific movie list
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $list = MovieList::with(['items', 'user:id,name'])
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhere('is_public', true);
                })
                ->findOrFail($id);

            return response()->json([
                'data' => [
                    'id' => $list->id,
                    'name' => $list->name,
                    'description' => $list->description,
                    'is_public' => $list->is_public,
                    'user' => $list->user->name,
                    'is_owner' => $list->user_id === $user->id,
                    'items' => $list->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'tmdb_movie_id' => $item->tmdb_movie_id,
                            'movie_title' => $item->movie_title,
                            'movie_poster_path' => $item->movie_poster_path,
                            'poster_url' => $item->poster_url,
                            'movie_overview' => $item->movie_overview,
                            'movie_release_date' => $item->movie_release_date,
                            'release_year' => $item->release_year,
                            'movie_vote_average' => $item->movie_vote_average,
                            'user_notes' => $item->user_notes,
                            'added_at' => $item->created_at,
                        ];
                    }),
                    'created_at' => $list->created_at,
                    'updated_at' => $list->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lista não encontrada',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update a movie list
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_public' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $list = MovieList::where('user_id', $user->id)->findOrFail($id);

            $list->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_public' => $request->boolean('is_public'),
            ]);

            return response()->json([
                'message' => 'Lista atualizada com sucesso',
                'data' => $list
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar lista',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a movie list
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $list = MovieList::where('user_id', $user->id)->findOrFail($id);

            $list->delete();

            return response()->json([
                'message' => 'Lista excluída com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao excluir lista',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a movie to a list
     */
    public function addMovie(Request $request, int $listId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tmdb_movie_id' => 'required|integer',
                'user_notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $list = MovieList::where('user_id', $user->id)->findOrFail($listId);

            // Get movie details from TMDB
            $movieData = $this->tmdbService->getMovieDetails($request->tmdb_movie_id);

            // Check if movie already exists in the list
            $existingItem = MovieListItem::where('movie_list_id', $list->id)
                ->where('tmdb_movie_id', $request->tmdb_movie_id)
                ->first();

            if ($existingItem) {
                return response()->json([
                    'error' => 'Filme já existe nesta lista'
                ], 409);
            }

            $item = MovieListItem::create([
                'movie_list_id' => $list->id,
                'tmdb_movie_id' => $request->tmdb_movie_id,
                'movie_title' => $movieData['title'],
                'movie_poster_path' => $movieData['poster_path'],
                'movie_overview' => $movieData['overview'],
                'movie_release_date' => $movieData['release_date'] ? date('Y-m-d', strtotime($movieData['release_date'])) : null,
                'movie_vote_average' => $movieData['vote_average'],
                'user_notes' => $request->user_notes,
            ]);

            return response()->json([
                'message' => 'Filme adicionado à lista com sucesso',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao adicionar filme à lista',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a movie from a list
     */
    public function removeMovie(int $listId, int $itemId): JsonResponse
    {
        try {
            $user = Auth::user();
            $list = MovieList::where('user_id', $user->id)->findOrFail($listId);
            
            $item = MovieListItem::where('movie_list_id', $list->id)
                ->findOrFail($itemId);

            $item->delete();

            return response()->json([
                'message' => 'Filme removido da lista com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover filme da lista',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update movie notes in a list
     */
    public function updateMovieNotes(Request $request, int $listId, int $itemId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $list = MovieList::where('user_id', $user->id)->findOrFail($listId);
            
            $item = MovieListItem::where('movie_list_id', $list->id)
                ->findOrFail($itemId);

            $item->update([
                'user_notes' => $request->user_notes
            ]);

            return response()->json([
                'message' => 'Anotações atualizadas com sucesso',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar anotações',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
