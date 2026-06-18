<?php

namespace App\Services\WeddingSyncV2;

use Carbon\Carbon;
use DateTimeInterface;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WeddingSyncV2WebExporter
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private Carbon $now;

    public function export(Command $command, ?string $onlyModule = null, bool $dryRun = false): void
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

        $command->info($dryRun ? 'DRY RUN export web ke spreadsheet v2...' : 'Mulai export web ke spreadsheet v2...');
        $command->newLine();

        foreach ($modules as $module => $definition) {
            $sheet = $definition['sheet'] ?? null;
            $headers = $definition['headers'] ?? [];

            if (!$sheet || empty($headers)) {
                $command->warn("SKIP {$module}: sheet/header belum lengkap.");
                continue;
            }

            $records = $this->recordsForModule($module, $definition);
            $rows = $records
                ->map(fn (Model $record) => $this->recordToRow($module, $definition, $headers, $record))
                ->values()
                ->all();

            $command->line("{$module} → {$sheet}: {$records->count()} data");

            if ($dryRun) {
                continue;
            }

            $this->writeHeader($sheet, $headers);
            $this->clearBody($sheet, count($headers));

            if (!empty($rows)) {
                $this->writeRows($sheet, $headers, $rows);
            }

            $this->markRecordsSynced($records);

            $this->appendSyncLog([
                'waktu' => $this->now->format('Y-m-d H:i:s'),
                'module' => $module,
                'source' => 'web',
                'direction' => 'web_to_sheet',
                'action' => 'export',
                'web_id' => '',
                'sync_key' => '',
                'item' => '',
                'field' => '',
                'old_value' => '',
                'new_value' => '',
                'status' => 'success',
                'message' => "Exported {$records->count()} row(s) to {$sheet}.",
            ]);

            $command->info("OK: {$sheet} berhasil diisi.");
        }

        $command->newLine();
        $command->info($dryRun ? 'DRY RUN selesai.' : 'Export web ke spreadsheet v2 selesai.');
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

    private function recordsForModule(string $module, array $definition): Collection
    {
        $modelClass = $definition['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            throw new RuntimeException("Model untuk module {$module} tidak valid.");
        }

        /** @var Model $instance */
        $instance = new $modelClass();

        $query = $modelClass::query();

        if (method_exists($instance, 'weddingEvent')) {
            $query->with('weddingEvent');
        }

        $records = $query->get();

        $type = $definition['type'] ?? null;

        if ($type === 'checklist') {
            $filter = $definition['filter'] ?? [];

            if (!empty($filter['category_in'])) {
                $allowed = collect($filter['category_in'])
                    ->map(fn ($value) => $this->normalizeText($value))
                    ->all();

                $records = $records->filter(function (Model $record) use ($allowed) {
                    return in_array($this->normalizeText($record->getAttribute('category')), $allowed, true);
                });
            }

            if (!empty($filter['category_not_in'])) {
                $blocked = collect($filter['category_not_in'])
                    ->map(fn ($value) => $this->normalizeText($value))
                    ->all();

                $records = $records->filter(function (Model $record) use ($blocked) {
                    return !in_array($this->normalizeText($record->getAttribute('category')), $blocked, true);
                });
            }

            return $records->values();
        }

        if (!empty($definition['event_side'])) {
            $targetSide = strtoupper((string) $definition['event_side']);

            $records = $records->filter(function (Model $record) use ($targetSide) {
                return $this->eventSideOf($record) === $targetSide;
            });
        }

        return $records->values();
    }

    private function recordToRow(string $module, array $definition, array $headers, Model $record): array
    {
        $mapping = $definition['mapping'] ?? [];
        $forcedValues = $definition['forced_values'] ?? [];
        $businessPayload = [];
        $row = [];

        foreach ($headers as $header) {
            if ($header === 'sync_action') {
                $row[] = 'SYNCED';
                continue;
            }

            if ($header === 'sync_status') {
                $row[] = '✅ SYNCED';
                continue;
            }

            if ($header === 'web_id') {
                $row[] = $record->getKey();
                continue;
            }

            if ($header === 'sync_key') {
                $row[] = $this->syncKey($module, $record);
                continue;
            }

            if ($header === 'last_modified_at') {
                $row[] = $this->formatValue($record->getAttribute('updated_at') ?: $this->now);
                continue;
            }

            if ($header === 'last_modified_by') {
                $row[] = 'web';
                continue;
            }

            if ($header === 'last_modified_source') {
                $row[] = 'web';
                continue;
            }

            if ($header === 'last_synced_at') {
                $row[] = $this->now->format('Y-m-d H:i:s');
                continue;
            }

            if ($header === 'row_hash') {
                $row[] = $this->rowHash($businessPayload);
                continue;
            }

            $attribute = $mapping[$header] ?? null;
            $value = '';

            if ($attribute) {
                if (array_key_exists($attribute, $forcedValues)) {
                    $value = $forcedValues[$attribute];
                } else {
                    $value = $this->attributeValue($record, $attribute);
                }
            }

            $sheetValue = $this->sheetDisplayValue($attribute, $value);

            $businessPayload[$header] = $this->normalizeHashValue($sheetValue);
            $row[] = $this->formatValue($sheetValue);
        }

        return $row;
    }


    private function sheetDisplayValue(?string $attribute, mixed $value): mixed
    {
        if ($attribute === null) {
            return $value;
        }

        $text = strtolower(trim((string) ($value ?? '')));

        if ($attribute === 'assigned_to') {
            return match ($text) {
                'cpw' => 'CPW',
                'cpp' => 'CPP',
                'both', 'bersama' => 'Bersama',
                default => $value,
            };
        }

        if ($attribute === 'status') {
            return match ($text) {
                'todo', 'belum' => 'Belum',
                'in_progress', 'progress', 'proses' => 'Proses',
                'done', 'selesai' => 'Selesai',
                'postponed', 'ditunda' => 'Ditunda',
                'canceled', 'cancelled', 'batal' => 'Batal',
                default => $value,
            };
        }

        if ($attribute === 'payment_status') {
            return match ($text) {
                'unpaid', 'belum bayar', 'belum' => 'Belum Bayar',
                'partial', 'sebagian', 'dp' => 'Sebagian',
                'paid', 'lunas' => 'Lunas',
                default => $value,
            };
        }

        return $value;
    }

    private function attributeValue(Model $record, string $attribute): mixed
    {
        if ($attribute === 'wedding_event_display') {
            return $this->eventDisplayOf($record);
        }

        if ($record->offsetExists($attribute) || array_key_exists($attribute, $record->getAttributes())) {
            return $record->getAttribute($attribute);
        }

        $fallbacks = [
            'note' => ['sync_note', 'notes', 'catatan'],
            'sync_note' => ['note', 'notes', 'catatan'],
            'dependency' => ['dependensi', 'depends_on'],
            'progress_percent' => ['progress', 'progress_percentage'],
        ];

        foreach ($fallbacks[$attribute] ?? [] as $fallback) {
            if ($record->offsetExists($fallback) || array_key_exists($fallback, $record->getAttributes())) {
                return $record->getAttribute($fallback);
            }
        }

        return null;
    }


    private function eventDisplayOf(Model $record): string
    {
        $event = $record->getRelationValue('weddingEvent');

        if (!$event) {
            return '';
        }

        foreach (['name', 'event_name', 'title', 'slug', 'event_key', 'side', 'event_side'] as $field) {
            $value = $event->getAttribute($field);

            if ($value !== null && trim((string) $value) !== '') {
                return (string) $value;
            }
        }

        return (string) $event->getKey();
    }

    private function eventSideOf(Model $record): ?string
    {
        $event = $record->getRelationValue('weddingEvent');

        $candidates = [
            $record->getAttribute('event_side'),
            $record->getAttribute('side'),
            $event?->getAttribute('event_side'),
            $event?->getAttribute('side'),
            $event?->getAttribute('pihak'),
            $event?->getAttribute('event_key'),
            $event?->getAttribute('slug'),
            $event?->getAttribute('name'),
            $event?->getAttribute('event_name'),
            $event?->getAttribute('title'),
        ];

        foreach ($candidates as $candidate) {
            $text = strtoupper((string) ($candidate ?? ''));

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

    private function syncKey(string $module, Model $record): string
    {
        $existing = $record->getAttribute('sheet_key')
            ?: $record->getAttribute('sync_key')
            ?: $record->getAttribute('record_key');

        if ($existing) {
            return (string) $existing;
        }

        return $module . ':web:' . $record->getKey();
    }

    private function rowHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function normalizeHashValue(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $this->formatValue($value);
        }

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    private function normalizeText(mixed $value): string
    {
        $text = strtolower(trim((string) ($value ?? '')));
        $text = str_replace(['_', '-'], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return $text ?: '';
    }

    private function formatValue(mixed $value): string|int|float|null
    {
        if ($value instanceof DateTimeInterface) {
            $time = $value->format('H:i:s');

            if ($time === '00:00:00') {
                return $value->format('Y-m-d');
            }

            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return $value;
        }

        return (string) $value;
    }

    private function writeHeader(string $sheet, array $headers): void
    {
        $endColumn = $this->columnLetter(count($headers));
        $range = $this->quoteSheet($sheet) . "!A1:{$endColumn}1";

        $body = new ValueRange([
            'values' => [
                array_values($headers),
            ],
        ]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    private function clearBody(string $sheet, int $columnCount): void
    {
        $endColumn = $this->columnLetter(max($columnCount, 26));
        $range = $this->quoteSheet($sheet) . "!A2:{$endColumn}";

        $this->sheets->spreadsheets_values->clear(
            $this->spreadsheetId,
            $range,
            new ClearValuesRequest()
        );
    }

    private function writeRows(string $sheet, array $headers, array $rows): void
    {
        $endColumn = $this->columnLetter(count($headers));
        $endRow = count($rows) + 1;
        $range = $this->quoteSheet($sheet) . "!A2:{$endColumn}{$endRow}";

        $body = new ValueRange([
            'values' => $rows,
        ]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    private function appendSyncLog(array $payload): void
    {
        $definition = config('wedding-sync-v2.system_sheets.sync_log');

        if (!$definition) {
            return;
        }

        $sheet = $definition['sheet'] ?? 'SYNC_LOG';
        $headers = $definition['headers'] ?? array_keys($payload);

        $row = [];

        foreach ($headers as $header) {
            $row[] = $payload[$header] ?? '';
        }

        $body = new ValueRange([
            'values' => [
                $row,
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

    private function markRecordsSynced(Collection $records): void
    {
        if ($records->isEmpty()) {
            return;
        }

        /** @var Model $first */
        $first = $records->first();
        $table = $first->getTable();

        if (!Schema::hasColumn($table, 'last_synced_at')) {
            return;
        }

        $ids = $records
            ->map(fn (Model $record) => $record->getKey())
            ->filter()
            ->values()
            ->all();

        if (empty($ids)) {
            return;
        }

        DB::table($table)
            ->whereIn($first->getKeyName(), $ids)
            ->update([
                'last_synced_at' => $this->now->format('Y-m-d H:i:s'),
            ]);
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
