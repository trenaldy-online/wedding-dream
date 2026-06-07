<?php

namespace App\Services\WeddingSync;

use Google\Client;
use Google\Service\Sheets;
use RuntimeException;

class GoogleSheetsClient
{
    private Sheets $service;
    private string $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = (string) config('google-sheets.spreadsheet_id');

        if (!$this->spreadsheetId) {
            throw new RuntimeException('GOOGLE_SHEET_ID belum diatur di file .env.');
        }

        $credentialPath = (string) config('google-sheets.service_account_json');

        if (!$credentialPath || !file_exists($credentialPath)) {
            throw new RuntimeException("File credential Google tidak ditemukan di: {$credentialPath}");
        }

        $client = new Client();
        $client->setApplicationName('Wedding Google Sheet Sync');
        $client->setAuthConfig($credentialPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        $this->service = new Sheets($client);
    }

    public function getValues(string $sheetName, string $range = 'A1:Z1000'): array
    {
        $safeRange = $this->buildRange($sheetName, $range);

        $response = $this->service
            ->spreadsheets_values
            ->get($this->spreadsheetId, $safeRange);

        return $response->getValues() ?? [];
    }

    public function getRowsWithHeader(string $sheetName, string $range = 'A1:Z1000'): array
    {
        $values = $this->getValues($sheetName, $range);

        if (count($values) < 1) {
            return [];
        }

        $headers = array_map(
            fn ($header) => $this->normalizeHeader((string) $header),
            $values[0]
        );

        $rows = [];

        foreach (array_slice($values, 1) as $index => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $assoc = [];

            foreach ($headers as $columnIndex => $header) {
                if ($header === '') {
                    continue;
                }

                $assoc[$header] = $row[$columnIndex] ?? null;
            }

            $assoc['_sheet_name'] = $sheetName;
            $assoc['_sheet_row'] = $index + 2;

            $rows[] = $assoc;
        }

        return $rows;
    }

    public function appendRow(string $sheetName, array $row): void
    {
        $safeRange = $this->buildRange($sheetName, 'A:Z');

        $body = new Sheets\ValueRange([
            'values' => [$this->normalizeRowForSheets($row)],
        ]);

        $this->service
            ->spreadsheets_values
            ->append(
                $this->spreadsheetId,
                $safeRange,
                $body,
                [
                    'valueInputOption' => 'USER_ENTERED',
                    'insertDataOption' => 'INSERT_ROWS',
                ]
            );
    }

    public function sheetExists(string $sheetName): bool
    {
        $spreadsheet = $this->service
            ->spreadsheets
            ->get($this->spreadsheetId);

        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === $sheetName) {
                return true;
            }
        }

        return false;
    }

    public function createSheet(string $sheetName): void
    {
        if ($this->sheetExists($sheetName)) {
            return;
        }

        $request = new Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => [
                [
                    'addSheet' => [
                        'properties' => [
                            'title' => $sheetName,
                        ],
                    ],
                ],
            ],
        ]);

        $this->service
            ->spreadsheets
            ->batchUpdate($this->spreadsheetId, $request);
    }

    public function updateRange(string $sheetName, string $range, array $values): void
    {
        $safeRange = $this->buildRange($sheetName, $range);

        $body = new Sheets\ValueRange([
            'values' => $this->normalizeValuesForSheets($values),
        ]);

        $this->service
            ->spreadsheets_values
            ->update(
                $this->spreadsheetId,
                $safeRange,
                $body,
                [
                    'valueInputOption' => 'USER_ENTERED',
                ]
            );
    }

    public function ensureSheetWithHeaders(string $sheetName, array $headers): void
    {
        $this->createSheet($sheetName);

        $endColumn = $this->columnLetter(count($headers));

        $this->updateRange($sheetName, "A1:{$endColumn}1", [$headers]);
    }

    public function rowExistsByKey(
        string $sheetName,
        string $keyHeader,
        string $keyValue,
        string $range = 'A1:Z5000'
    ): bool {
        if (!$this->sheetExists($sheetName)) {
            return false;
        }

        $rows = $this->getRowsWithHeader($sheetName, $range);

        foreach ($rows as $row) {
            if ((string) ($row[$keyHeader] ?? '') === (string) $keyValue) {
                return true;
            }
        }

        return false;
    }

    private function columnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $columnNumber = intdiv($columnNumber - $modulo, 26);
        }

        return $letter;
    }


    private function normalizeValuesForSheets(array $values): array
    {
        return array_map(function ($row) {
            if (!is_array($row)) {
                $row = [$row];
            }

            return $this->normalizeRowForSheets($row);
        }, array_values($values));
    }

    private function normalizeRowForSheets(array $row): array
    {
        /*
         * Google Sheets API membutuhkan array numerik yang rapat:
         * [0,1,2,3,...]
         *
         * Kalau ada null di tengah row, PHP/Google Client bisa mengirimnya
         * sebagai object {"0": "...", "1": "...", "5": "..."}.
         * Itu yang menyebabkan error:
         * Unknown name "0" at data.values[0].
         */
        $row = array_values($row);

        return array_map(function ($cell) {
            if ($cell === null) {
                return '';
            }

            if ($cell instanceof \DateTimeInterface) {
                return $cell->format('Y-m-d');
            }

            if (is_bool($cell)) {
                return $cell ? 'TRUE' : 'FALSE';
            }

            if (is_array($cell) || is_object($cell)) {
                return json_encode($cell, JSON_UNESCAPED_UNICODE);
            }

            return (string) $cell;
        }, $row);
    }

    private function buildRange(string $sheetName, string $range): string
    {
        $escapedSheetName = str_replace("'", "\\'", $sheetName);

        return "'{$escapedSheetName}'!{$range}";
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim($header);
        $header = strtolower($header);
        $header = preg_replace('/[^a-z0-9]+/i', '_', $header);
        $header = trim((string) $header, '_');

        return $header;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }
}
