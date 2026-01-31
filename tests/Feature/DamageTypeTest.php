<?php

use App\Models\Combat;
use App\Models\User;
use App\Models\CombatCharacter;
use App\Enums\UserRole;
use App\Enums\DamageType;
use App\Enums\HPUpdateType;

test('resistance reduces damage by half rounded down', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Resistant Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
        'resistances' => [DamageType::Fire->value],
    ]);

    // 21 Fire damage -> should be 10 damage actually dealt
    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'change_type' => HPUpdateType::Damage->value,
        'damages' => [
            ['amount' => 21, 'type' => DamageType::Fire->value],
        ],
    ]);

    expect($character->fresh()->current_hp)->toBe(90);
});

test('immunity reduces damage to zero', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Immune Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
        'immunities' => [DamageType::Fire->value],
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'change_type' => HPUpdateType::Damage->value,
        'damages' => [
            ['amount' => 50, 'type' => DamageType::Fire->value],
        ],
    ]);

    expect($character->fresh()->current_hp)->toBe(100);
});

test('vulnerability doubles damage', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Vulnerable Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
        'vulnerabilities' => [DamageType::Fire->value],
    ]);

    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'change_type' => HPUpdateType::Damage->value,
        'damages' => [
            ['amount' => 10, 'type' => DamageType::Fire->value],
        ],
    ]);

    expect($character->fresh()->current_hp)->toBe(80);
});

test('multiple damage types in one attack', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Mixed Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
        'resistances' => [DamageType::Fire->value],
        'vulnerabilities' => [DamageType::Cold->value],
    ]);

    // 10 Fire (resists to 5) + 10 Cold (vuln to 20) + 10 Acid (normal) = 35 total damage
    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'change_type' => HPUpdateType::Damage->value,
        'damages' => [
            ['amount' => 10, 'type' => DamageType::Fire->value],
            ['amount' => 10, 'type' => DamageType::Cold->value],
            ['amount' => 10, 'type' => DamageType::Acid->value],
        ],
    ]);

    expect($character->fresh()->current_hp)->toBe(65);
});

test('ignore resistance works correctly', function () {
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = Combat::create(['name' => 'Test Battle', 'user_id' => $user->id]);
    $character = $combat->characters()->create([
        'name' => 'Resistant Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 100,
        'user_id' => $user->id,
        'resistances' => [DamageType::Fire->value],
    ]);

    // 20 Fire damage with ignore_resist -> should deal 20 damage
    $this->actingAs($user)->post(route('combats.characters.update-hp', [$combat, $character]), [
        'change_type' => HPUpdateType::Damage->value,
        'damages' => [
            ['amount' => 20, 'type' => DamageType::Fire->value],
        ],
        'ignore_resist' => 1,
    ]);

    expect($character->fresh()->current_hp)->toBe(80);
});
