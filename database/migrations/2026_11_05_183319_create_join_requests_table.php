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
        Schema::create('join_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('user_id');
            $table->string('message')->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED'])->default('PENDING');
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('responded_at')->nullable();

            $table->unique(['room_id', 'user_id'], 'uq_join_once');
            $table->index('user_id', 'idx_join_user');
            $table->index('status', 'idx_join_status');
            $table->foreign('room_id', 'fk_join_room')->references('id')->on('open_rooms')->cascadeOnDelete();
            $table->foreign('user_id', 'fk_join_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_requests');
    }
};
