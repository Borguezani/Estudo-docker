<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get current user profile
     */
    public function profile(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'bio' => $user->bio,
                    'birth_date' => $user->birth_date,
                    'favorite_genre' => $user->favorite_genre,
                    'is_public_profile' => $user->is_public_profile,
                    'created_at' => $user->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar perfil',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'bio' => 'nullable|string|max:1000',
                'birth_date' => 'nullable|date|before:today',
                'favorite_genre' => 'nullable|string|max:100',
                'is_public_profile' => 'boolean',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            // Handle avatar upload
            $avatarPath = $user->avatar;
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                    Storage::disk('public')->delete('avatars/' . $user->avatar);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
                $avatar->storeAs('avatars', $avatarName, 'public');
                $avatarPath = $avatarName;
            }

            $user->update([
                'name' => $request->name,
                'bio' => $request->bio,
                'birth_date' => $request->birth_date,
                'favorite_genre' => $request->favorite_genre,
                'is_public_profile' => $request->boolean('is_public_profile', true),
                'avatar' => $avatarPath,
            ]);

            return response()->json([
                'message' => 'Perfil atualizado com sucesso',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'bio' => $user->bio,
                    'birth_date' => $user->birth_date,
                    'favorite_genre' => $user->favorite_genre,
                    'is_public_profile' => $user->is_public_profile,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar perfil',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'error' => 'Senha atual incorreta'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Senha alterada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao alterar senha',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get public user profile
     */
    public function publicProfile(int $userId): JsonResponse
    {
        try {
            $user = User::where('id', $userId)
                ->where('is_public_profile', true)
                ->with(['publicMovieLists' => function ($query) {
                    $query->with(['items' => function ($itemQuery) {
                        $itemQuery->select('movie_list_id', 'movie_title', 'movie_poster_path', 'movie_vote_average');
                    }]);
                }])
                ->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Perfil não encontrado ou não é público'
                ], 404);
            }

            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'bio' => $user->bio,
                    'favorite_genre' => $user->favorite_genre,
                    'created_at' => $user->created_at,
                    'public_lists' => $user->publicMovieLists->map(function ($list) {
                        return [
                            'id' => $list->id,
                            'name' => $list->name,
                            'description' => $list->description,
                            'items_count' => $list->items->count(),
                            'created_at' => $list->created_at,
                            'recent_movies' => $list->items->take(5)->map(function ($item) {
                                return [
                                    'title' => $item->movie_title,
                                    'poster_path' => $item->movie_poster_path,
                                    'vote_average' => $item->movie_vote_average,
                                ];
                            }),
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar perfil público',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's public movie lists
     */
    public function userPublicLists(int $userId): JsonResponse
    {
        try {
            $user = User::where('id', $userId)
                ->where('is_public_profile', true)
                ->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não encontrado ou perfil não é público'
                ], 404);
            }

            $lists = $user->publicMovieLists()
                ->with(['items'])
                ->get()
                ->map(function ($list) {
                    return [
                        'id' => $list->id,
                        'name' => $list->name,
                        'description' => $list->description,
                        'items_count' => $list->items->count(),
                        'created_at' => $list->created_at,
                    ];
                });

            return response()->json([
                'data' => $lists,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar listas públicas do usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user avatar
     */
    public function deleteAvatar(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            $user->update(['avatar' => null]);

            return response()->json([
                'message' => 'Avatar removido com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover avatar',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
