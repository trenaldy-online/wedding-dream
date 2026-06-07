<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('budget_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('wedding_profile_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('category');
        $table->string('item_name');
        $table->decimal('estimated_amount', 15, 2)->default(0);
        $table->decimal('actual_amount', 15, 2)->default(0);
        $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
