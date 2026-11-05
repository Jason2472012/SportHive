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
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('slot_id');
            $table->enum('status', ['PENDING', 'PAID', 'CANCELLED', 'EXPIRED', 'COMPLETED'])->default('PENDING');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('payment_ref', 100)->nullable();
            $table->string('e_ticket_code', 50)->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('active_slot_id')->nullable()->storedAs("CASE WHEN status IN ('PENDING', 'PAID', 'COMPLETED') THEN slot_id ELSE NULL END");

            $table->unique('active_slot_id', 'uq_booking_active_slot');
            $table->unique('e_ticket_code', 'uq_booking_ticket');
            $table->index('slot_id', 'idx_booking_slot');
            $table->index('status', 'idx_booking_status');
            $table->index('user_id', 'idx_booking_user');
            $table->foreign('slot_id', 'fk_booking_slot')->references('id')->on('time_slots');
            $table->foreign('user_id', 'fk_booking_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
