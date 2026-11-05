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
        Schema::create('venues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('owner_id');
            $table->string('name', 150);
            $table->string('address');
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('validation_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->unsignedInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('owner_id', 'idx_venues_owner');
            $table->index('city', 'idx_venues_city');
            $table->index('validation_status', 'idx_venues_status');
            $table->foreign('owner_id', 'fk_venues_owner')->references('id')->on('users');
            $table->foreign('validated_by', 'fk_venues_validator')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
