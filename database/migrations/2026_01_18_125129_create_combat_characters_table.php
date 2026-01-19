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
        Schema::create('combat_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combat_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('initiative');
            $table->integer('original_initiative');
            $table->integer('max_hp')->nullable();
            $table->integer('current_hp')->nullable();
            $table->integer('armor_class')->nullable();
            $table->boolean('is_player')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combat_characters');
    }
};
