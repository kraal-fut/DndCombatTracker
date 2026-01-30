<?php

namespace App\Models;

use App\Enums\AdvantageState;
use App\Enums\StateModifierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $combat_character_id
 * @property StateModifierType $modifier_type
 * @property string $name
 * @property int|null $value
 * @property AdvantageState $advantage_state
 * @property int|null $duration_rounds
 */
class CharacterStateEffect extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_character_id',
        'modifier_type',
        'name',
        'value',
        'advantage_state',
        'duration_rounds',
    ];

    protected function casts(): array
    {
        return [
            'modifier_type' => StateModifierType::class,
            'advantage_state' => AdvantageState::class,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<CombatCharacter, $this>
     */
    public function combatCharacter(): BelongsTo
    {
        return $this->belongsTo(CombatCharacter::class);
    }
}
