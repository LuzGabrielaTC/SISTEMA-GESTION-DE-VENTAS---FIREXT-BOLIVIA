<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\clienteController;
use App\Http\Controllers\Api\usuarioController;
use App\Http\Controllers\Api\notaEntregaController;
use App\Http\Controllers\Api\itemController;
use App\Http\Controllers\Api\servicioController;
use App\Http\Controllers\Api\productoController;
use App\Http\Controllers\Api\egresoController;
use App\Http\Controllers\Api\ingresoController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\notaRecepcionController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/me', [LoginController::class, 'me']);

    Route::get('/usuarios', [usuarioController::class, 'index']);
    Route::get('/usuarios/{id}', [usuarioController::class, 'show']);
    Route::post('/usuarios', [usuarioController::class, 'store']);
    Route::put('/usuarios/{id}', [usuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [usuarioController::class, 'destroy']);

    Route::get('/clientes', [clienteController::class, 'index']);
    Route::get('/clientes/{id}', [clienteController::class, 'show']);
    Route::post('/clientes', [clienteController::class, 'store']);
    Route::put('/clientes/{id}', [clienteController::class, 'update']);
    Route::delete('/clientes/{id}', [clienteController::class, 'destroy']);

    Route::get('/recepciones', [notaRecepcionController::class, 'index']);
    Route::get('/recepciones/{id}', [notaRecepcionController::class, 'show']);
    Route::post('/recepciones', [notaRecepcionController::class, 'store']);
    Route::put('/recepciones/{id}', [notaRecepcionController::class, 'update']);
    Route::delete('/recepciones/{id}', [notaRecepcionController::class, 'destroy']);

    Route::get('/entregas', [notaEntregaController::class, 'index']);
    Route::get('/entregas/{id}', [notaEntregaController::class, 'show']);
    Route::post('/entregas', [notaEntregaController::class, 'store']);
    Route::put('/entregas/{id}', [notaEntregaController::class, 'update']);
    Route::delete('/entregas/{id}', [notaEntregaController::class, 'destroy']);


    Route::apiResource('items', itemController::class);
    Route::apiResource('servicios', servicioController::class);
    Route::apiResource('productos', productoController::class);
    Route::apiResource('egresos', egresoController::class);
    Route::apiResource('ingresos', ingresoController::class);
});
