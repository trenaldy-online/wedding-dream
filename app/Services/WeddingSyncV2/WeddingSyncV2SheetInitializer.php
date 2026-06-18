<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\GridProperties;
use Google\Service\Sheets\RepeatCellRequest;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\SheetProperties;
use Google\Service\Sheets\UpdateSheetPropertiesRequest;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2SheetInitializer
{
    private Sheets $sheets;
    private string $spreadsheetId;

    public function init(Command $command): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $this->sheets = new Sheets($this->makeGoogleClient());

        $targets = $this->targetSheets();

        if (empty($targets)) {
            throw new RuntimeException('Tidak ada target sheet di config wedding-sync-v2.');
        }

        $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId);
        $existing = [];

        foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
            $properties = $sheet->getProperties();

            if (!$properties) {
                continue;
            }

            $existing[$properties->getTitle()] = $properties->getSheetId();
        }

        $created = [];

        foreach ($targets as $title => $headers) {
            if (!array_key_exists($title, $existing)) {
                $this->addSheet($title);
                $created[] = $title;
                $command->info("CREATED SHEET: {$title}");
            } else {
                $command->line("EXISTS SHEET: {$title}");
            }
        }

        if (!empty($created)) {
            $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId);
            $existing = [];

            foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
                $properties = $sheet->getProperties();

                if (!$properties) {
                    continue;
                }

                $existing[$properties->getTitle()] = $properties->getSheetId();
            }
        }

        foreach ($targets as $title => $headers) {
            $this->writeHeader($title, $headers);

            if (isset($existing[$title])) {
                $this->formatHeader((int) $existing[$title], count($headers));
            }

            $command->info("HEADER OK: {$title}");
        }

        $command->newLine();
        $command->info('Wedding Sync V2 spreadsheet berhasil disiapkan.');
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
            $headers = $definition['headers'] ?? [];

            if (!$sheet || empty($headers)) {
                continue;
            }

            $targets[$sheet] = $headers;
        }

        foreach ((array) config('wedding-sync-v2.system_sheets', []) as $module => $definition) {
            $sheet = $definition['sheet'] ?? null;
            $headers = $definition['headers'] ?? [];

            if (!$sheet || empty($headers)) {
                continue;
            }

            $targets[$sheet] = $headers;
        }

        return $targets;
    }

    private function addSheet(string $title): void
    {
        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                new Request([
                    'addSheet' => [
                        'properties' => [
                            'title' => $title,
                            'gridProperties' => [
                                'rowCount' => 1000,
                                'columnCount' => 30,
                                'frozenRowCount' => 1,
                            ],
                        ],
                    ],
                ]),
            ],
        ]);

        $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $body);
    }

    private function writeHeader(string $title, array $headers): void
    {
        $endColumn = $this->columnLetter(count($headers));
        $range = "'" . str_replace("'", "''", $title) . "'!A1:{$endColumn}1";

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

    private function formatHeader(int $sheetId, int $headerCount): void
    {
        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                new Request([
                    'updateSheetProperties' => new UpdateSheetPropertiesRequest([
                        'properties' => new SheetProperties([
                            'sheetId' => $sheetId,
                            'gridProperties' => new GridProperties([
                                'frozenRowCount' => 1,
                            ]),
                        ]),
                        'fields' => 'gridProperties.frozenRowCount',
                    ]),
                ]),
                new Request([
                    'repeatCell' => new RepeatCellRequest([
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 0,
                            'endRowIndex' => 1,
                            'startColumnIndex' => 0,
                            'endColumnIndex' => $headerCount,
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'textFormat' => [
                                    'bold' => true,
                                ],
                            ],
                        ],
                        'fields' => 'userEnteredFormat.textFormat.bold',
                    ]),
                ]),
            ],
        ]);

        $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $body);
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
