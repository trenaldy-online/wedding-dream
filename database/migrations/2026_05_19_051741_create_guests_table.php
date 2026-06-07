<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('guests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('wedding_profile_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('name');
        $table->string('phone')->nullable();
        $table->text('address')->nullable();
        $table->string('group_name')->nullable();
        $table->integer('total_invited')->default(1);
        $table->enum('rsvp_status', ['pending', 'attend', 'not_attend'])->default('pending');
        $table->timestamp('invitation_sent_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
