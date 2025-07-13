<?php

namespace App\Http\ApiRest;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait Validacao
{
    private $user = null;

    /**
     * Obter o usuário autenticado via Sanctum
     */
    protected function getUser(): ?User
    {
        if (is_null($this->user)) {
            $this->user = request()->user();
        }

        return $this->user;
    }

    /**
     * Verificar se o usuário está autenticado
     */
    protected function isAuthenticated(): bool
    {
        return !is_null($this->getUser());
    }

    /**
     * Verificar autenticação e retornar erro se não autenticado
     */
    protected function requireAuth(): ?JsonResponse
    {
        if (!$this->isAuthenticated()) {
            return response()->json([
                'success' => false,
                'message' => 'Token de acesso inválido ou expirado'
            ], 401);
        }

        return null;
    }

    /**
     * Obter o ID do usuário autenticado
     */
    protected function getUserId(): ?int
    {
        $user = $this->getUser();
        return $user ? $user->id : null;
    }

    /**
     * Resposta padronizada para sucesso
     */
    protected function successResponse($data = null, string $message = 'Operação realizada com sucesso', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Resposta padronizada para erro
     */
    protected function errorResponse(string $message = 'Erro interno', $errors = null, int $status = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Resposta padronizada para não autorizado
     */
    protected function unauthorizedResponse(string $message = 'Não autorizado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }

    /**
     * Resposta padronizada para não encontrado
     */
    protected function notFoundResponse(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 404);
    }
}


