<?php

namespace App\Services\WeddingSyncV2;

use App\Models\WeddingEvent;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WeddingSyncV2SheetImporter
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private Carbon $now;

    public function import(Command $command, ?string $onlyModule = null, bool $dryRun = false): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $modules = (array) config('wedding-sync-v2.modules', []);

        if ($onlyModule) {
            if (!array_key_exists($onlyModule, $modules)) {
                throw new RuntimeException("Module {$onlyModule} tidak ditemukan di config wedding-sync-v2.");
            }

            $modules = [
                $onlyModule => $modules[$onlyModule],
            ];
        }

        $this->sheets = new Sheets($this->makeGoogleClient());
        $this->now = Carbon::now(config('wedding-sync-v2.timezone', config('app.timezone', 'Asia/Jakarta')));

        $command->info($dryRun ? 'DRY RUN import spreadsheet v2 ke web...' : 'Mulai import spreadsheet v2 ke web...');
        $command->newLine();

        foreach ($modules as $module => $definition) {
            $sheet = $definition['sheet'] ?? null;

            if (!$sheet) {
                $command->warn("SKIP {$module}: nama sheet kosong.");
                continue;
            }

            $rows = $this->readRows($sheet);

            $created = 0;
            $updated = 0;
            $unchanged = 0;
            $skipped = 0;
            $deleted = 0;

            foreach ($rows as $row) {
                if ($this->isBlankBusinessRow($row, $definition)) {
                    $modelClassForDelete = $definition['model'] ?? null;
                    $recordForDelete = ($modelClassForDelete && class_exists($modelClassForDelete))
                        ? $this->findRecord($modelClassForDelete, $row)
                        : null;

                    if ($recordForDelete && $this->shouldDeleteFromSheet($definition, $row)) {
                        if (! $dryRun) {
                            $this->deleteFromSheet($module, $definition, $row, $recordForDelete);
                        }

                        $deleted++;
                        continue;
                    }

                    $skipped++;
                    continue;
                }

                $result = $this->importRow($module, $definition, $row, $dryRun);

                if ($result === 'created') {
                    $created++;
                } elseif ($result === 'updated') {
                    $updated++;
                } elseif ($result === 'unchanged') {
                    $unchanged++;
                } elseif ($result === 'deleted') {
                    $deleted++;
                } else {
                    $skipped++;
                }
            }

            $command->line("{$module} ← {$sheet}: created={$created}, updated={$updated}, deleted={$deleted}, unchanged={$unchanged}, skipped={$skipped}");
        }

        $command->newLine();
        $command->info($dryRun ? 'DRY RUN selesai.' : 'Import spreadsheet v2 ke web selesai.');
    }


    public function importSheetRow(string $sheetName, int $rowNumber, bool $dryRun = false): array
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        if ($rowNumber <= 1) {
            return [
                'sheet' => $sheetName,
                'row_number' => $rowNumber,
                'module' => null,
                'result' => 'skipped',
                'message' => 'Header row tidak diproses.',
            ];
        }

        $modules = (array) config('wedding-sync-v2.modules', []);
        $matchedModule = null;
        $matchedDefinition = null;

        foreach ($modules as $module => $definition) {
            if (($definition['sheet'] ?? null) === $sheetName) {
                $matchedModule = $module;
                $matchedDefinition = $definition;
                break;
            }
        }

        if (!$matchedModule || !$matchedDefinition) {
            throw new RuntimeException("Sheet {$sheetName} tidak terdaftar di config wedding-sync-v2.");
        }

        $this->sheets = new Sheets($this->makeGoogleClient());
        $this->now = Carbon::now(config('wedding-sync-v2.timezone', config('app.timezone', 'Asia/Jakarta')));

        $row = $this->readSingleRow($sheetName, $rowNumber);

        if (!$row) {
            return [
                'sheet' => $sheetName,
                'row_number' => $rowNumber,
                'module' => $matchedModule,
                'result' => 'skipped',
                'message' => 'Row kosong atau tidak ditemukan.',
            ];
        }

        if ($this->isBlankBusinessRow($row, $matchedDefinition)) {
            $modelClassForDelete = $matchedDefinition['model'] ?? null;
            $recordForDelete = ($modelClassForDelete && class_exists($modelClassForDelete))
                ? $this->findRecord($modelClassForDelete, $row)
                : null;

            if ($recordForDelete && $this->shouldDeleteFromSheet($matchedDefinition, $row)) {
                $result = $dryRun
                    ? 'deleted'
                    : $this->deleteFromSheet($matchedModule, $matchedDefinition, $row, $recordForDelete);

                return [
                    'sheet' => $sheetName,
                    'row_number' => $rowNumber,
                    'module' => $matchedModule,
                    'result' => $result,
                    'dry_run' => $dryRun,
                    'message' => "Row {$rowNumber} processed as {$result}.",
                ];
            }

            return [
                'sheet' => $sheetName,
                'row_number' => $rowNumber,
                'module' => $matchedModule,
                'result' => 'skipped',
                'message' => 'Business row kosong.',
            ];
        }

        $result = $this->importRow($matchedModule, $matchedDefinition, $row, $dryRun);

        return [
            'sheet' => $sheetName,
            'row_number' => $rowNumber,
            'module' => $matchedModule,
            'result' => $result,
            'dry_run' => $dryRun,
            'message' => "Row {$rowNumber} processed as {$result}.",
        ];
    }

    private function makeGoogleClient(): Client
    {
        $jsonPath = config('google-sheets.service_account_json');

        if (!$jsonPath) {
            $envPath = env('GOOGLE_SERVICE_ACCOUNT_JSON', 'storage/app/google/service-account.json');
            $jsonPath = str_starts_with($envPath, 'storage/')
                ? storage_path(substr($envPath, strlen('storage/')))
                : base_path($envPath);
        }

        if (!is_file($jsonPath)) {
            throw new RuntimeException("Service account JSON tidak ditemukan: {$jsonPath}");
        }

        $client = new Client();
        $client->setApplicationName('Wedding Dream Sync V2');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }


    private function readSingleRow(string $sheet, int $rowNumber): ?array
    {
        $headerRange = $this->quoteSheet($sheet) . '!A1:ZZ1';
        $rowRange = $this->quoteSheet($sheet) . "!A{$rowNumber}:ZZ{$rowNumber}";

        $headerResponse = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $headerRange);
        $rowResponse = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $rowRange);

        $headerValues = $headerResponse->getValues() ?? [];
        $rowValues = $rowResponse->getValues() ?? [];

        if (empty($headerValues[0]) || empty($rowValues[0])) {
            return null;
        }

        $headers = array_map(fn ($value) => trim((string) $value), $headerValues[0]);
        $line = $rowValues[0];

        $hasValue = collect($line)->contains(fn ($value) => trim((string) $value) !== '');

        if (!$hasValue) {
            return null;
        }

        $assoc = [
            '_row_number' => $rowNumber,
            '_headers' => $headers,
            '_raw_values' => $line,
        ];

        foreach ($headers as $i => $header) {
            if ($header === '') {
                continue;
            }

            $assoc[$header] = $line[$i] ?? '';
        }

        return $assoc;
    }

    private function readRows(string $sheet): array
    {
        $range = $this->quoteSheet($sheet) . '!A1:ZZ';
        $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues() ?? [];

        if (count($values) < 2) {
            return [];
        }

        $headers = array_map(fn ($value) => trim((string) $value), $values[0]);
        $rows = [];

        foreach (array_slice($values, 1) as $index => $line) {
            $assoc = [
                '_row_number' => $index + 2,
                '_headers' => $headers,
                '_raw_values' => $line,
            ];

            foreach ($headers as $i => $header) {
                if ($header === '') {
                    continue;
                }

                $assoc[$header] = $line[$i] ?? '';
            }

            $rows[] = $assoc;
        }

        return $rows;
    }

    private function isBlankBusinessRow(array $row, array $definition): bool
    {
        foreach (($definition['mapping'] ?? []) as $header => $attribute) {
            if (str_starts_with((string) $attribute, 'wedding_event_')) {
                continue;
            }

            if (trim((string) ($row[$header] ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    private function importRow(string $module, array $definition, array $row, bool $dryRun): string
    {
        $modelClass = $definition['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            throw new RuntimeException("Model untuk module {$module} tidak valid.");
        }

        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();

        $record = $this->findRecord($modelClass, $row);
        $payload = $this->payloadFromRow($module, $definition, $row, $record);

        if ($record && $this->shouldDeleteFromSheet($definition, $row)) {
            return $dryRun
                ? 'deleted'
                : $this->deleteFromSheet($module, $definition, $row, $record);
        }

        if (!$record && !$this->shouldCreateFromSheet($definition, $row)) {
            return 'skipped';
        }

        if (!$record && !$this->hasRequiredFieldsForCreate($definition, $row)) {
            return 'skipped';
        }

        if ($record) {
            $businessPayload = $this->businessPayloadOnly($payload);
            $changes = $this->changedPayload($record, $businessPayload);

            if (empty($changes)) {
                return 'unchanged';
            }

            if ($dryRun) {
                return 'updated';
            }

            $oldPayload = $this->recordPayload($record, array_keys($businessPayload));

            $record->fill($payload);
            $record->save();

            $this->writeChangeLog($module, $record, 'sheet', 'sheet_to_web', 'update', $oldPayload, $this->recordPayload($record, array_keys($businessPayload)));
            $this->appendSyncLog($module, $record, $row, 'update', 'success', 'Updated from spreadsheet.');
            $this->writeRowMeta($definition['sheet'], $row, $record, $module, $definition);
            $this->refreshDropdownsIfNeeded($module);

            return 'updated';
        }

        if ($dryRun) {
            return 'created';
        }

        $record = new $modelClass();
        $record->fill($payload);

        if (Schema::hasColumn($table, 'sync_source')) {
            $record->setAttribute('sync_source', 'sheet');
        }

        if (Schema::hasColumn($table, 'sheet_key')) {
            $syncKey = trim((string) ($row['sync_key'] ?? ''));

            if ($syncKey === '') {
                $syncKey = $this->makeSyncKey($module, $payload);
            }

            $record->setAttribute('sheet_key', $syncKey);
        }

        if (Schema::hasColumn($table, 'last_synced_at')) {
            $record->setAttribute('last_synced_at', $this->now->format('Y-m-d H:i:s'));
        }

        $record->save();

        $this->writeChangeLog($module, $record, 'sheet', 'sheet_to_web', 'create', [], $this->recordPayload($record, array_keys($payload)));
        $this->appendSyncLog($module, $record, $row, 'create', 'success', 'Created from spreadsheet.');
        $this->writeRowMeta($definition['sheet'], $row, $record, $module, $definition);
        $this->refreshDropdownsIfNeeded($module);

        return 'created';
    }





    private function forceWriteSyncAction(string $sheetName, array $row, array $definition, string $value): void
    {
        $headers = array_values($definition['headers'] ?? []);
        $columnIndex = array_search('sync_action', $headers, true);

        if ($columnIndex === false) {
            return;
        }

        $rowNumber = $this->sheetRowNumberFromRow($row);

        if (! $rowNumber) {
            return;
        }

        try {
            $range = $this->v2QuoteSheet($sheetName) . '!' . $this->v2ColumnLetter($columnIndex + 1) . $rowNumber;

            $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                new \Google\Service\Sheets\ValueRange([
                    'values' => [
                        [$value],
                    ],
                ]),
                [
                    'valueInputOption' => 'RAW',
                ]
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function sheetRowNumberFromRow(array $row): ?int
    {
        foreach (['_row_number', 'row_number', 'rowNumber', '_sheet_row', 'sheet_row', '__row', '_row'] as $key) {
            if (! empty($row[$key])) {
                return (int) $row[$key];
            }
        }

        return null;
    }

    private function v2ColumnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $mod = ($columnNumber - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $columnNumber = intdiv($columnNumber - $mod, 26);
        }

        return $letter;
    }

    private function v2QuoteSheet(string $sheetName): string
    {
        return "'" . str_replace("'", "''", $sheetName) . "'";
    }


    private function clearSheetRowAfterDelete(string $sheetName, array $row, array $definition): void
    {
        $rowNumber = $this->sheetRowNumberFromRow($row);

        if (! $rowNumber) {
            return;
        }

        $headers = array_values($definition['headers'] ?? []);
        $lastColumn = max(count($headers), 1);

        try {
            $range = $this->v2QuoteSheet($sheetName) . '!A' . $rowNumber . ':' . $this->v2ColumnLetter($lastColumn) . $rowNumber;

            $this->sheets->spreadsheets_values->clear(
                $this->spreadsheetId,
                $range,
                new \Google\Service\Sheets\ClearValuesRequest()
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function shouldDeleteFromSheet(array $definition, array $row): bool
    {
        if (! config('wedding-sync-v2.safe_delete.enabled', true)) {
            return false;
        }

        $headers = $definition['headers'] ?? [];

        if (! in_array('sync_action', $headers, true)) {
            return false;
        }

        $action = $this->normalizeText($row['sync_action'] ?? '');

        if ($action === 'delete') {
            return true;
        }

        if (in_array($action, ['sync', 'processing', 'error'], true)) {
            return false;
        }

        return $this->isBusinessRowCleared($definition, $row);
    }

    private function isBusinessRowCleared(array $definition, array $row): bool
    {
        $headers = array_values($definition['headers'] ?? []);

        $systemHeaders = [
            'sync_action',
            'sync_status',
            'web_id',
            'sync_key',
            'last_modified_at',
            'last_modified_by',
            'last_modified_source',
            'last_synced_at',
            'row_hash',
        ];

        $hasIdentity = trim((string) ($row['web_id'] ?? '')) !== ''
            || trim((string) ($row['sync_key'] ?? '')) !== '';

        if (! $hasIdentity) {
            return false;
        }

        $businessHeaders = array_values(array_filter(
            $headers,
            fn ($header) => ! in_array($header, $systemHeaders, true)
        ));

        if (empty($businessHeaders)) {
            return false;
        }

        foreach ($businessHeaders as $header) {
            if (trim((string) ($row[$header] ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    private function deleteFromSheet(string $module, array $definition, array $row, Model $record): string
    {
        $oldPayload = $record->getAttributes();

        $this->insertDeleteChangeLog($module, $row, $record, $oldPayload);

        $record->delete();

        $this->clearSheetRowAfterDelete($definition['sheet'], $row, $definition);

        $this->appendSyncLog($module, $record, $row, 'delete', 'success', 'Deleted from spreadsheet.');

        $this->refreshDropdownsIfNeeded($module);

        return 'deleted';
    }

    private function insertDeleteChangeLog(string $module, array $row, Model $record, array $oldPayload): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('wedding_sync_v2_change_logs')) {
            return;
        }

        $table = 'wedding_sync_v2_change_logs';
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);

        $payload = [
            'module' => $module,
            'model_type' => get_class($record),
            'model_id' => $record->getKey(),
            'sync_key' => $row['sync_key'] ?? null,
            'action' => 'delete',
            'old_payload' => json_encode($oldPayload, JSON_UNESCAPED_UNICODE),
            'new_payload' => json_encode([
                'deleted_via' => 'sheet',
                'deleted_at' => now()->format('Y-m-d H:i:s'),
            ], JSON_UNESCAPED_UNICODE),
            'changed_fields' => json_encode(['deleted_at'], JSON_UNESCAPED_UNICODE),
            'rollback_status' => 'available',
            'changed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insert = [];

        foreach ($payload as $key => $value) {
            if (in_array($key, $columns, true)) {
                $insert[$key] = $value;
            }
        }

        if (! empty($insert)) {
            \Illuminate\Support\Facades\DB::table($table)->insert($insert);
        }
    }

    private function shouldCreateFromSheet(array $definition, array $row): bool
    {
        $headers = $definition['headers'] ?? [];

        if (! in_array('sync_action', $headers, true)) {
            return true;
        }

        $action = $this->normalizeText($row['sync_action'] ?? '');

        return $action === 'sync';
    }

    private function hasRequiredFieldsForCreate(array $definition, array $row): bool
    {
        $required = $definition['required_for_create'] ?? [];

        if (empty($required)) {
            return true;
        }

        foreach ($required as $header) {
            if (trim((string) ($row[$header] ?? '')) === '') {
                return false;
            }
        }

        return true;
    }


    private function refreshDropdownsIfNeeded(string $module): void
    {
        if (! config('wedding-sync-v2.auto_refresh_dropdowns.enabled', true)) {
            return;
        }

        $modulesWithFlexibleOptions = [
            'persiapan',
            'budget_cpp',
            'budget_cpw',
            'tamu_cpp',
            'tamu_cpw',
        ];

        if (! in_array($module, $modulesWithFlexibleOptions, true)) {
            return;
        }

        try {
            app(WeddingSyncV2SheetDropdowns::class)->applySilently();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function findRecord(string $modelClass, array $row): ?Model
    {
        $webId = trim((string) ($row['web_id'] ?? ''));

        if ($webId !== '' && ctype_digit($webId)) {
            $record = $modelClass::query()->find((int) $webId);

            if ($record) {
                return $record;
            }
        }

        $syncKey = trim((string) ($row['sync_key'] ?? ''));

        if ($syncKey === '') {
            return null;
        }

        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();

        if (Schema::hasColumn($table, 'sheet_key')) {
            return $modelClass::query()->where('sheet_key', $syncKey)->first();
        }

        return null;
    }

    private function payloadFromRow(string $module, array $definition, array $row, ?Model $record): array
    {
        $modelClass = $definition['model'];
        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();

        $payload = [];
        $mapping = $definition['mapping'] ?? [];
        $defaults = $definition['defaults'] ?? [];
        $forcedValues = $definition['forced_values'] ?? [];

        foreach ($mapping as $header => $attribute) {
            if (str_starts_with((string) $attribute, 'wedding_event_')) {
                continue;
            }

            if (!Schema::hasColumn($table, $attribute)) {
                continue;
            }

            $raw = $row[$header] ?? '';

            if (trim((string) $raw) === '' && array_key_exists($attribute, $defaults)) {
                $raw = $defaults[$attribute];
            }

            if (array_key_exists($attribute, $forcedValues)) {
                $raw = $forcedValues[$attribute];
            }

            $payload[$attribute] = $this->castValue($attribute, $raw);
        }

        foreach ($defaults as $attribute => $value) {
            if (!Schema::hasColumn($table, $attribute)) {
                continue;
            }

            if (!array_key_exists($attribute, $payload) || $payload[$attribute] === '' || $payload[$attribute] === null) {
                $payload[$attribute] = $this->castValue($attribute, $value);
            }
        }

        foreach ($forcedValues as $attribute => $value) {
            if (Schema::hasColumn($table, $attribute)) {
                $payload[$attribute] = $this->castValue($attribute, $value);
            }
        }

        if (!$record && Schema::hasColumn($table, 'wedding_event_id')) {
            $eventId = $this->resolveWeddingEventId($definition, $row);

            if ($eventId) {
                $payload['wedding_event_id'] = $eventId;
            }
        }

        if (Schema::hasColumn($table, 'last_synced_at')) {
            $payload['last_synced_at'] = $this->now->format('Y-m-d H:i:s');
        }

        if (
            in_array($module, ['budget_cpp', 'budget_cpw'], true)
            && Schema::hasColumn($table, 'category')
            && blank($payload['category'] ?? null)
        ) {
            $payload['category'] = 'Lainnya';
        }

        if (Schema::hasColumn($table, 'sync_source')) {
            $payload['sync_source'] = 'sheet';
        }

        return $payload;
    }


    private function businessPayloadOnly(array $payload): array
    {
        $ignoredAttributes = [
            'sync_source',
            'last_synced_at',
            'last_checked_at',
            'sheet_updated_at',
            'web_updated_at',
            'sheet_hash',
            'web_hash',
            'sheet_row',
        ];

        foreach ($ignoredAttributes as $attribute) {
            unset($payload[$attribute]);
        }

        return $payload;
    }

    private function changedPayload(Model $record, array $payload): array
    {
        $changes = [];

        foreach ($payload as $attribute => $newValue) {
            $oldValue = $record->getAttribute($attribute);

            if ($this->normalizeCompare($oldValue, $attribute) !== $this->normalizeCompare($newValue, $attribute)) {
                $changes[$attribute] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function recordPayload(Model $record, array $attributes): array
    {
        $payload = [];

        foreach ($attributes as $attribute) {
            $payload[$attribute] = $record->getAttribute($attribute);
        }

        return $payload;
    }

    private function resolveWeddingEventId(array $definition, array $row): ?int
    {
        if (!class_exists(WeddingEvent::class)) {
            return null;
        }

        $eventSide = strtoupper((string) ($definition['event_side'] ?? ''));

        if ($eventSide !== '') {
            $event = WeddingEvent::query()->get()->first(function ($event) use ($eventSide) {
                return $this->eventSideOf($event) === $eventSide;
            });

            return $event?->getKey();
        }

        $eventText = trim((string) ($row['acara'] ?? ''));

        if ($eventText !== '') {
            $target = $this->normalizeText($eventText);

            $event = WeddingEvent::query()->get()->first(function ($event) use ($target) {
                foreach (['name', 'event_name', 'title', 'slug', 'event_key', 'side', 'event_side', 'pihak'] as $field) {
                    $value = $event->getAttribute($field);

                    if ($value !== null && $this->normalizeText($value) === $target) {
                        return true;
                    }
                }

                return false;
            });

            if ($event) {
                return $event->getKey();
            }
        }

        return WeddingEvent::query()->orderBy('id')->value('id');
    }

    private function eventSideOf(Model $event): ?string
    {
        foreach (['event_side', 'side', 'pihak', 'event_key', 'slug', 'name', 'event_name', 'title'] as $field) {
            $text = strtoupper((string) ($event->getAttribute($field) ?? ''));

            if ($text === '') {
                continue;
            }

            if (str_contains($text, 'CPW') || str_contains($text, 'WANITA') || str_contains($text, 'CEWE')) {
                return 'CPW';
            }

            if (str_contains($text, 'CPP') || str_contains($text, 'PRIA') || str_contains($text, 'COWO')) {
                return 'CPP';
            }
        }

        return null;
    }

    private function castValue(string $attribute, mixed $value): mixed
    {
        if ($value === '') {
            return null;
        }

        if ($attribute === 'status') {
            return $this->normalizeChecklistStatus($value);
        }

        if ($attribute === 'assigned_to') {
            return $this->normalizeAssignedTo($value);
        }

        if ($attribute === 'payment_status') {
            return $this->normalizePaymentStatus($value);
        }

        if ($this->isDateAttribute($attribute)) {
            $date = $this->normalizeDateValue($value);

            return $date === '' ? null : $date;
        }

        if (in_array($attribute, [
            'estimated_amount',
            'actual_amount',
            'envelope_amount',
        ], true)) {
            return $this->numberValue($value);
        }

        if (in_array($attribute, [
            'total_invited',
            'rsvp_count',
            'actual_attendance_count',
            'souvenir_count',
            'progress_percent',
        ], true)) {
            return (int) $this->numberValue($value);
        }

        return $value;
    }


    private function normalizeChecklistStatus(mixed $value): string
    {
        $text = $this->normalizeText($value);

        $map = [
            'belum' => 'todo',
            'todo' => 'todo',
            'to do' => 'todo',
            'proses' => 'in_progress',
            'progress' => 'in_progress',
            'in progress' => 'in_progress',
            'sedang proses' => 'in_progress',
            'selesai' => 'done',
            'done' => 'done',
            'complete' => 'done',
            'completed' => 'done',
            'ditunda' => 'postponed',
            'tunda' => 'postponed',
            'postponed' => 'postponed',
            'batal' => 'canceled',
            'cancel' => 'canceled',
            'canceled' => 'canceled',
            'cancelled' => 'canceled',
        ];

        return $map[$text] ?? trim((string) $value);
    }

    private function normalizeAssignedTo(mixed $value): string
    {
        $text = $this->normalizeText($value);

        $map = [
            'cpw' => 'cpw',
            'calon pengantin wanita' => 'cpw',
            'wanita' => 'cpw',
            'cpp' => 'cpp',
            'calon pengantin pria' => 'cpp',
            'pria' => 'cpp',
            'bersama' => 'both',
            'both' => 'both',
            'semua' => 'both',
            'all' => 'both',
        ];

        return $map[$text] ?? strtolower(trim((string) $value));
    }

    private function normalizePaymentStatus(mixed $value): string
    {
        $text = $this->normalizeText($value);

        $map = [
            'belum bayar' => 'unpaid',
            'belum' => 'unpaid',
            'unpaid' => 'unpaid',
            'sebagian' => 'partial',
            'cicil' => 'partial',
            'partial' => 'partial',
            'dp' => 'partial',
            'lunas' => 'paid',
            'paid' => 'paid',
            'selesai' => 'paid',
        ];

        return $map[$text] ?? trim((string) $value);
    }

    private function numberValue(mixed $value): float|int
    {
        if (is_numeric($value)) {
            return $value + 0;
        }

        $text = trim((string) $value);
        $text = str_replace(['Rp', 'rp', 'IDR', 'idr', ' '], '', $text);
        $text = str_replace('.', '', $text);
        $text = str_replace(',', '.', $text);

        if ($text === '' || !is_numeric($text)) {
            return 0;
        }

        return $text + 0;
    }

    private function normalizeCompare(mixed $value, ?string $attribute = null): string
    {
        if ($attribute && $this->isDateAttribute($attribute)) {
            return $this->normalizeDateValue($value);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_numeric($value)) {
            return (string) ($value + 0);
        }

        return trim((string) ($value ?? ''));
    }

    private function isDateAttribute(string $attribute): bool
    {
        return in_array($attribute, [
            'due_date',
            'deadline',
            'rsvp_confirmed_at',
            'invitation_sent_at',
            'checked_in_at',
        ], true);
    }

    private function normalizeDateValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $text = trim((string) $value);

        if ($text === '') {
            return '';
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $text, $match)) {
            return "{$match[1]}-{$match[2]}-{$match[3]}";
        }

        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $text, $match)) {
            return sprintf('%04d-%02d-%02d', (int) $match[3], (int) $match[2], (int) $match[1]);
        }

        try {
            return Carbon::parse($text)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $text;
        }
    }

    private function normalizeText(mixed $value): string
    {
        $text = strtolower(trim((string) ($value ?? '')));
        $text = str_replace(['_', '-'], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return $text ?: '';
    }

    private function makeSyncKey(string $module, array $payload): string
    {
        $name = $payload['title']
            ?? $payload['item_name']
            ?? $payload['name']
            ?? uniqid('row_', true);

        return $module . ':sheet:' . substr(hash('sha256', $name . '|' . microtime(true)), 0, 16);
    }

    private function businessPayloadFromRow(array $definition, array $row): array
    {
        $payload = [];

        foreach (($definition['mapping'] ?? []) as $header => $attribute) {
            if (str_starts_with((string) $attribute, 'wedding_event_')) {
                continue;
            }

            $payload[$header] = trim((string) ($row[$header] ?? ''));
        }

        ksort($payload);

        return $payload;
    }

    private function rowHash(array $definition, array $row): string
    {
        return hash('sha256', json_encode(
            $this->businessPayloadFromRow($definition, $row),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));
    }

    private function writeRowMeta(string $sheet, array $row, Model $record, string $module, array $definition, string $syncAction = 'SYNCED'): void
    {
        $headers = $row['_headers'] ?? [];
        $values = $row['_raw_values'] ?? [];
        $rowNumber = (int) ($row['_row_number'] ?? 0);

        if ($rowNumber <= 1 || empty($headers)) {
            return;
        }

        $syncKey = trim((string) ($row['sync_key'] ?? ''));

        if ($syncKey === '') {
            $syncKey = $this->makeSyncKey($module, $record->getAttributes());
        }

        $meta = [
            'sync_action' => $syncAction,
            'sync_status' => $syncAction === 'SYNCED' ? '✅ Selesai ' . $this->now->format('H:i:s') : $syncAction,
            'web_id' => $record->getKey(),
            'sync_key' => $syncKey,
            'last_modified_at' => $this->now->format('Y-m-d H:i:s'),
            'last_modified_by' => 'sheet',
            'last_modified_source' => 'sheet',
            'last_synced_at' => $this->now->format('Y-m-d H:i:s'),
            'row_hash' => $this->rowHash($definition, $row),
        ];

        foreach ($headers as $index => $header) {
            if (array_key_exists($header, $meta)) {
                $values[$index] = $meta[$header];
            }
        }

        $endColumn = $this->columnLetter(count($headers));
        $range = $this->quoteSheet($sheet) . "!A{$rowNumber}:{$endColumn}{$rowNumber}";

        $body = new ValueRange([
            'values' => [
                array_values($values),
            ],
        ]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    private function writeChangeLog(string $module, Model $record, string $source, string $direction, string $action, array $oldPayload, array $newPayload): void
    {
        if (!Schema::hasTable('wedding_sync_v2_change_logs')) {
            return;
        }

        DB::table('wedding_sync_v2_change_logs')->insert([
            'module' => $module,
            'model_type' => $record::class,
            'model_id' => $record->getKey(),
            'sync_key' => $record->getAttribute('sheet_key') ?: ($record->getAttribute('sync_key') ?: null),
            'source' => $source,
            'direction' => $direction,
            'action' => $action,
            'old_payload' => json_encode($oldPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'new_payload' => json_encode($newPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'changed_by' => 'sheet',
            'changed_at' => $this->now->format('Y-m-d H:i:s'),
            'rollback_status' => 'available',
            'created_at' => $this->now->format('Y-m-d H:i:s'),
            'updated_at' => $this->now->format('Y-m-d H:i:s'),
        ]);
    }

    private function appendSyncLog(string $module, Model $record, array $row, string $action, string $status, string $message): void
    {
        $definition = config('wedding-sync-v2.system_sheets.sync_log');

        if (!$definition) {
            return;
        }

        $sheet = $definition['sheet'] ?? 'SYNC_LOG';
        $headers = $definition['headers'] ?? [];

        $item = $record->getAttribute('title')
            ?? $record->getAttribute('item_name')
            ?? $record->getAttribute('name')
            ?? '';

        $payload = [
            'waktu' => $this->now->format('Y-m-d H:i:s'),
            'module' => $module,
            'source' => 'sheet',
            'direction' => 'sheet_to_web',
            'action' => $action,
            'web_id' => $record->getKey(),
            'sync_key' => $row['sync_key'] ?? '',
            'item' => $item,
            'field' => '',
            'old_value' => '',
            'new_value' => '',
            'status' => $status,
            'message' => $message,
        ];

        $values = [];

        foreach ($headers as $header) {
            $values[] = $payload[$header] ?? '';
        }

        $body = new ValueRange([
            'values' => [
                $values,
            ],
        ]);

        $this->sheets->spreadsheets_values->append(
            $this->spreadsheetId,
            $this->quoteSheet($sheet) . '!A1',
            $body,
            [
                'valueInputOption' => 'RAW',
                'insertDataOption' => 'INSERT_ROWS',
            ]
        );
    }

    private function quoteSheet(string $sheet): string
    {
        return "'" . str_replace("'", "''", $sheet) . "'";
    }

    private function columnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $letter = chr(65 + $remainder) . $letter;
            $columnNumber = intdiv($columnNumber - 1, 26);
        }

        return $letter;
    }
}
