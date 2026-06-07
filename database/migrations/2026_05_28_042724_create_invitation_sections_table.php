<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wedding_profile_id')
                ->constrained('wedding_profiles')
                ->cascadeOnDelete();

            $table->string('section_key');
            $table->string('section_label');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('content')->nullable();

            $table->timestamps();

            $table->unique(['wedding_profile_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_sections');
    }
};