<?php

namespace App\Models;

use App\Enums\AdvantageState;
use App\Enums\StateModifierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterStateEffect extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_character_id',
        'modifier_type',
        'name',
        'value',
        'advantage_state',
        'description',
        'duration_rounds',
    ];

    protected function casts(): array
    {
        return [
            'modifier_type' => StateModifierType::class,
            'advantage_state' => AdvantageState::class,
        ];
    }

    public function combatCharacter(): BelongsTo
    {
        return $this->belongsTo(CombatCharacter::class);
    }
}
