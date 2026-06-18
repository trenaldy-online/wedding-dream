<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wedding_sync_v2_change_logs')) {
            return;
        }

        Schema::create('wedding_sync_v2_change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module')->nullable()->index();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable()->index();
            $table->string('sync_key')->nullable()->index();
            $table->string('source')->nullable();
            $table->string('direction')->nullable();
            $table->string('action')->nullable();
            $table->json('old_payload')->nullable();
            $table->json('new_payload')->nullable();
            $table->string('changed_by')->nullable();
            $table->timestamp('changed_at')->nullable();
            $table->string('rollback_status')->default('available');
            $table->timestamp('rolled_back_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_sync_v2_change_logs');
    }
};
