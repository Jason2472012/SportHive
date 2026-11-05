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
        Schema::create('room_members', function (Blueprint $table) {
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('joined_at')->useCurrent();

            $table->primary(['room_id', 'user_id']);
            $table->index('user_id', 'idx_member_user');
            $table->foreign('room_id', 'fk_member_room')->references('id')->on('open_rooms')->cascadeOnDelete();
            $table->foreign('user_id', 'fk_member_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_members');
    }
};
