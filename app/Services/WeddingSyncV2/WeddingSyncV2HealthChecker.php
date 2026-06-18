<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

class WeddingSyncV2HealthChecker
{
    private int $failures = 0;

    public function check(Command $command): int
    {
        $this->failures = 0;

        $command->info('Wedding Sync V2 Health Check');
        $command->line(str_repeat('-', 34));

        $spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');
        $webhookToken = (string) config('wedding-sync-v2.webhook.token');
        $legacyEnabled = (bool) config('wedding-sync-legacy.enabled', false);

        $this->assert($command, $spreadsheetId !== '', 'GOOGLE_SHEET_ID_V2 terbaca');
        $this->assert($command, $webhookToken !== '', 'Webhook token terbaca');
        $this->assert($command, $legacyEnabled === false, 'Legacy sync nonaktif');

        $jsonPath = $this->serviceAccountJsonPath();
        $this->assert($command, is_file($jsonPath), "Service account JSON ditemukan: {$jsonPath}");

        if (!is_file($jsonPath) || $spreadsheetId === '') {
            return $this->finish($command);
        }

        try {
            $sheets = new Sheets($this->makeGoogleClient($jsonPath));
            $spreadsheet = $sheets->spreadsheets->get($spreadsheetId);

            $command->info('OK   Spreadsheet bisa diakses: ' . $spreadsheet->getProperties()->getTitle());

            $sheetTitles = [];

            foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
                $properties = $sheet->getProperties();

                if ($properties) {
                    $sheetTitles[] = $properties->getTitle();
                }
            }

            $this->checkSheetsAndHeaders($command, $sheets, $spreadsheetId, $sheetTitles);
        } catch (Throwable $e) {
            $this->fail($command, 'Spreadsheet access gagal: ' . $e->getMessage());
        }

        return $this->finish($command);
    }

    private function checkSheetsAndHeaders(Command $command, Sheets $sheets, string $spreadsheetId, array $sheetTitles): void
    {
        $targets = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $targets[$module] = [
                'sheet' => $definition['sheet'] ?? null,
                'headers' => $definition['headers'] ?? [],
            ];
        }

        foreach ((array) config('wedding-sync-v2.system_sheets', []) as $module => $definition) {
            $targets[$module] = [
                'sheet' => $definition['sheet'] ?? null,
                'headers' => $definition['headers'] ?? [],
            ];
        }

        foreach ($targets as $module => $target) {
            $sheetName = $target['sheet'] ?? null;
            $expectedHeaders = array_values($target['headers'] ?? []);

            if (!$sheetName) {
                $this->fail($command, "{$module}: nama sheet kosong");
                continue;
            }

            if (!in_array($sheetName, $sheetTitles, true)) {
                $this->fail($command, "{$module}: sheet {$sheetName} tidak ditemukan");
                continue;
            }

            $command->info("OK   {$module}: sheet {$sheetName} tersedia");

            try {
                $range = "'" . str_replace("'", "''", $sheetName) . "'!A1:ZZ1";
                $response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
                $values = $response->getValues() ?? [];
                $actualHeaders = array_map(fn ($value) => trim((string) $value), $values[0] ?? []);

                $missing = array_values(array_diff($expectedHeaders, $actualHeaders));

                if (!empty($missing)) {
                    $this->fail($command, "{$module}: header hilang di {$sheetName}: " . implode(', ', $missing));
                    continue;
                }

                $command->info("OK   {$module}: header valid");
            } catch (Throwable $e) {
                $this->fail($command, "{$module}: gagal cek header {$sheetName}: " . $e->getMessage());
            }
        }
    }

    private function serviceAccountJsonPath(): string
    {
        $jsonPath = config('google-sheets.service_account_json');

        if (!$jsonPath) {
            $envPath = env('GOOGLE_SERVICE_ACCOUNT_JSON', 'storage/app/google/service-account.json');

            return str_starts_with($envPath, 'storage/')
                ? storage_path(substr($envPath, strlen('storage/')))
                : base_path($envPath);
        }

        return (string) $jsonPath;
    }

    private function makeGoogleClient(string $jsonPath): Client
    {
        if (!is_file($jsonPath)) {
            throw new RuntimeException("Service account JSON tidak ditemukan: {$jsonPath}");
        }

        $client = new Client();
        $client->setApplicationName('Wedding Dream Sync V2 Health Check');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }

    private function assert(Command $command, bool $condition, string $message): void
    {
        if ($condition) {
            $command->info("OK   {$message}");
            return;
        }

        $this->fail($command, $message);
    }

    private function fail(Command $command, string $message): void
    {
        $this->failures++;
        $command->error("FAIL {$message}");
    }

    private function finish(Command $command): int
    {
        $command->line(str_repeat('-', 34));

        if ($this->failures > 0) {
            $command->error("Health check selesai dengan {$this->failures} masalah.");
            return 1;
        }

        $command->info('Health check OK. Sync V2 siap dipakai.');
        return 0;
    }
}
