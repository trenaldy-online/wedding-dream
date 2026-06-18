<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2SheetColumnVisibility
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private array $hiddenSystemColumns = [
        'web_id',
        'sync_key',
        'last_modified_at',
        'last_modified_by',
        'last_modified_source',
        'last_synced_at',
        'row_hash',
    ];

    private array $visibleSystemColumns = [
        'sync_action',
        'sync_status',
    ];

    public function apply(Command $command): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $this->sheets = new Sheets($this->makeGoogleClient());

        $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId);
        $sheetIds = [];

        foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
            $properties = $sheet->getProperties();

            if ($properties) {
                $sheetIds[$properties->getTitle()] = $properties->getSheetId();
            }
        }

        $requests = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $sheetName = $definition['sheet'] ?? null;
            $headers = array_values($definition['headers'] ?? []);

            if (!$sheetName || !isset($sheetIds[$sheetName])) {
                $command->warn("SKIP: {$module}, sheet tidak ditemukan.");
                continue;
            }

            $sheetId = (int) $sheetIds[$sheetName];

            foreach ($headers as $index => $header) {
                if (in_array($header, $this->hiddenSystemColumns, true)) {
                    $requests[] = $this->hiddenRequest($sheetId, $index, true);
                    continue;
                }

                if (in_array($header, $this->visibleSystemColumns, true)) {
                    $requests[] = $this->hiddenRequest($sheetId, $index, false);
                    continue;
                }
            }

            $command->info("OK: visibility column {$sheetName} disiapkan.");
        }

        if (empty($requests)) {
            $command->warn('Tidak ada perubahan visibility column.');
            return;
        }

        $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ])
        );

        $command->info('Kolom sistem berhasil disembunyikan dan kolom status/action ditampilkan.');
    }

    private function hiddenRequest(int $sheetId, int $columnIndex, bool $hidden): array
    {
        return [
            'updateDimensionProperties' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'COLUMNS',
                    'startIndex' => $columnIndex,
                    'endIndex' => $columnIndex + 1,
                ],
                'properties' => [
                    'hiddenByUser' => $hidden,
                ],
                'fields' => 'hiddenByUser',
            ],
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
        $client->setApplicationName('Wedding Dream Sync V2 Column Visibility');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }
}
