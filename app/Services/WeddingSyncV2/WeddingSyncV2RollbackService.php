<?php

namespace App\Services\WeddingSyncV2;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class WeddingSyncV2RollbackService
{
    private Carbon $now;

    public function rollback(Command $command, int $logId, bool $dryRun = false, bool $deleteCreated = false): void
    {
        if (!Schema::hasTable('wedding_sync_v2_change_logs')) {
            throw new RuntimeException('Tabel wedding_sync_v2_change_logs belum ada. Jalankan migration terlebih dahulu.');
        }

        $this->now = Carbon::now(config('wedding-sync-v2.timezone', config('app.timezone', 'Asia/Jakarta')));

        $log = DB::table('wedding_sync_v2_change_logs')->where('id', $logId)->first();

        if (!$log) {
            throw new RuntimeException("Change log ID {$logId} tidak ditemukan.");
        }

        if (($log->rollback_status ?? null) === 'rolled_back') {
            throw new RuntimeException("Change log ID {$logId} sudah pernah di-rollback.");
        }

        $modelType = (string) ($log->model_type ?? '');

        if ($modelType === '' || !class_exists($modelType)) {
            throw new RuntimeException("Model type tidak valid pada change log ID {$logId}.");
        }

        /** @var Model $model */
        $model = new $modelType();

        $record = $modelType::query()->find($log->model_id);

        $oldPayload = $this->decodePayload($log->old_payload ?? null);
        $newPayload = $this->decodePayload($log->new_payload ?? null);
        $action = (string) ($log->action ?? '');

        $command->info($dryRun ? "DRY RUN rollback log ID {$logId}" : "Mulai rollback log ID {$logId}");
        $command->line("Module : {$log->module}");
        $command->line("Action : {$action}");
        $command->line("Model  : {$modelType}");
        $command->line("ID     : {$log->model_id}");
        $command->newLine();

        if ($action === 'create') {
            if (!$deleteCreated) {
                $command->warn('Log ini adalah action=create.');
                $command->warn('Rollback create berarti menghapus record yang dibuat dari sheet.');
                $command->warn('Jalankan dengan --delete-created jika memang ingin menghapus record tersebut.');

                return;
            }

            if (!$record) {
                $command->warn('Record sudah tidak ditemukan. Log akan ditandai rolled_back.');

                if (!$dryRun) {
                    $this->markRolledBack($logId);
                    $this->appendRollbackLog($log, 'system', 'created_record_missing', 'deleted/missing', 'success', 'Record already missing.');
                }

                return;
            }

            if ($dryRun) {
                $command->line('Record berikut akan dihapus:');
                $command->line(json_encode($record->getAttributes(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                return;
            }

            $record->delete();

            $this->markRolledBack($logId);
            $this->appendRollbackLog($log, 'system', 'created', 'deleted', 'success', 'Created record deleted by rollback.');
            $this->exportModule($command, (string) $log->module);

            $command->info('Rollback create selesai. Record berhasil dihapus dan sheet diperbarui.');

            return;
        }

        if ($action !== 'update') {
            throw new RuntimeException("Action {$action} belum didukung untuk rollback otomatis.");
        }

        if (!$record) {
            throw new RuntimeException("Record ID {$log->model_id} tidak ditemukan di database.");
        }

        if (empty($oldPayload)) {
            throw new RuntimeException("old_payload kosong. Tidak ada data lama untuk rollback.");
        }

        $rollbackPayload = $this->filterRollbackPayload($record, $oldPayload);

        if (empty($rollbackPayload)) {
            throw new RuntimeException('Tidak ada field valid yang bisa di-rollback.');
        }

        $command->line('Field yang akan dikembalikan:');

        foreach ($rollbackPayload as $field => $oldValue) {
            $currentValue = $record->getAttribute($field);

            $command->line("- {$field}: " . $this->stringValue($currentValue) . " → " . $this->stringValue($oldValue));
        }

        if ($dryRun) {
            $command->newLine();
            $command->info('DRY RUN selesai. Tidak ada data yang diubah.');

            return;
        }

        $record->fill($rollbackPayload);
        $record->save();

        $this->markRolledBack($logId);
        $this->appendRollbackLog($log, 'system', 'current', 'old_payload', 'success', 'Rollback update applied.');
        $this->exportModule($command, (string) $log->module);

        $command->newLine();
        $command->info('Rollback selesai. Data web dan spreadsheet sudah diperbarui.');
    }

    private function decodePayload(?string $payload): array
    {
        if (!$payload) {
            return [];
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function filterRollbackPayload(Model $record, array $payload): array
    {
        $table = $record->getTable();

        $ignored = [
            'id',
            'created_at',
            'updated_at',
        ];

        $filtered = [];

        foreach ($payload as $field => $value) {
            if (in_array($field, $ignored, true)) {
                continue;
            }

            if (!Schema::hasColumn($table, $field)) {
                continue;
            }

            $filtered[$field] = $value;
        }

        return $filtered;
    }

    private function markRolledBack(int $logId): void
    {
        DB::table('wedding_sync_v2_change_logs')
            ->where('id', $logId)
            ->update([
                'rollback_status' => 'rolled_back',
                'rolled_back_at' => $this->now->format('Y-m-d H:i:s'),
                'updated_at' => $this->now->format('Y-m-d H:i:s'),
            ]);
    }

    private function appendRollbackLog(object $log, string $rollbackBy, string $from, string $to, string $status, string $message): void
    {
        $definition = config('wedding-sync-v2.system_sheets.rollback_log');

        if (!$definition) {
            return;
        }

        $spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($spreadsheetId === '') {
            return;
        }

        $sheet = $definition['sheet'] ?? 'ROLLBACK_LOG';
        $headers = $definition['headers'] ?? [];

        $payload = [
            'waktu' => $this->now->format('Y-m-d H:i:s'),
            'module' => $log->module ?? '',
            'web_id' => $log->model_id ?? '',
            'sync_key' => $log->sync_key ?? '',
            'item' => $this->itemNameFromLog($log),
            'rollback_by' => $rollbackBy,
            'rollback_from' => $from,
            'rollback_to' => $to,
            'status' => $status,
            'message' => $message,
        ];

        $row = [];

        foreach ($headers as $header) {
            $row[] = $payload[$header] ?? '';
        }

        $body = new ValueRange([
            'values' => [
                $row,
            ],
        ]);

        $sheets = new Sheets($this->makeGoogleClient());

        $sheets->spreadsheets_values->append(
            $spreadsheetId,
            $this->quoteSheet($sheet) . '!A1',
            $body,
            [
                'valueInputOption' => 'RAW',
                'insertDataOption' => 'INSERT_ROWS',
            ]
        );
    }

    private function itemNameFromLog(object $log): string
    {
        $newPayload = $this->decodePayload($log->new_payload ?? null);
        $oldPayload = $this->decodePayload($log->old_payload ?? null);

        foreach (['title', 'item_name', 'name'] as $field) {
            if (!empty($newPayload[$field])) {
                return (string) $newPayload[$field];
            }

            if (!empty($oldPayload[$field])) {
                return (string) $oldPayload[$field];
            }
        }

        return '';
    }

    private function exportModule(Command $command, string $module): void
    {
        if ($module === '') {
            return;
        }

        app(WeddingSyncV2WebExporter::class)->export($command, $module, false);
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

    private function quoteSheet(string $sheet): string
    {
        return "'" . str_replace("'", "''", $sheet) . "'";
    }

    private function stringValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
