<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $observer = $this->app->make(\App\Observers\RealtimeCombatObserver::class);

        \App\Models\Combat::observe($observer);
        \App\Models\CombatCharacter::observe($observer);
        \App\Models\CharacterCondition::observe($observer);
        \App\Models\CharacterStateEffect::observe($observer);
        \App\Models\CharacterReaction::observe($observer);
    }
}
