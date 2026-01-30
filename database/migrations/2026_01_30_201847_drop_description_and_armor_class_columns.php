<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('character_conditions', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('character_reactions', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('character_state_effects', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('combat_characters', function (Blueprint $table) {
            $table->dropColumn('armor_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('character_conditions', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('character_reactions', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('character_state_effects', function (Blueprint $table) {
            $table->text('description')->nullable();
        });

        Schema::table('combat_characters', function (Blueprint $table) {
            $table->integer('armor_class')->nullable();
        });
    }
};
