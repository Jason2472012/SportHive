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
        Schema::create('sport_skill_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sport_id');
            $table->string('name', 50);
            $table->unsignedTinyInteger('level_order')->default(1);

            $table->unique(['sport_id', 'name'], 'uq_skill_per_sport');
            $table->foreign('sport_id', 'fk_skill_sport')->references('id')->on('sports')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_skill_levels');
    }
};
