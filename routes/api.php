<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;


Route::apiResource('hello', HelloWorldController::class);
use App\Http\Controllers\JsonController;
use App\Http\Controllers\CsvController;


Route::get('/json', [JsonController::class, 'index']);
Route::post('/json', [JsonController::class, 'store']);
Route::get('/json/{id}', [JsonController::class, 'show']);
Route::put('/json/{id}', [JsonController::class, 'update']);
Route::delete('/json/{id}', [JsonController::class, 'destroy']);

Route::get('/csv', [CsvController::class, 'index']); // Lista todos los archivos CSV
Route::post('/csv', [CsvController::class, 'store']); // Crea un nuevo archivo CSV
Route::get('/csv/{id}', [CsvController::class, 'show']); // Muestra el contenido de un archivo CSV
Route::put('/csv/{id}', [CsvController::class, 'update']); // Actualiza un archivo CSV
Route::delete('/csv/{id}', [CsvController::class, 'destroy']); // Elimina un archivo CSV
