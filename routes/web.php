<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\GameController::class, 'index'])->name('game.index');
Route::post('/guess', [App\Http\Controllers\GameController::class, 'guess'])->name('game.guess');
Route::post('/guess-letter', [App\Http\Controllers\GameController::class, 'guessLetter'])->name('game.guess_letter');
Route::get('/reset', [App\Http\Controllers\GameController::class, 'reset'])->name('game.reset');