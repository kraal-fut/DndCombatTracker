<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function combatCharacter(): BelongsTo
    {
        return $this->belongsTo(CombatCharacter::class);
    }
}
