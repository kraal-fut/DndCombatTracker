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
            $table->json('condition_immunities')->nullable()->after('vulnerabilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combat_characters', function (Blueprint $table) {
            $table->dropColumn('condition_immunities');
        });
    }
};
