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
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('rater_id');
            $table->unsignedInteger('ratee_id');
            $table->unsignedTinyInteger('stars');
            $table->string('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['room_id', 'rater_id', 'ratee_id'], 'uq_rating_once');
            $table->index('ratee_id', 'idx_rating_ratee');
            $table->foreign('room_id', 'fk_rating_room')->references('id')->on('open_rooms')->cascadeOnDelete();
            $table->foreign('rater_id', 'fk_rating_rater')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ratee_id', 'fk_rating_ratee')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
