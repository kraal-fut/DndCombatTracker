<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CombatCharacter extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_id',
        'name',
        'initiative',
        'original_initiative',
        'max_hp',
        'current_hp',
        'armor_class',
        'is_player',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_player' => 'boolean',
        ];
    }

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(CharacterCondition::class);
    }

    public function stateEffects(): HasMany
    {
        return $this->hasMany(CharacterStateEffect::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CharacterReaction::class);
    }

    public function hasUnusedReaction(): bool
    {
        return $this->reactions()->where('is_used', false)->exists();
    }

    public function hasUsedReaction(): bool
    {
        return $this->reactions()->where('is_used', true)->exists();
    }

    public function canUseReaction(): bool
    {
        return !$this->hasUsedReaction();
    }
}
