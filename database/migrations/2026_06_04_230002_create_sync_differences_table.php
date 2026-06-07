<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_differences', function (Blueprint $table) {
            $table->id();

            $table->string('module')->index();
            // Contoh: guests, budget_items, checklist_items, wedding_events

            $table->string('sheet_name')->nullable();
            // Contoh: INPUT_TAMU_CPW, INPUT_BUDGET

            $table->string('record_key')->nullable()->index();
            // Contoh: guest_key, budget_key, task_key, event_key

            $table->string('web_model')->nullable();
            // Contoh: App\Models\Guest

            $table->unsignedBigInteger('web_id')->nullable()->index();
            $table->unsignedInteger('sheet_row')->nullable();

            $table->string('difference_type')->index();
            // Contoh: sheet_only, web_only, different, conflict, dummy

            $table->json('sheet_payload')->nullable();
            $table->json('web_payload')->nullable();
            $table->json('field_differences')->nullable();

            $table->string('status')->default('pending')->index();
            // pending, resolved, ignored, approved_sheet_to_web, approved_web_to_sheet

            $table->text('note')->nullable();

            $table->timestamp('checked_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();

            $table->timestamps();

            $table->index(['module', 'difference_type']);
            $table->index(['module', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_differences');
    }
};
