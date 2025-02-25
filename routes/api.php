<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::post('/questions/create', [GameController::class, 'create']);
Route::get('/questions/{joinCode}', [GameController::class, 'fetch']);
Route::get('/games/most-played', [GameController::class, 'mostPlayed']); // Add this line


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
