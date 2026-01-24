<?php

use App\DataTransferObjects\AddCharacterData;
use App\Enums\CombatStatus;
use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\CombatService;

test('can create a combat', function () {
    $service = new CombatService();
    $user = User::factory()->create(['role' => UserRole::DM]);

    $combat = $service->createCombat('Test Battle', $user->id);

    expect($combat)->toBeInstanceOf(Combat::class)
        ->and($combat->name)->toBe('Test Battle')
        ->and($combat->status)->toBe(CombatStatus::Active)
        ->and($combat->current_round)->toBe(1)
        ->and($combat->current_turn_index)->toBe(0);
});

test('can add character to combat', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $character = $service->addCharacter(
        combat: $combat,
        data: new AddCharacterData(
            name: 'Gandalf',
            initiative: 15,
            maxHp: 100,
            currentHp: 80,
            armorClass: 15,
            isPlayer: true
        )
    );

    expect($character)->toBeInstanceOf(CombatCharacter::class)
        ->and($character->name)->toBe('Gandalf')
        ->and($character->initiative)->toBe(15)
        ->and($character->max_hp)->toBe(100)
        ->and($character->current_hp)->toBe(80)
        ->and($character->armor_class)->toBe(15)
        ->and($character->is_player)->toBeTrue();
});

test('characters are ordered by initiative initially', function () {
    $service = new CombatService();
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = $service->createCombat('Test Battle', $user->id);

    $service->addCharacter($combat, new AddCharacterData('Fighter', 12));
    $service->addCharacter($combat, new AddCharacterData('Wizard', 18));
    $service->addCharacter($combat, new AddCharacterData('Rogue', 15));

    $characters = $combat->fresh()->characters;

    // All start with order 0, so sorted by initiative descending: Wizard (18), Rogue (15), Fighter (12)
    expect($characters->pluck('name')->toArray())->toBe(['Fighter', 'Wizard', 'Rogue']);
    expect($characters->pluck('initiative')->toArray())->toBe([12, 18, 15]);
});

test('can advance to next turn', function () {
    $service = new CombatService();
    $user = User::factory()->create(['role' => UserRole::DM]);
    $combat = $service->createCombat('Test Battle', $user->id);

    $fighter = $service->addCharacter($combat, new AddCharacterData('Fighter', 12));
    $wizard = $service->addCharacter($combat, new AddCharacterData('Wizard', 18));

    expect($combat->current_turn_index)->toBe(0);

    // All start with order 0, sorted by initiative: Wizard (18) comes before Fighter (12)
    $characters = $combat->fresh()->characters;
    expect($characters->first()->name)->toBe('Fighter');
    expect($characters->first()->order)->toBe(1);

    // After next turn, Wizard's order increases, Fighter now has lower order so comes first
    $service->nextTurn($combat);
    expect($combat->fresh()->current_turn_index)->toBe(0);

    $characters = $combat->fresh()->characters;
    expect($characters->first()->name)->toBe('Wizard');
    expect($characters->first()->order)->toBe(2);
    expect($characters->first()->initiative)->toBe(18); // Initiative unchanged!
});

test('can advance to next round', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $service->addCharacter($combat, new AddCharacterData('Fighter', 12));
    $service->addCharacter($combat, new AddCharacterData('Wizard', 18));

    $service->nextRound($combat);

    expect($combat->fresh()->current_round)->toBe(2)
        ->and($combat->fresh()->current_turn_index)->toBe(0);
});

test('can remove character from combat', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 12));

    expect($combat->fresh()->characters)->toHaveCount(1);

    $service->removeCharacter($character);

    expect($combat->fresh()->characters)->toHaveCount(0);
});

test('can remove all characters from combat', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $service->addCharacter($combat, new AddCharacterData('Fighter', 12));
    $service->addCharacter($combat, new AddCharacterData('Wizard', 18));

    expect($combat->fresh()->characters)->toHaveCount(2);

    $service->removeAllCharacters($combat);

    expect($combat->fresh()->characters)->toHaveCount(0)
        ->and($combat->fresh()->current_turn_index)->toBe(0)
        ->and($combat->fresh()->current_round)->toBe(1);
});

test('can pause and resume combat', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $service->pauseCombat($combat);
    expect($combat->fresh()->status)->toBe(CombatStatus::Paused);

    $service->resumeCombat($combat);
    expect($combat->fresh()->status)->toBe(CombatStatus::Active);
});

test('can end combat', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $service->endCombat($combat);

    expect($combat->fresh()->status)->toBe(CombatStatus::Completed);
});

test('reactions are reset when character turn ends', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    $character = $service->addCharacter($combat, new AddCharacterData('Fighter', 12));
    $reaction = $character->reactions()->create([
        'name' => 'Attack of Opportunity',
        'is_used' => true,
    ]);

    expect($reaction->fresh()->is_used)->toBeTrue();

    $service->nextTurn($combat);

    expect($reaction->fresh()->is_used)->toBeFalse();
});

test('round increments when last character acts', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    // Add 3 characters with different initiatives
    $wizard = $service->addCharacter($combat, new AddCharacterData('Wizard', 18));
    $rogue = $service->addCharacter($combat, new AddCharacterData('Rogue', 15));
    $fighter = $service->addCharacter($combat, new AddCharacterData('Fighter', 12));

    expect($combat->current_round)->toBe(1);

    // Wizard's turn (not last)
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(1);

    // Rogue's turn (not last)
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(1);

    // Fighter's turn (last - has lowest original_initiative)
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(2);
});

test('round increments correctly with tied initiatives', function () {
    $service = new CombatService();
    $combat = $service->createCombat('Test Battle');

    // Add 3 characters, two with same initiative
    $wizard = $service->addCharacter($combat, new AddCharacterData('Wizard', 18));
    $fighter1 = $service->addCharacter($combat, new AddCharacterData('Fighter 1', 12));
    $fighter2 = $service->addCharacter($combat, new AddCharacterData('Fighter 2', 12));

    expect($combat->current_round)->toBe(1);

    // Wizard's turn
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(1);

    // Fighter 1's turn (tied for lowest, but not last)
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(1);

    // Fighter 2's turn (also tied for lowest, IS last)
    $service->nextTurn($combat);
    expect($combat->fresh()->current_round)->toBe(2);
});

