<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('/v1')->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/forms', [FormController::class, 'store']);
    Route::get('/forms', [FormController::class, 'index']);
    Route::get('/forms/{slug}', [FormController::class, 'show']);
    Route::post('/forms/{slug}/questions', [QuestionController::class, 'store']);
    Route::delete('/forms/{slug}/questions/{id}', [QuestionController::class, 'destroy']);
    Route::post('/forms/{slug}/responses', [ResponseController::class, 'store']);
    Route::get('/forms/{slug}/responses', [ResponseController::class, 'index']);
});
