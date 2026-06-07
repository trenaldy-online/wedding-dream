<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('wedding_events', function (Blueprint $table) {
        $table->id();

        $table->foreignId('wedding_profile_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('event_name');
        $table->enum('event_side', ['cpw', 'cpp', 'both'])->default('both');

        $table->dateTime('event_date')->nullable();
        $table->string('venue_name')->nullable();
        $table->text('venue_address')->nullable();
        $table->text('note')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_events');
    }
};
