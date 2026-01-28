<?php

namespace App\Observers;

use App\Events\CombatStateChanged;
use App\Models\Combat;
use App\Models\CombatCharacter;
use App\Models\CharacterCondition;
use App\Models\CharacterStateEffect;
use App\Models\CharacterReaction;
use Ecotone\Modelling\EventBus;
use Illuminate\Support\Facades\Log;

class RealtimeCombatObserver
{
    public function __construct(
        private EventBus $eventBus
    ) {
    }

    public function saved(object $model): void
    {
        $this->notify($model);
    }

    public function deleted(object $model): void
    {
        $this->notify($model);
    }

    private function notify(object $model): void
    {
        $combatId = $this->resolveCombatId($model);

        if ($combatId) {
            $this->eventBus->publish(new CombatStateChanged($combatId));
        }
    }

    private function resolveCombatId(object $model): ?int
    {
        if ($model instanceof Combat) {
            return $model->id;
        }

        if ($model instanceof CombatCharacter) {
            return $model->combat_id;
        }

        if ($model instanceof CharacterCondition || $model instanceof CharacterStateEffect || $model instanceof CharacterReaction) {
            return $model->combatCharacter?->combat_id;
        }

        return null;
    }
}
