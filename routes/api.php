<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::post('/questions/create', [GameController::class, 'create']);
Route::get('/questions/{joinCode}', [GameController::class, 'fetch']);





// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
