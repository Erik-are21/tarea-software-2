<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('teams.index'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/games', [GameController::class, 'index'])->name('games.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:manage-league')->group(function () {
        Route::resource('teams', TeamController::class)->except(['index', 'show']);
        Route::resource('players', PlayerController::class)->except(['index', 'show']);
        Route::resource('games', GameController::class)->except(['index', 'show']);
    });
});

require __DIR__ . '/auth.php';
