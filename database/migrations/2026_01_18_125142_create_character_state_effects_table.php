<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_state_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combat_character_id')->constrained()->cascadeOnDelete();
            $table->string('modifier_type');
            $table->string('name');
            $table->integer('value')->default(0);
            $table->string('advantage_state')->default(\App\Enums\AdvantageState::Normal->value);
            $table->text('description')->nullable();
            $table->integer('duration_rounds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_state_effects');
    }
};
