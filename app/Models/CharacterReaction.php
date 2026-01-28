<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $combat_character_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_used
 */
class CharacterReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_character_id',
        'name',
        'description',
        'is_used',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
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
