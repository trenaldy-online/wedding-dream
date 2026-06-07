<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'wedding_events',
        'guests',
        'budget_items',
        'checklist_items',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            $this->addSyncColumns($tableName);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            $this->dropSyncColumns($tableName);
        }
    }

    private function addSyncColumns(string $tableName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'sheet_key')) {
                $table->string('sheet_key')->nullable()->index();
            }

            if (!Schema::hasColumn($tableName, 'sheet_row')) {
                $table->unsignedInteger('sheet_row')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'sync_source')) {
                $table->string('sync_source')->nullable()->comment('web, sheet, dummy');
            }

            if (!Schema::hasColumn($tableName, 'sheet_hash')) {
                $table->string('sheet_hash', 64)->nullable();
            }

            if (!Schema::hasColumn($tableName, 'web_hash')) {
                $table->string('web_hash', 64)->nullable();
            }

            if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'last_checked_at')) {
                $table->timestamp('last_checked_at')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'is_dummy')) {
                $table->boolean('is_dummy')->default(false);
            }

            if (!Schema::hasColumn($tableName, 'sync_note')) {
                $table->text('sync_note')->nullable();
            }
        });
    }

    private function dropSyncColumns(string $tableName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $columns = [
                'sheet_key',
                'sheet_row',
                'sync_source',
                'sheet_hash',
                'web_hash',
                'last_synced_at',
                'last_checked_at',
                'is_dummy',
                'sync_note',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
