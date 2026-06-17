<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            if (! Schema::hasColumn('checklist_items', 'priority')) {
                $table->string('priority')->nullable()->after('assigned_to');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE checklist_items
                MODIFY status ENUM('todo','in_progress','done','postponed','cancelled')
                NOT NULL DEFAULT 'todo'
            ");
        }
    }

    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_items', 'priority')) {
                $table->dropColumn('priority');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE checklist_items
                MODIFY status ENUM('todo','in_progress','done')
                NOT NULL DEFAULT 'todo'
            ");
        }
    }
};
