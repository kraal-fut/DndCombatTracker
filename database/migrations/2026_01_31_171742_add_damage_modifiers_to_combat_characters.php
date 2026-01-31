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
        Schema::table('combat_characters', function (Blueprint $table) {
            $table->json('resistances')->nullable()->after('temporary_hp');
            $table->json('immunities')->nullable()->after('resistances');
            $table->json('vulnerabilities')->nullable()->after('immunities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combat_characters', function (Blueprint $table) {
            $table->dropColumn(['resistances', 'immunities', 'vulnerabilities']);
        });
    }
};
