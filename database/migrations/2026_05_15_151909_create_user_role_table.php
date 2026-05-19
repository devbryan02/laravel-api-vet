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
        Schema::create('user_role', function (Blueprint $table) {
            $table -> ulid('id')->primary();
            $table -> foreignUlid("user_id") -> constrained()->cascadeOnDelete();
            $table -> foreignUlid('role_id') -> constrained()->cascadeOnDelete();
            $table -> timestamp("asigned_at")->useCurrent();
            $table -> unique(["user_id", "role_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
};
