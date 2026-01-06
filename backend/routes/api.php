<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FreelancerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas de autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas públicas de consulta
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/freelancers', [FreelancerController::class, 'index']);
Route::get('/freelancers/{freelancer}', [FreelancerController::class, 'show']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);

// Rotas protegidas por autenticação
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Services (apenas freelancers podem criar, atualizar e deletar)
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    
    // Tickets (clientes criam, ambos visualizam e atualizam)
    Route::apiResource('tickets', TicketController::class);
    
    // Reviews (apenas em tickets completos)
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    
    // Freelancers (edição de perfil)
    Route::put('/freelancers/{freelancer}', [FreelancerController::class, 'update']);
    
    // Rotas administrativas
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/services', [AdminController::class, 'services']);
        Route::get('/tickets', [AdminController::class, 'tickets']);
        Route::get('/reviews', [AdminController::class, 'reviews']);
    });
    
    // Categorias (apenas admin pode criar, atualizar e deletar)
    Route::middleware('admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });
});
