<?php

use App\Models\Combat;
use App\Models\User;
use App\Enums\UserRole;

test('can deal damage to character', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 25,
        'change_type' => 'damage',
    ]);

    $response->assertRedirect();
    expect($character->fresh()->current_hp)->toBe(75);
});

test('can heal character', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 50,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 30,
        'change_type' => 'heal',
    ]);

    $response->assertRedirect();
    expect($character->fresh()->current_hp)->toBe(80);
});

test('hp cannot go below zero', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 20,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 50,
        'change_type' => 'damage',
    ]);

    expect($character->fresh()->current_hp)->toBe(0);
});

test('hp cannot exceed max hp when healing', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 90,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => 50,
        'change_type' => 'heal',
    ]);

    expect($character->fresh()->current_hp)->toBe(100);
});

test('negative damage values are converted to positive', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => -25,
        'change_type' => 'damage',
    ]);

    expect($character->fresh()->current_hp)->toBe(75);
});

test('negative heal values are converted to positive', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 50,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'hp_change' => -30,
        'change_type' => 'heal',
    ]);

    expect($character->fresh()->current_hp)->toBe(80);
});
