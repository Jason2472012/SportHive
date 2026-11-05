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
        Schema::create('open_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('booking_id');
            $table->string('title', 150)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['FUN', 'TOURNAMENT'])->default('FUN');
            $table->unsignedSmallInteger('quota');
            $table->decimal('fee_per_person', 12, 2)->default(0);
            $table->unsignedInteger('skill_target_id')->nullable();
            $table->enum('status', ['OPEN', 'FULL', 'CLOSED', 'CANCELLED'])->default('OPEN');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('booking_id', 'uq_room_booking');
            $table->index('type', 'idx_room_type');
            $table->index('status', 'idx_room_status');
            $table->foreign('booking_id', 'fk_room_booking')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreign('skill_target_id', 'fk_room_skill')->references('id')->on('sport_skill_levels')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_rooms');
    }
};
