<?php

use App\Models\Combat;

test('can deal damage to character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
    ]);
    
    $response = $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 25,
        'change_type' => 'damage',
    ]);
    
    $response->assertRedirect();
    expect($character->fresh()->current_hp)->toBe(75);
});

test('can heal character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 50,
    ]);
    
    $response = $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 30,
        'change_type' => 'heal',
    ]);
    
    $response->assertRedirect();
    expect($character->fresh()->current_hp)->toBe(80);
});

test('hp cannot go below zero', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 20,
    ]);
    
    $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 50,
        'change_type' => 'damage',
    ]);
    
    expect($character->fresh()->current_hp)->toBe(0);
});

test('hp cannot exceed max hp when healing', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 90,
    ]);
    
    $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 50,
        'change_type' => 'heal',
    ]);
    
    expect($character->fresh()->current_hp)->toBe(100);
});

test('negative damage values are converted to positive', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
    ]);
    
    $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => -25,
        'change_type' => 'damage',
    ]);
    
    expect($character->fresh()->current_hp)->toBe(75);
});

test('negative heal values are converted to positive', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 50,
    ]);
    
    $this->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => -30,
        'change_type' => 'heal',
    ]);
    
    expect($character->fresh()->current_hp)->toBe(80);
});
