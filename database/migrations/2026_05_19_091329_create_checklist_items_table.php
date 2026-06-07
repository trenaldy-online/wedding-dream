<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('checklist_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('wedding_profile_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->string('title');
        $table->string('category')->nullable();

        $table->enum('assigned_to', ['cpp', 'cpw', 'both'])->default('both');
        $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');

        $table->date('due_date')->nullable();
        $table->text('note')->nullable();
        $table->timestamp('completed_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
