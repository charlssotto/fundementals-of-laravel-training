<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [App\Http\Controllers\LoginController::class, 'show'])->name('login');
Route::get('/login', [App\Http\Controllers\LoginController::class, 'show'])->name('login.show');
Route::post('/login', [App\Http\Controllers\LoginController::class, 'authenticate'])->name('login.authenticate');
Route::get('/register', [App\Http\Controllers\RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'save'])->name('register.save');

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\GameSessionController::class, 'dashboard'])->name('game.dashboard');
    
    // Game Sessions
    Route::get('/game-session/create', [App\Http\Controllers\GameSessionController::class, 'create'])->name('game.session.create');
    Route::post('/game-session', [App\Http\Controllers\GameSessionController::class, 'store'])->name('game.session.store');
    Route::get('/game-session/{gameSession}', [App\Http\Controllers\GameSessionController::class, 'show'])->name('game.session.show');
    Route::get('/game-session/{gameSession}/history', [App\Http\Controllers\GameSessionController::class, 'history'])->name('game.session.history');
    
    // Game
    Route::get('/game', [App\Http\Controllers\GameController::class, 'index'])->name('game.index');
    Route::post('/guess', [App\Http\Controllers\GameController::class, 'guess'])->name('game.guess');
    Route::post('/guess-letter', [App\Http\Controllers\GameController::class, 'guessLetter'])->name('game.guessLetter');
    Route::get('/reset', [App\Http\Controllers\GameController::class, 'reset'])->name('game.reset');
    Route::get('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
});