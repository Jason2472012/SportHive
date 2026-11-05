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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('sender_id');
            $table->text('body');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['room_id', 'created_at'], 'idx_chat_room_time');
            $table->foreign('room_id', 'fk_chat_room')->references('id')->on('open_rooms')->cascadeOnDelete();
            $table->foreign('sender_id', 'fk_chat_sender')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
