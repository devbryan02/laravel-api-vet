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
        Schema::create('audits', function (Blueprint $table) {
            $table-> ulid('id')->primary();
            $table->string("action");
            $table->string("entity_type");
            $table->string("entity_id");
            $table->json("changes")->nullable();
            $table->string("ip_address", 54)->nullable();
            $table->string("user_agent")->nullable();
            $table->foreignUlid("user_id")->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
