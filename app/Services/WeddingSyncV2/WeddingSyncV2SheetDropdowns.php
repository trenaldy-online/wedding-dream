<?php

namespace App\Services\WeddingSyncV2;

use App\Models\BudgetItem;
use App\Models\ChecklistItem;
use App\Models\Guest;
use App\Models\WeddingEvent;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use RuntimeException;

class WeddingSyncV2SheetDropdowns
{
    private Sheets $sheets;

    private string $spreadsheetId;

    public function apply(?Command $command = null): void
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

        $rules = $this->rules();
        $requests = [];

        foreach ($rules as $sheetName => $columns) {
            if (!isset($sheetIds[$sheetName])) {
                if ($command) { $command->warn("SKIP: {$sheetName} tidak ditemukan."); }
                continue;
            }

            $headers = $this->headersForSheet($sheetName);

            if (empty($headers)) {
                if ($command) { $command->warn("SKIP: header {$sheetName} tidak ditemukan di config."); }
                continue;
            }

            $applied = 0;

            foreach ($columns as $header => $rule) {
                $columnIndex = array_search($header, $headers, true);

                if ($columnIndex === false) {
                    if ($command) { $command->warn("SKIP: kolom {$header} tidak ditemukan di {$sheetName}."); }
                    continue;
                }

                $values = $this->cleanOptions($rule['values'] ?? []);

                if (empty($values)) {
                    if ($command) { $command->warn("SKIP: opsi {$sheetName}.{$header} kosong."); }
                    continue;
                }

                $requests[] = $this->dataValidationRequest(
                    (int) $sheetIds[$sheetName],
                    $columnIndex,
                    $values,
                    (bool) ($rule['strict'] ?? false),
                    (string) ($rule['message'] ?? '')
                );

                $applied++;
            }

            if ($command) { $command->info("OK: {$sheetName} {$applied} dropdown diterapkan."); }
        }

        if (empty($requests)) {
            if ($command) { $command->warn('Tidak ada dropdown yang diterapkan.'); }
            return;
        }

