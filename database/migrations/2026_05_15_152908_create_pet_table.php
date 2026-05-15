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
        Schema::create('pets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string("identifier")->unique();
            $table->string("name");
            $table->string("species");
            $table->string("race");
            $table->enum("gender", ["MACHO", "HEMBRA"]);
            $table->string("temperament");
            $table->enum("reproductive_condition", ["ENTERO", "CASTRADO"])->default("ENTERO");
            $table->string("color");
            $table->integer("years");
            $table->integer("months");
            $table->enum("status", ["ADOPTADO", "EN ADOPCIÓN"])->default("ADOPTADO");
            $table->foreignUlid("user_id")->constrained();
            $table->timestamp("created_at")->useCurrent();
            $table->timestamp("updated_at")->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
