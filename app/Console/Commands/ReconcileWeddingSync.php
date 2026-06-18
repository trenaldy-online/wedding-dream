<?php

namespace App\Console\Commands;

use App\Models\SyncDifference;
use App\Models\SyncRun;
use App\Services\WeddingSync\GoogleSheetsClient;
use App\Services\WeddingSync\SyncHasher;
use App\Services\WeddingSync\SyncPayloadBuilder;
use Illuminate\Console\Command;
use Throwable;

class ReconcileWeddingSync extends Command
{
    protected $signature = 'wedding:sync-reconcile
                            {--modules=all : all, events, guests, budget_items, checklist_items}
                            {--fresh : Hapus pending sync_differences lama sebelum membuat laporan baru}';

    protected $description = 'Membandingkan data Google Sheet dan database web tanpa menimpa data';

    private array $moduleConfig = [
        'events' => [
            'sheet' => 'MASTER_EVENT',
            'range' => 'A1:Z1000',
        ],
        'guests_cpw' => [
            'module' => 'guests',
            'sheet' => 'INPUT_TAMU_CPW',
            'range' => 'A1:Z2000',
        ],
        'guests_cpp' => [
            'module' => 'guests',
            'sheet' => 'INPUT_TAMU_CPP',
            'range' => 'A1:Z2000',
        ],
        'budget_items' => [
            'sheet' => 'INPUT_BUDGET',
            'range' => 'A1:Z2000',
        ],
        'checklist_items' => [
            'sheet' => 'INPUT_PERSIAPAN',
            'range' => 'A1:Z2000',
        ],
        'documents' => [
            'module' => 'checklist_items',
            'sheet' => 'INPUT_DOKUMEN',
            'range' => 'A1:Z2000',
        ],
    ];

    public function handle(
        GoogleSheetsClient $sheets,
        SyncPayloadBuilder $payloadBuilder,
        SyncHasher $hasher
    ): int {
                if (! config('wedding-sync-legacy.enabled', false)) {
            $this->warn(config('wedding-sync-legacy.message', 'Sync lama sudah dinonaktifkan. Gunakan Wedding Sync V2.'));
            return self::SUCCESS;
        }

$selectedModules = $this->selectedModules();

        $run = SyncRun::create([
            'run_type' => 'manual_reconcile',
            'status' => 'running',
            'started_at' => now(),
        ]);

        $summary = [
            'total_sheet_rows' => 0,
            'total_web_rows' => 0,
            'total_same' => 0,
            'total_sheet_only' => 0,
            'total_web_only' => 0,
            'total_different' => 0,
            'total_conflict' => 0,
            'total_dummy' => 0,
            'total_errors' => 0,
        ];

        try {
            if ($this->option('fresh')) {
                SyncDifference::query()
                    ->where('status', 'pending')
                    ->delete();

                $this->warn('Pending sync_differences lama sudah dihapus.');
            }

            foreach ($selectedModules as $configKey) {
                $config = $this->moduleConfig[$configKey];

                $module = $config['module'] ?? $configKey;
                $sheetName = $config['sheet'];
                $range = $config['range'];

                $this->newLine();
                $this->info("Mengecek module: {$module}");
                $this->line("Sheet: {$sheetName}");

                $sheetRows = $sheets->getRowsWithHeader($sheetName, $range);
                $sheetPayloads = $payloadBuilder->sheetPayloads($module, $sheetName, $sheetRows);
                $webPayloads = $payloadBuilder->webPayloads($module, $sheetName);

                $summary['total_sheet_rows'] += count($sheetPayloads);
                $summary['total_web_rows'] += $webPayloads->count();

                $moduleResult = $this->compareModule(
                    module: $module,
                    sheetName: $sheetName,
                    sheetPayloads: $sheetPayloads,
                    webPayloads: $webPayloads->values()->all(),
                    payloadBuilder: $payloadBuilder,
                    hasher: $hasher
                );

                foreach ($moduleResult as $key => $value) {
                    $summary[$key] += $value;
                }

                $this->line('Sheet rows: ' . count($sheetPayloads));
                $this->line('Web rows  : ' . $webPayloads->count());
                $this->line('Same      : ' . $moduleResult['total_same']);
                $this->line('Sheet only: ' . $moduleResult['total_sheet_only']);
                $this->line('Web only  : ' . $moduleResult['total_web_only']);
                $this->line('Different : ' . $moduleResult['total_different']);
                $this->line('Dummy     : ' . $moduleResult['total_dummy']);
            }

            $run->update([
                'status' => 'completed',
                'finished_at' => now(),
                'total_sheet_rows' => $summary['total_sheet_rows'],
                'total_web_rows' => $summary['total_web_rows'],
                'total_same' => $summary['total_same'],
                'total_sheet_only' => $summary['total_sheet_only'],
                'total_web_only' => $summary['total_web_only'],
                'total_different' => $summary['total_different'],
                'total_conflict' => $summary['total_conflict'],
                'total_dummy' => $summary['total_dummy'],
                'total_errors' => $summary['total_errors'],
                'summary' => $summary,
            ]);

            $this->newLine();
            $this->info('Reconcile selesai.');
            $this->table(
                ['Metric', 'Total'],
                [
                    ['Sheet rows', $summary['total_sheet_rows']],
                    ['Web rows', $summary['total_web_rows']],
                    ['Same', $summary['total_same']],
                    ['Sheet only', $summary['total_sheet_only']],
                    ['Web only', $summary['total_web_only']],
                    ['Different', $summary['total_different']],
                    ['Dummy', $summary['total_dummy']],
                    ['Errors', $summary['total_errors']],
                ]
            );

            $this->newLine();
            $this->line('Cek hasil detail dengan:');
            $this->line('php artisan tinker');
            $this->line('\\App\\Models\\SyncDifference::where("status", "pending")->latest()->take(10)->get()->toArray();');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'total_errors' => 1,
                'error_message' => $e->getMessage(),
            ]);