        $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ])
        );

        if ($command) { $command->newLine(); }
        if ($command) { $command->info('Dropdown spreadsheet v2 selesai diterapkan.'); }
    }

    public function applySilently(): void
    {
        $this->apply(null);
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

    private function rules(): array
    {
        $eventOptions = $this->eventOptions();
        $picOptions = ['CPW', 'CPP', 'Bersama'];
        $checklistStatusOptions = ['Belum', 'Proses', 'Selesai', 'Ditunda', 'Batal'];
        $priorityOptions = ['Wajib', 'Penting', 'Opsional', 'Bisa Ditunda'];
        $paymentStatusOptions = ['Belum Bayar', 'Sebagian', 'Lunas'];
        $syncActionOptions = ['DRAFT', 'SYNC', 'SYNCED', 'DELETE', 'DELETED'];

        $persiapanCategories = $this->mergeOptions(
            ['Persiapan', 'Vendor', 'Venue', 'Dekorasi', 'Catering', 'Undangan'],
            ChecklistItem::query()
                ->whereNotIn('category', ['Dokumen', 'Dokumen Nikah'])
                ->distinct()
                ->pluck('category')
        );

        $budgetCategories = $this->mergeOptions(
            ['Venue', 'Dekorasi', 'Catering', 'Busana', 'Dokumentasi', 'Undangan', 'Souvenir', 'Lainnya'],
            BudgetItem::query()
                ->distinct()
                ->pluck('category')
        );

        $guestGroups = $this->mergeOptions(
            ['Keluarga', 'Tetangga', 'Teman', 'Rekan Kerja', 'VIP'],
            Guest::query()
                ->distinct()
                ->pluck('group_name')
        );

        return [
            'INPUT_PERSIAPAN' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'acara' => [
                    'values' => $eventOptions,
                    'strict' => true,
                    'message' => 'Pilih acara yang sudah ada di web.',
                ],
                'kategori' => [
                    'values' => $persiapanCategories,
                    'strict' => false,
                    'message' => 'Pilih kategori yang tersedia atau ketik kategori baru.',
                ],
                'pic' => [
                    'values' => $picOptions,
                    'strict' => true,
                    'message' => 'Pilih PIC: CPW, CPP, atau Bersama.',
                ],
                'prioritas' => [
                    'values' => $priorityOptions,
                    'strict' => true,
                    'message' => 'Pilih prioritas yang tersedia.',
                ],
                'status' => [
                    'values' => $checklistStatusOptions,
                    'strict' => true,
                    'message' => 'Pilih status yang tersedia.',
                ],
            ],

            'INPUT_DOKUMEN' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'acara' => [
                    'values' => $eventOptions,
                    'strict' => true,
                    'message' => 'Pilih acara yang sudah ada di web.',
                ],
                'kategori' => [
                    'values' => ['Dokumen Nikah'],
                    'strict' => true,
                    'message' => 'Dokumen wajib memakai kategori Dokumen Nikah.',
                ],
                'pic' => [
                    'values' => $picOptions,
                    'strict' => true,
                    'message' => 'Pilih PIC: CPW, CPP, atau Bersama.',
                ],
                'status' => [
                    'values' => $checklistStatusOptions,
                    'strict' => true,
                    'message' => 'Pilih status yang tersedia.',
                ],
            ],

            'BUDGET_CPP' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'kategori' => [
                    'values' => $budgetCategories,
                    'strict' => false,
                    'message' => 'Pilih kategori yang tersedia atau ketik kategori baru.',
                ],
                'status_bayar' => [
                    'values' => $paymentStatusOptions,
                    'strict' => true,
                    'message' => 'Pilih status bayar.',
                ],
            ],

            'BUDGET_CPW' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'kategori' => [
                    'values' => $budgetCategories,
                    'strict' => false,
                    'message' => 'Pilih kategori yang tersedia atau ketik kategori baru.',
                ],
                'status_bayar' => [
                    'values' => $paymentStatusOptions,
                    'strict' => true,
                    'message' => 'Pilih status bayar.',
                ],
            ],

            'TAMU_CPP' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'grup' => [
                    'values' => $guestGroups,
                    'strict' => false,
                    'message' => 'Pilih grup yang tersedia atau ketik grup baru.',
                ],
            ],

            'TAMU_CPW' => [                'sync_action' => [
                    'values' => $syncActionOptions,
                    'strict' => true,
                    'message' => 'Untuk row baru dari Sheet, pilih SYNC setelah data lengkap. DRAFT untuk menunda. DELETE untuk menghapus aman. SYNCED/DELETED berarti sudah diproses.',
                ],


                'grup' => [
                    'values' => $guestGroups,
                    'strict' => false,
                    'message' => 'Pilih grup yang tersedia atau ketik grup baru.',
                ],
            ],
        ];
    }

    private function headersForSheet(string $sheetName): array
    {
        foreach ((array) config('wedding-sync-v2.modules', []) as $definition) {
            if (($definition['sheet'] ?? null) === $sheetName) {
                return array_values($definition['headers'] ?? []);
            }
        }

        return [];
    }

    private function eventOptions(): array
    {
        $events = WeddingEvent::query()->get();

        return $events
            ->map(function ($event) {
                foreach (['name', 'event_name', 'title', 'slug', 'event_key'] as $field) {
                    $value = $event->getAttribute($field);

                    if ($value !== null && trim((string) $value) !== '') {
                        return (string) $value;
                    }
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function mergeOptions(array $defaults, Collection $dynamic): array
    {
        return collect($defaults)
            ->merge($dynamic)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function cleanOptions(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->take(500)
            ->values()
            ->all();
    }

    private function dataValidationRequest(
        int $sheetId,
        int $columnIndex,
        array $values,
        bool $strict,
        string $message
    ): array {
        return [
            'setDataValidation' => [
                'range' => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => 1,
                    'endRowIndex' => 1000,
                    'startColumnIndex' => $columnIndex,
                    'endColumnIndex' => $columnIndex + 1,
                ],
                'rule' => [
                    'condition' => [
                        'type' => 'ONE_OF_LIST',
                        'values' => array_map(
                            fn ($value) => ['userEnteredValue' => $value],
                            $values
                        ),
                    ],
                    'inputMessage' => $message,
                    'strict' => $strict,
                    'showCustomUi' => true,
                ],
            ],
        ];
    }
}
