<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_runs', function (Blueprint $table) {
            $table->id();

            $table->string('run_type')->default('nightly_reconcile');
            // nightly_reconcile, manual_reconcile, export_dummy, clear_dummy

            $table->string('status')->default('running')->index();
            // running, completed, failed

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->unsignedInteger('total_sheet_rows')->default(0);
            $table->unsignedInteger('total_web_rows')->default(0);

            $table->unsignedInteger('total_same')->default(0);
            $table->unsignedInteger('total_sheet_only')->default(0);
            $table->unsignedInteger('total_web_only')->default(0);
            $table->unsignedInteger('total_different')->default(0);
            $table->unsignedInteger('total_conflict')->default(0);
            $table->unsignedInteger('total_dummy')->default(0);
            $table->unsignedInteger('total_errors')->default(0);

            $table->json('summary')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['run_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_runs');
    }
};
