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
        Schema::create('courts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('venue_id');
            $table->unsignedInteger('sport_id');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['venue_id', 'name'], 'uq_court_name_per_venue');
            $table->index('sport_id', 'idx_courts_sport');
            $table->foreign('venue_id', 'fk_courts_venue')->references('id')->on('venues')->cascadeOnDelete();
            $table->foreign('sport_id', 'fk_courts_sport')->references('id')->on('sports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