            $this->error('Reconcile gagal.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function compareModule(
        string $module,
        string $sheetName,
        array $sheetPayloads,
        array $webPayloads,
        SyncPayloadBuilder $payloadBuilder,
        SyncHasher $hasher
    ): array {
        $result = [
            'total_same' => 0,
            'total_sheet_only' => 0,
            'total_web_only' => 0,
            'total_different' => 0,
            'total_conflict' => 0,
            'total_dummy' => 0,
            'total_errors' => 0,
        ];

        $fields = $payloadBuilder->compareFields($module);

        $webByKey = [];
        $webBySignature = [];
        $matchedWebIds = [];

        foreach ($webPayloads as $webPayload) {
            $webKey = $webPayload['record_key'] ?? null;
            $webSignature = $hasher->signature($module, $webPayload);

            if (!empty($webKey)) {
                $webByKey[$webKey] = $webPayload;
            }

            $webBySignature[$webSignature] = $webPayload;
        }

        foreach ($sheetPayloads as $sheetPayload) {
            $sheetKey = $sheetPayload['record_key'] ?? null;
            $sheetSignature = $hasher->signature($module, $sheetPayload);

            $matchedWebPayload = null;
            $matchType = null;

            if ($sheetKey && isset($webByKey[$sheetKey])) {
                $matchedWebPayload = $webByKey[$sheetKey];
                $matchType = 'key';
            } elseif (isset($webBySignature[$sheetSignature])) {
                $matchedWebPayload = $webBySignature[$sheetSignature];
                $matchType = 'signature';
            }

            if (!$matchedWebPayload) {
                $this->storeDifference(
                    module: $module,
                    sheetName: $sheetName,
                    differenceType: 'sheet_only',
                    sheetPayload: $sheetPayload,
                    webPayload: null,
                    fieldDifferences: null,
                    note: 'Data ada di Google Sheet tetapi belum ditemukan di database web.'
                );

                $result['total_sheet_only']++;
                continue;
            }

            $webId = $matchedWebPayload['web_id'] ?? null;

            if ($webId) {
                $matchedWebIds[] = $webId;
            }

            if (($matchedWebPayload['is_dummy'] ?? false) === true) {
                $this->storeDifference(
                    module: $module,
                    sheetName: $sheetName,
                    differenceType: 'dummy',
                    sheetPayload: $sheetPayload,
                    webPayload: $matchedWebPayload,
                    fieldDifferences: null,
                    note: 'Data web ditandai sebagai dummy.'
                );

                $result['total_dummy']++;
                continue;
            }

            $fieldDifferences = $hasher->diff($sheetPayload, $matchedWebPayload, $fields);

            if ($matchType === 'signature' && empty($matchedWebPayload['record_key'])) {
                $fieldDifferences['record_key'] = [
                    'sheet' => $sheetPayload['record_key'] ?? null,
                    'web' => null,
                ];
            }

            if (empty($fieldDifferences)) {
                $result['total_same']++;
                continue;
            }

            $this->storeDifference(
                module: $module,
                sheetName: $sheetName,
                differenceType: 'different',
                sheetPayload: $sheetPayload,
                webPayload: $matchedWebPayload,
                fieldDifferences: $fieldDifferences,
                note: 'Data ditemukan di kedua sumber, tetapi ada field yang berbeda.'
            );

            $result['total_different']++;
        }

        foreach ($webPayloads as $webPayload) {
            $webId = $webPayload['web_id'] ?? null;

            if ($webId && in_array($webId, $matchedWebIds, true)) {
                continue;
            }

            if (($webPayload['is_dummy'] ?? false) === true) {
                $this->storeDifference(
                    module: $module,
                    sheetName: $sheetName,
                    differenceType: 'dummy',
                    sheetPayload: null,
                    webPayload: $webPayload,
                    fieldDifferences: null,
                    note: 'Data hanya ada di web dan ditandai sebagai dummy.'
                );

                $result['total_dummy']++;
                continue;
            }

            /*
             * Data yang sudah dikirim ke WEB_EXPORT_* tidak perlu
             * dimunculkan berulang sebagai web_only.
             * Nanti setelah admin memindahkannya ke INPUT_*,
             * data akan cocok lagi lewat sheet_key.
             */
            if (($webPayload['sync_source'] ?? null) === 'web_exported') {
                continue;
            }

            $this->storeDifference(
                module: $module,
                sheetName: $sheetName,
                differenceType: 'web_only',
                sheetPayload: null,
                webPayload: $webPayload,
                fieldDifferences: null,
                note: 'Data ada di database web tetapi belum ditemukan di Google Sheet.'
            );

            $result['total_web_only']++;
        }

        return $result;
    }

