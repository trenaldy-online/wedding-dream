<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class WeddingSyncV2StatusController extends Controller
{
    public function index()
    {
        $status = [
            'spreadsheet_id' => (string) config('wedding-sync-v2.spreadsheet_id'),
            'legacy_enabled' => (bool) config('wedding-sync-legacy.enabled', false),
            'webhook_token_ok' => (string) config('wedding-sync-v2.webhook.token') !== '',
            'auto_refresh_dropdowns' => (bool) config('wedding-sync-v2.auto_refresh_dropdowns.enabled', true),
            'safe_delete' => (bool) config('wedding-sync-v2.safe_delete.enabled', true),
            'web_export_queue' => (bool) config('wedding-sync-v2.auto_export_from_web.use_queue', false),
            'spreadsheet_ok' => false,
            'spreadsheet_title' => null,
            'spreadsheet_error' => null,
            'sheets' => [],
            'latest_sync_logs' => [],
            'latest_change_logs' => [],
        ];

        try {
            $sheets = new Sheets($this->makeGoogleClient());
            $spreadsheet = $sheets->spreadsheets->get($status['spreadsheet_id']);

            $status['spreadsheet_ok'] = true;
            $status['spreadsheet_title'] = $spreadsheet->getProperties()->getTitle();

            $sheetTitles = [];

            foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
                $properties = $sheet->getProperties();

                if ($properties) {
                    $sheetTitles[] = $properties->getTitle();
                }
            }

            $status['sheets'] = $this->sheetStatus($sheets, $status['spreadsheet_id'], $sheetTitles);
            $status['latest_sync_logs'] = $this->latestSyncLogs($sheets, $status['spreadsheet_id']);
        } catch (Throwable $e) {
            $status['spreadsheet_error'] = $e->getMessage();
        }

        if (Schema::hasTable('wedding_sync_v2_change_logs')) {
            $status['latest_change_logs'] = DB::table('wedding_sync_v2_change_logs')
                ->latest('id')
                ->limit(10)
                ->get();
        }

        return view('wedding-sync-v2.status', [
            'status' => $status,
        ]);
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

        $client = new Client();
        $client->setApplicationName('Wedding Dream Sync V2 Status');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }

    private function sheetStatus(Sheets $sheets, string $spreadsheetId, array $sheetTitles): array
    {
        $rows = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $sheetName = $definition['sheet'] ?? '';
            $expectedHeaders = array_values($definition['headers'] ?? []);
            $exists = in_array($sheetName, $sheetTitles, true);
            $missingHeaders = [];

            if ($exists) {
                try {
                    $range = "'" . str_replace("'", "''", $sheetName) . "'!A1:ZZ1";
                    $response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
                    $actualHeaders = array_map(fn ($value) => trim((string) $value), ($response->getValues()[0] ?? []));
                    $missingHeaders = array_values(array_diff($expectedHeaders, $actualHeaders));
                } catch (Throwable $e) {
                    $missingHeaders = ['Gagal membaca header: ' . $e->getMessage()];
                }
            }

            $rows[] = [
                'module' => $module,
                'sheet' => $sheetName,
                'exists' => $exists,
                'headers_ok' => $exists && empty($missingHeaders),
                'missing_headers' => $missingHeaders,
            ];
        }

        return $rows;
    }

    private function latestSyncLogs(Sheets $sheets, string $spreadsheetId): array
    {
        try {
            $sheetName = config('wedding-sync-v2.system_sheets.sync_log.sheet', 'SYNC_LOG');
            $range = "'" . str_replace("'", "''", $sheetName) . "'!A1:Z200";
            $response = $sheets->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues() ?? [];

            if (count($values) <= 1) {
                return [];
            }

            $headers = array_map(fn ($value) => trim((string) $value), array_shift($values));
            $logs = [];

            foreach (array_reverse($values) as $row) {
                $item = [];

                foreach ($headers as $index => $header) {
                    $item[$header] = $row[$index] ?? '';
                }

                $logs[] = $item;

                if (count($logs) >= 10) {
                    break;
                }
            }

            return $logs;
        } catch (Throwable $e) {
            return [
                [
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }
}
