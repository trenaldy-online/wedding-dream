<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->foreignId('wedding_event_id')
                ->nullable()
                ->after('wedding_profile_id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('wedding_event_id')
                ->nullable()
                ->after('wedding_profile_id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('checklist_items', function (Blueprint $table) {
            $table->foreignId('wedding_event_id')
                ->nullable()
                ->after('wedding_profile_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wedding_event_id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wedding_event_id');
        });

        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wedding_event_id');
        });
    }
};