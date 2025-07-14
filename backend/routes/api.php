<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota pública de teste
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando!', 'timestamp' => now()]);
});

// Rota de teste POST (precisa de CSRF token)
Route::post('/test', function (Request $request) {
    return response()->json([
        'message' => 'POST funcionando!',
        'data' => $request->all(),
        'timestamp' => now()
    ]);
});

// Rota para gerar token CSRF
// Route::get('sanctum/csrf-cookie', function () {
//     return response()->json(['csrf_token' => csrf_token()]);
// });

// ===== ROTAS PÚBLICAS DE AUTENTICAÇÃO =====
Route::prefix('auth')->group(function () {
    // Login
    Route::post('/login', [AuthController::class, 'login']);
    
    // Registro
    Route::post('/register', [AuthController::class, 'register']);
    
    // Rotas protegidas de autenticação
    Route::middleware('auth:api')->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Refresh token
        Route::post('/refresh', [AuthController::class, 'refresh']);
        
        // Dados do usuário autenticado
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// ===== ROTAS PROTEGIDAS =====
Route::middleware('auth:api')->group(function () {
    // Rota de exemplo protegida
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/protected', function () {
        return response()->json(['message' => 'Esta rota está protegida por JWT!']);
    });
    
    // Aqui você pode adicionar suas rotas protegidas que usarão o trait Validacao
    // Exemplo usando o ExampleController:

    
    // Ou rotas individuais:
    // Route::get('/examples', [ExampleController::class, 'index']);
    // Route::post('/examples', [ExampleController::class, 'store']);
    // Route::get('/examples/{id}', [ExampleController::class, 'show']);
    // Route::put('/examples/{id}', [ExampleController::class, 'update']);
    // Route::delete('/examples/{id}', [ExampleController::class, 'destroy']);
});
