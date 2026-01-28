<?php

namespace App\Models;

use App\Enums\CombatStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CharacterCondition;
use App\Models\CharacterStateEffect;

class Combat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'current_round',
        'current_turn_index',
    ];

    protected function casts(): array
    {
        return [
            'status' => CombatStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(CombatCharacter::class)->orderBy('order')->orderBy('initiative', 'desc')->orderBy('id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(CombatShare::class);
    }

    public function getCurrentCharacter(): ?CombatCharacter
    {
        $characters = $this->characters;

        if ($characters->isEmpty()) {
            return null;
        }

        return $characters->get($this->current_turn_index);
    }

    public function nextTurn(): void
    {
        $characters = $this->characters()->get();

        if ($characters->isEmpty()) {
            return;
        }

        $currentCharacter = $characters->get($this->current_turn_index);

        if (!$currentCharacter) {
            $this->current_turn_index = 0;
            $this->save();
            return;
        }

        // Store current character's original initiative before moving
        $currentOriginalInitiative = $currentCharacter->original_initiative;

        // Move current character to the end by setting their order to be very high
        $maxOrder = $characters->max('order') ?? 0;

        $currentCharacter->update([
            'order' => $maxOrder + 1,
        ]);

        // Reset reactions for the character whose turn just ended
        $currentCharacter->reactions()->update(['is_used' => false]);

        // Decrement durations for conditions and state effects
        $currentCharacter->conditions->each(function (CharacterCondition $condition) {
            if ($condition->duration_rounds !== null) {
                $condition->duration_rounds--;
                if ($condition->duration_rounds <= 0) {
                    $condition->delete();
                } else {
                    $condition->save();
                }
            }
        });

        $currentCharacter->stateEffects->each(function (CharacterStateEffect $effect) {
            if ($effect->duration_rounds !== null) {
                $effect->duration_rounds--;
                if ($effect->duration_rounds <= 0) {
                    $effect->delete();
                } else {
                    $effect->save();
                }
            }
        });

        // Check if round completed: Next character has higher original_initiative
        // This means we've cycled back to the beginning
        $nextCharacter = $this->characters()->first();
        if ($nextCharacter && $nextCharacter->original_initiative > $currentOriginalInitiative) {
            $this->nextRound();
        }

        // Keep the turn index at 0 (always the first character in the list)
        $this->current_turn_index = 0;

        $this->save();
    }

    public function nextRound(): void
    {
        $this->current_round++;
        $this->current_turn_index = 0;

        $this->characters->each(function (CombatCharacter $character) {
            $character->reactions()->update(['is_used' => false]);

            $character->conditions->each(function (CharacterCondition $condition) {
                if ($condition->duration_rounds !== null) {
                    $condition->duration_rounds--;
                    if ($condition->duration_rounds <= 0) {
                        $condition->delete();
                    } else {
                        $condition->save();
                    }
                }
            });

            $character->stateEffects->each(function (CharacterStateEffect $effect) {
                if ($effect->duration_rounds !== null) {
                    $effect->duration_rounds--;
                    if ($effect->duration_rounds <= 0) {
                        $effect->delete();
                    } else {
                        $effect->save();
                    }
                }
            });
        });

        $this->save();
    }

    protected function resetAllReactions(): void
    {
        $this->characters->each(function (CombatCharacter $character) {
            $character->reactions()->update(['is_used' => false]);
        });
    }

    public function hasPlayerCharacter(int $userId): bool
    {
        return $this->characters->where('user_id', $userId)->isNotEmpty();
    }

    public function getActiveShare(): ?CombatShare
    {
        return $this->shares()->active()->first();
    }

    public function generateShareLink(): CombatShare
    {
        $existingShare = $this->getActiveShare();

        if ($existingShare) {
            return $existingShare;
        }

        return $this->shares()->create([
            'share_token' => CombatShare::generateToken(),
            'is_active' => true,
        ]);
    }

    public function revokeShare(): void
    {
        $this->shares()->where('is_active', true)->update(['is_active' => false]);
    }

    public function regenerateShareLink(): CombatShare
    {
        $this->revokeShare();

        return $this->shares()->create([
            'share_token' => CombatShare::generateToken(),
            'is_active' => true,
        ]);
    }
}
