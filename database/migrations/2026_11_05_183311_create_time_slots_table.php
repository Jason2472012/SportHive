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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('court_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->decimal('price', 12, 2);
            $table->enum('status', ['AVAILABLE', 'HELD', 'BOOKED', 'OFF'])->default('AVAILABLE');
            $table->dateTime('held_until')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['court_id', 'start_at', 'end_at'], 'uq_slot_court_time');
            $table->index('start_at', 'idx_slot_start');
            $table->index('status', 'idx_slot_status');
            $table->foreign('court_id', 'fk_slots_court')->references('id')->on('courts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
