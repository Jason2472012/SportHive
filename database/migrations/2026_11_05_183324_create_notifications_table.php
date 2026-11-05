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
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->enum('type', ['SCHEDULE_REMINDER', 'JOIN_REQUEST', 'JOIN_STATUS', 'NEW_CHAT', 'BOOKING', 'SYSTEM']);
            $table->string('title', 150)->nullable();
            $table->string('body')->nullable();
            $table->string('ref_type', 30)->nullable();
            $table->unsignedInteger('ref_id')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'read_at'], 'idx_notif_user_read');
            $table->foreign('user_id', 'fk_notif_user')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
