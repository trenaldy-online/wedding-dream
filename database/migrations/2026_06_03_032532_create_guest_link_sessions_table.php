<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_link_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('guest_link_id')
                ->constrained('guest_links')
                ->cascadeOnDelete();

            $table->string('session_token', 80)->nullable();
            $table->string('device_hash', 64);

            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('opened_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();

            $table->unsignedInteger('open_count')->default(1);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedTinyInteger('max_scroll_percent')->default(0);

            $table->timestamps();

            $table->unique(['guest_link_id', 'device_hash']);
            $table->index('session_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_link_sessions');
    }
};