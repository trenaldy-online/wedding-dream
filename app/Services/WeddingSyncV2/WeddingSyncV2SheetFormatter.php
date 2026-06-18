<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2SheetFormatter
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private array $systemColumns = [
        'web_id',
        'sync_key',
        'last_modified_at',
        'last_modified_by',
        'last_modified_source',
        'last_synced_at',
        'row_hash',
    ];

    public function format(Command $command): void
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

            if (!$properties) {
                continue;
            }

            $sheetIds[$properties->getTitle()] = $properties->getSheetId();
        }

        $targets = $this->targetSheets();

        foreach ($targets as $sheetName => $definition) {
            if (!isset($sheetIds[$sheetName])) {
                $command->warn("SKIP: {$sheetName} tidak ditemukan di spreadsheet.");
                continue;
            }

            $sheetId = (int) $sheetIds[$sheetName];
            $headers = array_values($definition['headers'] ?? []);

            if (empty($headers)) {
                $command->warn("SKIP: {$sheetName} tidak punya headers.");
                continue;
            }

            $isSystemSheet = (bool) ($definition['system_sheet'] ?? false);

            $this->formatOneSheet($sheetId, $sheetName, $headers, $isSystemSheet);

            $command->info("OK: {$sheetName} diformat.");
        }

        $command->newLine();
        $command->info('Format spreadsheet v2 selesai.');
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

    private function targetSheets(): array
    {
        $targets = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $sheet = $definition['sheet'] ?? null;

            if (!$sheet) {
                continue;
            }

            $targets[$sheet] = [
                'headers' => $definition['headers'] ?? [],
                'module' => $module,
                'system_sheet' => false,
            ];
        }

        foreach ((array) config('wedding-sync-v2.system_sheets', []) as $module => $definition) {
            $sheet = $definition['sheet'] ?? null;

            if (!$sheet) {
                continue;
            }

            $targets[$sheet] = [
                'headers' => $definition['headers'] ?? [],
                'module' => $module,
                'system_sheet' => true,
            ];
        }

        return $targets;
    }

    private function formatOneSheet(int $sheetId, string $sheetName, array $headers, bool $isSystemSheet): void
    {
        $headerCount = count($headers);
        $requests = [];

        // Freeze header.
        $requests[] = [
            'updateSheetProperties' => [
                'properties' => [
                    'sheetId' => $sheetId,
                    'gridProperties' => [
                        'frozenRowCount' => 1,
                    ],
                ],
                'fields' => 'gridProperties.frozenRowCount',
            ],
        ];

        // Header style.
        $headerColor = $isSystemSheet
            ? $this->rgb(0.23, 0.32, 0.45)
            : $this->rgb(0.13, 0.32, 0.29);

        $requests[] = [
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 0,
                    'endRowIndex' => 1,
                    'startColumnIndex' => 0,
                    'endColumnIndex' => $headerCount,
                ],
                'cell' => [
                    'userEnteredFormat' => [
                        'backgroundColor' => $headerColor,
                        'horizontalAlignment' => 'CENTER',
                        'verticalAlignment' => 'MIDDLE',
                        'wrapStrategy' => 'WRAP',
                        'textFormat' => [
                            'bold' => true,
                            'foregroundColor' => $this->rgb(1, 1, 1),
                            'fontSize' => 10,
                        ],
                    ],
                ],
                'fields' => 'userEnteredFormat(backgroundColor,horizontalAlignment,verticalAlignment,wrapStrategy,textFormat)',
            ],
        ];

        // Body style.
        $requests[] = [
            'repeatCell' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 1,
                    'endRowIndex' => 1000,
                    'startColumnIndex' => 0,
                    'endColumnIndex' => $headerCount,
                ],
                'cell' => [
                    'userEnteredFormat' => [
                        'verticalAlignment' => 'MIDDLE',
                        'wrapStrategy' => 'WRAP',
                        'textFormat' => [
                            'fontSize' => 10,
                        ],
                    ],
                ],
                'fields' => 'userEnteredFormat(verticalAlignment,wrapStrategy,textFormat.fontSize)',
            ],
        ];

        // Basic filter.
        $requests[] = [
            'setBasicFilter' => [
                'filter' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1000,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => $headerCount,
                    ],
                ],
            ],
        ];

        // Header row height.
        $requests[] = [
            'updateDimensionProperties' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'ROWS',
                    'startIndex' => 0,
                    'endIndex' => 1,
                ],
                'properties' => [
                    'pixelSize' => 34,
                ],
                'fields' => 'pixelSize',
            ],
        ];

        // Body row height.
        $requests[] = [
            'updateDimensionProperties' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'dimension' => 'ROWS',
                    'startIndex' => 1,
                    'endIndex' => 1000,
                ],
                'properties' => [
                    'pixelSize' => 28,
                ],
                'fields' => 'pixelSize',
            ],
        ];

        // Column formatting.
        foreach ($headers as $index => $header) {
            $width = $this->widthForHeader($header);
            $hidden = (!$isSystemSheet && in_array($header, $this->systemColumns, true));

            $requests[] = [
                'updateDimensionProperties' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => $index,
                        'endIndex' => $index + 1,
                    ],
                    'properties' => [
                        'pixelSize' => $width,
                        'hiddenByUser' => $hidden,
                    ],
                    'fields' => 'pixelSize,hiddenByUser',
                ],
            ];

            $numberFormat = $this->numberFormatForHeader($header);

            if ($numberFormat) {
                $requests[] = [
                    'repeatCell' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 1,
                            'endRowIndex' => 1000,
                            'startColumnIndex' => $index,
                            'endColumnIndex' => $index + 1,
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'numberFormat' => $numberFormat,
                            ],
                        ],
                        'fields' => 'userEnteredFormat.numberFormat',
                    ],
                ];
            }

            if (!$isSystemSheet && in_array($header, $this->systemColumns, true)) {
                $requests[] = [
                    'repeatCell' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 0,
                            'endRowIndex' => 1000,
                            'startColumnIndex' => $index,
                            'endColumnIndex' => $index + 1,
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'backgroundColor' => $this->rgb(0.90, 0.90, 0.90),
                            ],
                        ],
                        'fields' => 'userEnteredFormat.backgroundColor',
                    ],
                ];
            }
        }

        // Alternating row colors tidak ditambahkan ulang di sini.
        // Jika banding sudah ada, Google Sheets akan error ketika addBanding dipanggil lagi.
        // Format lain tetap idempotent dan aman dijalankan berulang.

        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => $requests,
        ]);

        $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $body);
    }

    private function widthForHeader(string $header): int
    {
        return match ($header) {
            'nama', 'item', 'tugas', 'dokumen' => 190,
            'alamat', 'catatan', 'catatan_sync', 'message' => 280,
            'rsvp_note', 'sync_note' => 260,
            'no_wa', 'phone' => 140,
            'kategori', 'acara', 'grup', 'pic', 'prioritas', 'status', 'status_bayar' => 150,
            'deadline', 'waktu', 'detected_at', 'changed_at' => 145,
            'estimasi', 'aktual', 'envelope_amount' => 130,
            'jumlah_undangan', 'rsvp_count', 'actual_attendance_count', 'souvenir_count', 'progress_percent' => 120,
            'web_id' => 70,
            'sync_key' => 170,
            'row_hash' => 220,
            'last_modified_at', 'last_synced_at' => 155,
            'last_modified_by', 'last_modified_source' => 135,
            default => 130,
        };
    }

    private function numberFormatForHeader(string $header): ?array
    {
        if (in_array($header, ['no_wa', 'phone', 'sync_key', 'row_hash'], true)) {
            return [
                'type' => 'TEXT',
                'pattern' => '@',
            ];
        }

        if (in_array($header, ['deadline'], true)) {
            return [
                'type' => 'DATE',
                'pattern' => 'yyyy-mm-dd',
            ];
        }

        if (in_array($header, ['waktu', 'detected_at', 'last_modified_at', 'last_synced_at'], true)) {
            return [
                'type' => 'DATE_TIME',
                'pattern' => 'yyyy-mm-dd hh:mm',
            ];
        }

        if (in_array($header, ['estimasi', 'aktual', 'envelope_amount', 'old_value', 'new_value'], true)) {
            return [
                'type' => 'NUMBER',
                'pattern' => '#,##0',
            ];
        }

        if (in_array($header, ['jumlah_undangan', 'rsvp_count', 'actual_attendance_count', 'souvenir_count', 'progress_percent'], true)) {
            return [
                'type' => 'NUMBER',
                'pattern' => '0',
            ];
        }

        return null;
    }

    private function rgb(float $red, float $green, float $blue): array
    {
        return [
            'red' => $red,
            'green' => $green,
            'blue' => $blue,
        ];
    }
}
