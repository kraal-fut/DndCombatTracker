<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('combat_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combat_id')->constrained()->onDelete('cascade');
            $table->string('share_token', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['share_token', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_shares');
    }
};
