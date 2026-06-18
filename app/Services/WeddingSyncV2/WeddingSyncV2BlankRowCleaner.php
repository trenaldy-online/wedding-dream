<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ClearValuesRequest;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2BlankRowCleaner
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private array $systemHeaders = [
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

    public function apply(Command $command, ?string $onlyModule = null): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $this->sheets = new Sheets($this->makeGoogleClient());

        $modules = (array) config('wedding-sync-v2.modules', []);

        if ($onlyModule) {
            if (! isset($modules[$onlyModule])) {
                throw new RuntimeException("Module {$onlyModule} tidak ditemukan di config wedding-sync-v2.");
            }

            $modules = [
                $onlyModule => $modules[$onlyModule],
            ];
        }

        $totalCleared = 0;

        foreach ($modules as $module => $definition) {
            $sheetName = $definition['sheet'] ?? null;
            $headers = array_values($definition['headers'] ?? []);

            if (! $sheetName || empty($headers)) {
                $command->warn("SKIP {$module}: sheet/header kosong.");
                continue;
            }

            $cleared = $this->cleanSheet($sheetName, $headers);
            $totalCleared += $cleared;

            $command->info("OK {$module} / {$sheetName}: {$cleared} blank leftover row dibersihkan.");
        }

        $command->newLine();
        $command->info("Selesai. Total row dibersihkan: {$totalCleared}");
    }

    private function cleanSheet(string $sheetName, array $headers): int
    {
        $lastColumn = $this->columnLetter(count($headers));
        $range = $this->quoteSheet($sheetName) . "!A1:{$lastColumn}1000";

        $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
        $rows = $response->getValues() ?? [];

        if (count($rows) <= 1) {
            return 0;
        }

        $businessIndexes = [];
        $systemIndexes = [];

        foreach ($headers as $index => $header) {
            if (in_array($header, $this->systemHeaders, true)) {
                $systemIndexes[] = $index;
            } else {
                $businessIndexes[] = $index;
            }
        }

        $cleared = 0;

        foreach ($rows as $zeroBasedRowIndex => $row) {
            $sheetRowNumber = $zeroBasedRowIndex + 1;

            if ($sheetRowNumber === 1) {
                continue;
            }

            $businessEmpty = true;

            foreach ($businessIndexes as $index) {
                if (trim((string) ($row[$index] ?? '')) !== '') {
                    $businessEmpty = false;
                    break;
                }
            }

            if (! $businessEmpty) {
                continue;
            }

            $hasLeftover = false;

            foreach ($systemIndexes as $index) {
                if (trim((string) ($row[$index] ?? '')) !== '') {
                    $hasLeftover = true;
                    break;
                }
            }

            if (! $hasLeftover) {
                continue;
            }

            $clearRange = $this->quoteSheet($sheetName) . "!A{$sheetRowNumber}:{$lastColumn}{$sheetRowNumber}";

            $this->sheets->spreadsheets_values->clear(
                $this->spreadsheetId,
                $clearRange,
                new ClearValuesRequest()
            );

            $cleared++;
        }

        return $cleared;
    }

    private function makeGoogleClient(): Client
    {
        $jsonPath = config('google-sheets.service_account_json');

        if (! $jsonPath) {
            $envPath = env('GOOGLE_SERVICE_ACCOUNT_JSON', 'storage/app/google/service-account.json');

            $jsonPath = str_starts_with($envPath, 'storage/')
                ? storage_path(substr($envPath, strlen('storage/')))
                : base_path($envPath);
        }

        if (! is_file($jsonPath)) {
            throw new RuntimeException("Service account JSON tidak ditemukan: {$jsonPath}");
        }

        $client = new Client();
        $client->setApplicationName('Wedding Dream Sync V2 Blank Row Cleaner');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }

    private function columnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $mod = ($columnNumber - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $columnNumber = intdiv($columnNumber - $mod, 26);
        }

        return $letter;
    }

    private function quoteSheet(string $sheetName): string
    {
        return "'" . str_replace("'", "''", $sheetName) . "'";
    }
}
