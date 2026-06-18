<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2SheetProtector
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private string $descriptionPrefix = 'Wedding Sync V2 System Column';

    private array $systemColumns = [
        'web_id',
        'sync_key',
        'last_modified_at',
        'last_modified_by',
        'last_modified_source',
        'last_synced_at',
        'row_hash',
    ];

    public function protect(Command $command, bool $hardLock = false): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $this->sheets = new Sheets($this->makeGoogleClient());

        $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId, [
            'fields' => 'sheets(properties(sheetId,title),protectedRanges(protectedRangeId,description))',
        ]);

        $sheetIds = [];
        $deleteProtectionRequests = [];

        foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
            $properties = $sheet->getProperties();

            if (!$properties) {
                continue;
            }

            $sheetIds[$properties->getTitle()] = $properties->getSheetId();

            foreach ($sheet->getProtectedRanges() ?? [] as $protectedRange) {
                $description = (string) $protectedRange->getDescription();

                if (str_starts_with($description, $this->descriptionPrefix)) {
                    $deleteProtectionRequests[] = [
                        'deleteProtectedRange' => [
                            'protectedRangeId' => $protectedRange->getProtectedRangeId(),
                        ],
                    ];
                }
            }
        }

        if (!empty($deleteProtectionRequests)) {
            $this->sheets->spreadsheets->batchUpdate(
                $this->spreadsheetId,
                new BatchUpdateSpreadsheetRequest([
                    'requests' => $deleteProtectionRequests,
                ])
            );

            $command->info('Proteksi lama Sync V2 dihapus agar tidak dobel.');
        }

        $requests = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $sheetName = $definition['sheet'] ?? null;
            $headers = array_values($definition['headers'] ?? []);

            if (!$sheetName || empty($headers)) {
                continue;
            }

            if (!isset($sheetIds[$sheetName])) {
                $command->warn("SKIP: {$sheetName} tidak ditemukan.");
                continue;
            }

            $sheetId = (int) $sheetIds[$sheetName];
            $protectedCount = 0;

            foreach ($headers as $index => $header) {
                if (!in_array($header, $this->systemColumns, true)) {
                    continue;
                }

                $requests[] = $this->addProtectedColumnRequest(
                    $sheetId,
                    $sheetName,
                    $header,
                    $index,
                    $hardLock
                );

                $protectedCount++;
            }

            $command->info("OK: {$sheetName} {$protectedCount} kolom sistem disiapkan proteksinya.");
        }

        if (empty($requests)) {
            $command->warn('Tidak ada kolom sistem yang diproteksi.');
            return;
        }

        $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ])
        );

        $command->newLine();

        if ($hardLock) {
            $command->info('Proteksi kolom sistem selesai dalam mode HARD LOCK.');
        } else {
            $command->info('Proteksi kolom sistem selesai dalam mode WARNING.');
        }
    }

    private function addProtectedColumnRequest(
        int $sheetId,
        string $sheetName,
        string $header,
        int $columnIndex,
        bool $hardLock
    ): array {
        $protectedRange = [
            'range' => [
                'sheetId' => $sheetId,
                'startRowIndex' => 0,
                'endRowIndex' => 1000,
                'startColumnIndex' => $columnIndex,
                'endColumnIndex' => $columnIndex + 1,
            ],
            'description' => "{$this->descriptionPrefix}: {$sheetName}.{$header}",
            'warningOnly' => !$hardLock,
        ];

        if ($hardLock) {
            $serviceAccountEmail = $this->serviceAccountEmail();

            if ($serviceAccountEmail !== '') {
                $protectedRange['editors'] = [
                    'users' => [
                        $serviceAccountEmail,
                    ],
                ];
            }
        }

        return [
            'addProtectedRange' => [
                'protectedRange' => $protectedRange,
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
        $client->setApplicationName('Wedding Dream Sync V2');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }

    private function serviceAccountEmail(): string
    {
        $jsonPath = config('google-sheets.service_account_json');

        if (!$jsonPath) {
            $envPath = env('GOOGLE_SERVICE_ACCOUNT_JSON', 'storage/app/google/service-account.json');
            $jsonPath = str_starts_with($envPath, 'storage/')
                ? storage_path(substr($envPath, strlen('storage/')))
                : base_path($envPath);
        }

        if (!is_file($jsonPath)) {
            return '';
        }

        $json = json_decode((string) file_get_contents($jsonPath), true);

        return (string) ($json['client_email'] ?? '');
    }
}
