<?php

namespace App\Models;

use App\Enums\ConditionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ConditionType $condition_type
 * @property string|null $custom_name
 */
class CharacterCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_character_id',
        'condition_type',
        'custom_name',
        'duration_rounds',
    ];

    protected function casts(): array
    {
        return [
            'condition_type' => ConditionType::class,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<CombatCharacter, $this>
     */
    public function combatCharacter(): BelongsTo
    {
        return $this->belongsTo(CombatCharacter::class);
    }

    public function getDisplayName(): string
    {
        if ($this->condition_type === ConditionType::Custom) {
            return $this->custom_name ?? 'Custom Condition';
        }

        return $this->condition_type->label();
    }
}
