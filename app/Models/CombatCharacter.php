<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $combat_id
 * @property int $user_id
 * @property string $name
 * @property int $initiative
 * @property int $original_initiative
 * @property int $max_hp
 * @property int $current_hp
 * @property int $temporary_hp
 * @property bool $is_player
 * @property int $order
 * @property \Illuminate\Database\Eloquent\Collection<int, CharacterCondition> $conditions
 * @property \Illuminate\Database\Eloquent\Collection<int, CharacterStateEffect> $stateEffects
 * @property \Illuminate\Database\Eloquent\Collection<int, CharacterReaction> $reactions
 */
class CombatCharacter extends Model
{
    use HasFactory;

    protected $fillable = [
        'combat_id',
        'user_id',
        'name',
        'initiative',
        'original_initiative',
        'max_hp',
        'current_hp',
        'temporary_hp',
        'resistances',
        'immunities',
        'vulnerabilities',
        'condition_immunities',
        'is_player',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_player' => 'boolean',
            'resistances' => 'array',
            'immunities' => 'array',
            'vulnerabilities' => 'array',
            'condition_immunities' => 'array',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Combat, $this>
     */
    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CharacterCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(CharacterCondition::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CharacterStateEffect, $this>
     */
    public function stateEffects(): HasMany
    {
        return $this->hasMany(CharacterStateEffect::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CharacterReaction, $this>
     */
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

    /**
     * Apply damage to the character, considering resistances, immunities, and vulnerabilities.
     *
     * @param array<int, array{amount: int, type: string}> $damages
     * @param bool $ignoreResist Whether to ignore resistances (e.g., Elemental Adept)
     */
    /**
     * @param \App\DTOs\DamageEntry[] $damages
     */
    public function applyDamage(array $damages, bool $ignoreResist = false): void
    {
        $totalDamage = 0;
        $resistances = $this->resistances ?? [];
        $immunities = $this->immunities ?? [];
        $vulnerabilities = $this->vulnerabilities ?? [];

        foreach ($damages as $damage) {
            $amount = abs($damage->amount);
            $type = $damage->type;

            if (in_array($type, $immunities)) {
                continue;
            }

            if (!$ignoreResist && in_array($type, $resistances)) {
                $amount = (int) floor($amount / 2);
            }

            if (in_array($type, $vulnerabilities)) {
                $amount *= 2;
            }

            $totalDamage += $amount;
        }

        $damageRemaining = $totalDamage;

        if ($this->temporary_hp > 0) {
            $tempDamage = min($this->temporary_hp, $damageRemaining);
            $this->temporary_hp -= $tempDamage;
            $damageRemaining -= $tempDamage;
        }

        if ($damageRemaining > 0) {
            $this->current_hp = max(0, $this->current_hp - $damageRemaining);
        }
    }

    public function applyHealing(int $amount): void
    {
        $this->current_hp = min($this->max_hp, $this->current_hp + abs($amount));
    }

    public function setTemporaryHp(int $amount): void
    {
        $this->temporary_hp = abs($amount);
    }

    public function hasConditionImmunity(string $type): bool
    {
        return in_array($type, $this->condition_immunities ?? []);
    }

    public function addCondition(array $data, bool $bypassImmunity = false): bool
    {
        if (!$bypassImmunity && $this->hasConditionImmunity($data['condition_type'])) {
            return false;
        }

        $this->conditions()->create($data);
        return true;
    }
}
