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
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reporter_id');
            $table->enum('target_type', ['USER', 'VENUE', 'ROOM', 'OTHER']);
            $table->unsignedInteger('target_id')->nullable();
            $table->text('reason');
            $table->enum('status', ['OPEN', 'IN_REVIEW', 'RESOLVED', 'DISMISSED'])->default('OPEN');
            $table->unsignedInteger('handled_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('resolved_at')->nullable();

            $table->index('status', 'idx_report_status');
            $table->foreign('reporter_id', 'fk_report_reporter')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('handled_by', 'fk_report_handler')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
