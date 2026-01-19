<?php

use App\DataTransferObjects\AddCharacterData;
use App\Enums\ConditionType;
use App\Models\Combat;
use App\Models\CombatCharacter;

test('can add condition to character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    $response = $this->post(route('characters.conditions.store', $character), [
        'condition_type' => ConditionType::Poisoned->value,
        'duration_rounds' => 3,
    ]);
    
    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('character_conditions', [
        'combat_character_id' => $character->id,
        'condition_type' => ConditionType::Poisoned->value,
        'duration_rounds' => 3,
    ]);
});

test('can add custom condition to character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    $response = $this->post(route('characters.conditions.store', $character), [
        'condition_type' => ConditionType::Custom->value,
        'custom_name' => 'Blessed',
        'description' => 'Extra damage on next hit',
    ]);
    
    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('character_conditions', [
        'combat_character_id' => $character->id,
        'condition_type' => ConditionType::Custom->value,
        'custom_name' => 'Blessed',
    ]);
});

test('can remove condition from character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
    ]);
    
    $response = $this->delete(route('characters.conditions.destroy', [$character, $condition]));
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('character_conditions', ['id' => $condition->id]);
});

test('conditions with duration are reduced on next round', function () {
    $service = new App\Services\CombatService();
    $combat = $service->createCombat('Test Battle');
    
    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 15));
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
        'duration_rounds' => 3,
    ]);
    
    expect($condition->duration_rounds)->toBe(3);
    
    $service->nextRound($combat);
    
    expect($condition->fresh()->duration_rounds)->toBe(2);
});

test('conditions are removed when duration reaches zero', function () {
    $service = new App\Services\CombatService();
    $combat = $service->createCombat('Test Battle');
    
    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 15));
    $condition = $character->conditions()->create([
        'condition_type' => ConditionType::Poisoned,
        'duration_rounds' => 1,
    ]);
    
    $service->nextRound($combat);
    
    expect($character->fresh()->conditions)->toHaveCount(0);
});
