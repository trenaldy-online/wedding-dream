<?php

use App\Services\WeddingSyncV2\WeddingSyncV2BlankRowCleaner;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetColumnVisibility;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetGuide;

use App\Services\WeddingSyncV2\WeddingSyncV2HealthChecker;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetDropdowns;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetProtector;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetFormatter;

use App\Services\WeddingSyncV2\WeddingSyncV2RollbackService;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetImporter;

use App\Services\WeddingSyncV2\WeddingSyncV2WebExporter;

use App\Services\WeddingSyncV2\WeddingSyncV2SheetInitializer;

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('wedding:sync-v2-init-sheets', function () {
    app(WeddingSyncV2SheetInitializer::class)->init($this);
})->purpose('Initialize Wedding Sync V2 Google Sheets tabs and headers.');


Artisan::command('wedding:sync-v2-export-web {--module= : Export module tertentu saja, contoh: tamu_cpw} {--dry-run : Cek jumlah data tanpa menulis ke spreadsheet}', function () {
    app(WeddingSyncV2WebExporter::class)->export(
        $this,
        $this->option('module') ?: null,
        (bool) $this->option('dry-run')
    );
})->purpose('Export existing web data to Wedding Sync V2 Google Sheets.');


Artisan::command('wedding:sync-v2-import-sheet {--module= : Import module tertentu saja, contoh: tamu_cpw} {--dry-run : Cek perubahan tanpa menulis ke database}', function () {
    app(WeddingSyncV2SheetImporter::class)->import(
        $this,
        $this->option('module') ?: null,
        (bool) $this->option('dry-run')
    );
})->purpose('Import Wedding Sync V2 Google Sheets data to web database.');


Artisan::command('wedding:sync-v2-rollback {log_id : ID dari wedding_sync_v2_change_logs} {--dry-run : Preview rollback tanpa mengubah data} {--delete-created : Untuk rollback action=create dengan menghapus record yang dibuat}', function () {
    app(WeddingSyncV2RollbackService::class)->rollback(
        $this,
        (int) $this->argument('log_id'),
        (bool) $this->option('dry-run'),
        (bool) $this->option('delete-created')
    );
})->purpose('Rollback a Wedding Sync V2 change log entry.');


Artisan::command('wedding:sync-v2-format-sheets', function () {
    app(WeddingSyncV2SheetFormatter::class)->format($this);
})->purpose('Format Wedding Sync V2 Google Sheets for better readability.');


Artisan::command('wedding:sync-v2-protect-system-columns {--hard-lock : Kunci penuh kolom sistem, bukan hanya warning}', function () {
    app(WeddingSyncV2SheetProtector::class)->protect(
        $this,
        (bool) $this->option('hard-lock')
    );
})->purpose('Protect Wedding Sync V2 system columns in Google Sheets.');


Artisan::command('wedding:sync-v2-apply-dropdowns', function () {
    app(WeddingSyncV2SheetDropdowns::class)->apply($this);
})->purpose('Apply dropdown validation rules to Wedding Sync V2 Google Sheets.');


Artisan::command('wedding:sync-v2-health', function () {
    return app(WeddingSyncV2HealthChecker::class)->check($this);
})->purpose('Check Wedding Sync V2 configuration, spreadsheet access, and headers.');


Artisan::command('wedding:sync-v2-apply-guide', function () {
    app(WeddingSyncV2SheetGuide::class)->apply($this);
})->purpose('Apply input guide and header notes to Wedding Sync V2 Google Sheets.');

Artisan::command('wedding:sync-v2-fix-column-visibility', function () {
    app(WeddingSyncV2SheetColumnVisibility::class)->apply($this);
})->purpose('Hide Wedding Sync V2 system columns and show user-facing sync columns.');

Artisan::command('wedding:sync-v2-clear-blank-rows {--module=}', function () {
    app(WeddingSyncV2BlankRowCleaner::class)->apply($this, $this->option('module') ?: null);
})->purpose('Clear blank Wedding Sync V2 input rows that still contain sync/system leftovers.');