    private function storeDifference(
        string $module,
        string $sheetName,
        string $differenceType,
        ?array $sheetPayload,
        ?array $webPayload,
        ?array $fieldDifferences,
        ?string $note
    ): void {
        SyncDifference::create([
            'module' => $module,
            'sheet_name' => $sheetName,
            'record_key' => $sheetPayload['record_key'] ?? $webPayload['record_key'] ?? null,
            'web_model' => $webPayload['web_model'] ?? null,
            'web_id' => $webPayload['web_id'] ?? null,
            'sheet_row' => $sheetPayload['_sheet_row'] ?? null,
            'difference_type' => $differenceType,
            'sheet_payload' => $sheetPayload,
            'web_payload' => $webPayload,
            'field_differences' => $fieldDifferences,
            'status' => 'pending',
            'note' => $note,
            'checked_at' => now(),
        ]);
    }

    private function selectedModules(): array
    {
        $input = (string) $this->option('modules');

        if ($input === 'all') {
            return array_keys($this->moduleConfig);
        }

        $requested = array_map('trim', explode(',', $input));

        $selected = [];

        foreach ($requested as $module) {
            if ($module === 'guests') {
                $selected[] = 'guests_cpw';
                $selected[] = 'guests_cpp';
                continue;
            }

            if (array_key_exists($module, $this->moduleConfig)) {
                $selected[] = $module;
            }
        }

        return array_values(array_unique($selected));
    }
}
