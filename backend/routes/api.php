<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\ApiRest\PublicRoutes\Login;
use App\Http\ApiRest\PrivateRoutes\ExampleController;

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

// Rota para obter CSRF token (necessário para SPA)
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// ===== ROTAS PÚBLICAS DE AUTENTICAÇÃO =====
Route::prefix('auth')->group(function () {
    // Login
    Route::post('/login', [Login::class, 'login']);
    
    // Registro
    Route::post('/register', [Login::class, 'register']);
    
    // Rotas protegidas de autenticação
    Route::middleware('auth:sanctum')->group(function () {
        // Logout
        Route::post('/logout', [Login::class, 'logout']);
        
        // Logout de todos os dispositivos
        Route::post('/logout-all', [Login::class, 'logoutAll']);
        
        // Dados do usuário autenticado
        Route::get('/me', [Login::class, 'me']);
    });
});

// ===== ROTAS PROTEGIDAS =====
Route::middleware('auth:sanctum')->group(function () {
    // Rota de exemplo protegida
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/protected', function () {
        return response()->json(['message' => 'Esta rota está protegida por Sanctum!']);
    });
    
    // Aqui você pode adicionar suas rotas protegidas que usarão o trait Validacao
    // Exemplo usando o ExampleController:
    Route::apiResource('examples', ExampleController::class);
    
    // Ou rotas individuais:
    // Route::get('/examples', [ExampleController::class, 'index']);
    // Route::post('/examples', [ExampleController::class, 'store']);
    // Route::get('/examples/{id}', [ExampleController::class, 'show']);
    // Route::put('/examples/{id}', [ExampleController::class, 'update']);
    // Route::delete('/examples/{id}', [ExampleController::class, 'destroy']);
});
