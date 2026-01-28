<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if column doesn't exist before adding it
        if (!Schema::hasColumn('combat_characters', 'original_initiative')) {
            Schema::table('combat_characters', function (Blueprint $table) {
                $table->integer('original_initiative')->after('initiative')->default(0);
            });

            // Update existing records to set original_initiative equal to initiative
            \DB::table('combat_characters')->update([
                'original_initiative' => \DB::raw('initiative')
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('combat_characters', function (Blueprint $table) {
            $table->dropColumn('original_initiative');
        });
    }
};
