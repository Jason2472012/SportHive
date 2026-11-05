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
        Schema::create('user_sport_profiles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('sport_id');
            $table->unsignedInteger('skill_level_id')->nullable();
            $table->boolean('is_favorite')->default(false);

            $table->primary(['user_id', 'sport_id']);
            $table->foreign('user_id', 'fk_usp_user')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('sport_id', 'fk_usp_sport')->references('id')->on('sports')->cascadeOnDelete();
            $table->foreign('skill_level_id', 'fk_usp_skill')->references('id')->on('sport_skill_levels')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sport_profiles');
    }
};
