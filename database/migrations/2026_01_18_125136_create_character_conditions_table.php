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
        Schema::create('character_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combat_character_id')->constrained()->cascadeOnDelete();
            $table->string('condition_type');
            $table->string('custom_name')->nullable();
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
        Schema::dropIfExists('character_conditions');
    }
};
