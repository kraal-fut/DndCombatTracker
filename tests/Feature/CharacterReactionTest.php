<?php

use App\DataTransferObjects\AddCharacterData;
use App\Models\Combat;

test('can add reaction to character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    $response = $this->post(route('characters.reactions.store', $character), [
        'name' => 'Attack of Opportunity',
        'description' => 'Attack when enemy moves away',
    ]);
    
    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('character_reactions', [
        'combat_character_id' => $character->id,
        'name' => 'Attack of Opportunity',
        'is_used' => false,
    ]);
});

test('can mark reaction as used', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => false,
    ]);
    
    $response = $this->post(route('characters.reactions.use', [$character, $reaction]));
    
    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeTrue();
});

test('can reset reaction', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => true,
    ]);
    
    $response = $this->post(route('characters.reactions.reset', [$character, $reaction]));
    
    $response->assertRedirect();
    expect($reaction->fresh()->is_used)->toBeFalse();
});

test('can remove reaction from character', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    $reaction = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
    ]);
    
    $response = $this->delete(route('characters.reactions.destroy', [$character, $reaction]));
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('character_reactions', ['id' => $reaction->id]);
});

test('reactions are reset on next round', function () {
    $service = new App\Services\CombatService();
    $combat = $service->createCombat('Test Battle');
    
    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 15));
    $reaction1 = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => true,
    ]);
    $reaction2 = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => true,
    ]);
    
    $service->nextRound($combat);
    
    expect($reaction1->fresh()->is_used)->toBeFalse()
        ->and($reaction2->fresh()->is_used)->toBeFalse();
});

test('character can check if has unused reaction', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    expect($character->hasUnusedReaction())->toBeFalse();
    
    $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => true,
    ]);
    
    expect($character->fresh()->hasUnusedReaction())->toBeFalse();
    
    $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => false,
    ]);
    
    expect($character->fresh()->hasUnusedReaction())->toBeTrue();
});

test('character can only use one reaction per round', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Fighter',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    $reaction1 = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => false,
    ]);
    
    $reaction2 = $character->reactions()->create([
        'name' => 'Shield',
        'is_used' => false,
    ]);
    
    expect($character->canUseReaction())->toBeTrue();
    
    $this->post(route('characters.reactions.use', [$character, $reaction1]));
    
    expect($character->fresh()->canUseReaction())->toBeFalse();
    
    $response = $this->post(route('characters.reactions.use', [$character, $reaction2]));
    
    $response->assertRedirect();
    expect($reaction2->fresh()->is_used)->toBeFalse();
    expect(session('error'))->toContain('already used their reaction');
});
