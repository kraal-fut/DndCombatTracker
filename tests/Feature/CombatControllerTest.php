<?php

use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Services\CombatService;

test('can view combats index page', function () {
    $combat1 = Combat::create(['name' => 'Battle 1']);
    $combat2 = Combat::create(['name' => 'Battle 2']);
    
    $response = $this->get(route('combats.index'));
    
    $response->assertStatus(200)
        ->assertSee('Battle 1')
        ->assertSee('Battle 2');
});

test('can view create combat page', function () {
    $response = $this->get(route('combats.create'));
    
    $response->assertStatus(200)
        ->assertSee('Create New Combat');
});

test('can create combat via form', function () {
    $response = $this->post(route('combats.store'), [
        'name' => 'Epic Battle',
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('combats', ['name' => 'Epic Battle']);
});

test('can view combat show page', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    
    $response = $this->get(route('combats.show', $combat));
    
    $response->assertStatus(200)
        ->assertSee('Test Battle');
});

test('can delete combat', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    
    $response = $this->delete(route('combats.destroy', $combat));
    
    $response->assertRedirect(route('combats.index'));
    $this->assertDatabaseMissing('combats', ['id' => $combat->id]);
});

test('can add character to combat', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    
    $response = $this->post(route('combats.characters.store', $combat), [
        'name' => 'Gandalf',
        'initiative' => 15,
        'max_hp' => 100,
        'current_hp' => 80,
        'armor_class' => 15,
        'is_player' => true,
    ]);
    
    $response->assertRedirect(route('combats.show', $combat));
    $this->assertDatabaseHas('combat_characters', [
        'combat_id' => $combat->id,
        'name' => 'Gandalf',
        'initiative' => 15,
    ]);
});

test('can remove character from combat', function () {
    $combat = Combat::create(['name' => 'Test Battle']);
    $character = $combat->characters()->create([
        'name' => 'Gandalf',
        'initiative' => 15,
        'original_initiative' => 15,
    ]);
    
    $response = $this->delete(route('combats.characters.destroy', [$combat, $character]));
    
    $response->assertRedirect();
    $this->assertDatabaseMissing('combat_characters', ['id' => $character->id]);
});

test('can advance combat turn', function () {
    $combat = Combat::create(['name' => 'Test Battle', 'current_turn_index' => 0]);
    $combat->characters()->create(['name' => 'Fighter', 'initiative' => 15, 'original_initiative' => 15]);
    $combat->characters()->create(['name' => 'Wizard', 'initiative' => 18, 'original_initiative' => 18]);
    
    $response = $this->post(route('combats.next-turn', $combat));
    
    $response->assertRedirect();
    // With the new system, turn index stays at 0 as characters rotate to bottom
    expect($combat->fresh()->current_turn_index)->toBe(0);
});

test('can advance combat round', function () {
    $combat = Combat::create(['name' => 'Test Battle', 'current_round' => 1]);
    
    $response = $this->post(route('combats.next-round', $combat));
    
    $response->assertRedirect();
    expect($combat->fresh()->current_round)->toBe(2);
});
