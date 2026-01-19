<?php

use App\Http\Controllers\CharacterConditionController;
use App\Http\Controllers\CharacterReactionController;
use App\Http\Controllers\CharacterStateEffectController;
use App\Http\Controllers\CombatCharacterController;
use App\Http\Controllers\CombatController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/combats');

Route::resource('combats', CombatController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

Route::prefix('combats/{combat}')->name('combats.')->group(function () {
    Route::post('next-turn', [CombatController::class, 'nextTurn'])->name('next-turn');
    Route::post('next-round', [CombatController::class, 'nextRound'])->name('next-round');
    Route::post('pause', [CombatController::class, 'pause'])->name('pause');
    Route::post('resume', [CombatController::class, 'resume'])->name('resume');
    Route::post('end', [CombatController::class, 'end'])->name('end');
    
    Route::prefix('characters')->name('characters.')->group(function () {
        Route::get('create', [CombatCharacterController::class, 'create'])->name('create');
        Route::post('/', [CombatCharacterController::class, 'store'])->name('store');
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
