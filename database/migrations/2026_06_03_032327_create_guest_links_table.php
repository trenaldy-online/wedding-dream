<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wedding_profile_id')
                ->constrained('wedding_profiles')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('guest_id')->nullable();

            $table->string('guest_name');
            $table->string('guest_slug');
            $table->string('token', 64)->unique();

            $table->boolean('is_active')->default(true);

            // Tidak untuk lock, hanya batas normal sebelum warning.
            $table->unsignedTinyInteger('device_warning_threshold')->default(3);

            $table->unsignedInteger('open_count')->default(0);
            $table->unsignedInteger('unique_device_count')->default(0);

            $table->timestamp('first_opened_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();

            $table->boolean('is_suspected_shared')->default(false);
            $table->text('suspicion_reason')->nullable();

            $table->timestamps();

            $table->index(['guest_slug', 'token']);
            $table->index('guest_id');
            $table->index('is_suspected_shared');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_links');
    }
};