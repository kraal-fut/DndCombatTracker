<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CharacterConditionController;
use App\Http\Controllers\CharacterReactionController;
use App\Http\Controllers\CharacterStateEffectController;
use App\Http\Controllers\CombatCharacterController;
use App\Http\Controllers\CombatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedCombatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/combats-redirect', '/combats');

    Route::resource('combats', CombatController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::prefix('combats/{combat}')->name('combats.')->group(function () {
        Route::post('next-turn', [CombatController::class, 'nextTurn'])->name('next-turn');
        Route::post('next-round', [CombatController::class, 'nextRound'])->name('next-round');
        Route::post('start', [CombatController::class, 'start'])->name('start');
        Route::post('pause', [CombatController::class, 'pause'])->name('pause');
        Route::post('resume', [CombatController::class, 'resume'])->name('resume');
        Route::post('end', [CombatController::class, 'end'])->name('end');

        Route::prefix('characters')->name('characters.')->group(function () {
            Route::get('create', [CombatCharacterController::class, 'create'])->name('create');
            Route::post('/', [CombatCharacterController::class, 'store'])->name('store');
            Route::get('{character}/edit', [CombatCharacterController::class, 'edit'])->name('edit');
            Route::patch('{character}', [CombatCharacterController::class, 'update'])->name('update');
            Route::post('{character}/hp', [CombatCharacterController::class, 'updateHp'])->name('update-hp');
            Route::delete('{character}', [CombatCharacterController::class, 'destroy'])->name('destroy');
            Route::delete('/', [CombatCharacterController::class, 'destroyAll'])->name('destroy-all');
        });
    });

    Route::prefix('characters/{character}')->name('characters.')->group(function () {
        Route::prefix('conditions')->name('conditions.')->group(function () {
            Route::get('create', [CharacterConditionController::class, 'create'])->name('create');
            Route::post('/', [CharacterConditionController::class, 'store'])->name('store');
            Route::delete('{condition}', [CharacterConditionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('state-effects')->name('state-effects.')->group(function () {
            Route::get('create', [CharacterStateEffectController::class, 'create'])->name('create');
            Route::post('/', [CharacterStateEffectController::class, 'store'])->name('store');
            Route::delete('{stateEffect}', [CharacterStateEffectController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reactions')->name('reactions.')->group(function () {
            Route::get('create', [CharacterReactionController::class, 'create'])->name('create');
            Route::post('/', [CharacterReactionController::class, 'store'])->name('store');
            Route::post('{reaction}/use', [CharacterReactionController::class, 'use'])->name('use');
            Route::post('{reaction}/reset', [CharacterReactionController::class, 'reset'])->name('reset');
            Route::delete('{reaction}', [CharacterReactionController::class, 'destroy'])->name('destroy');
        });
    });
});

// Shared combat routes (authentication required)
Route::middleware('auth')->prefix('shared/combat/{token}')->group(function () {
    Route::get('/', [SharedCombatController::class, 'show'])->name('combats.shared');
    Route::get('/add-character', [SharedCombatController::class, 'addCharacter'])->name('combats.shared.add-character');
    Route::post('/add-character', [SharedCombatController::class, 'storeCharacter'])
        ->name('combats.shared.store-character');
    Route::patch('/character/{character}', [SharedCombatController::class, 'updateCharacter'])
        ->name('combats.shared.update-character');
});

// Share management routes (DM/Admin only)
Route::middleware('auth')->prefix('combats/{combat}/share')->name('combats.share.')->group(function () {
    Route::post('/', [CombatController::class, 'generateShare'])->name('generate');
    Route::delete('/', [CombatController::class, 'revokeShare'])->name('revoke');
    Route::post('/regenerate', [CombatController::class, 'regenerateShare'])->name('regenerate');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
