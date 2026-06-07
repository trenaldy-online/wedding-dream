<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('wedding_profiles', function (Blueprint $table) {
        $table->id();
        $table->string('groom_name');
        $table->string('bride_name');
        $table->string('slug')->unique();
        $table->dateTime('event_date')->nullable();
        $table->string('venue_name')->nullable();
        $table->text('venue_address')->nullable();
        $table->text('opening_text')->nullable();
        $table->text('story')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_profiles');
    }
};
