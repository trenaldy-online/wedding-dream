<?php

namespace App\Services\WeddingSyncV2;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use RuntimeException;

class WeddingSyncV2SheetGuide
{
    private Sheets $sheets;

    private string $spreadsheetId;

    private string $guideSheet = 'PANDUAN_INPUT';

    private array $systemColumns = [
        'web_id',
        'sync_key',
        'last_modified_at',
        'last_modified_by',
        'last_modified_source',
        'last_synced_at',
        'row_hash',
    ];

    public function apply(Command $command): void
    {
        $this->spreadsheetId = (string) config('wedding-sync-v2.spreadsheet_id');

        if ($this->spreadsheetId === '') {
            throw new RuntimeException('GOOGLE_SHEET_ID_V2 belum diisi di .env.');
        }

        $this->sheets = new Sheets($this->makeGoogleClient());

        $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId);
        $sheetIds = $this->sheetIdsFromSpreadsheet($spreadsheet);

        if (!isset($sheetIds[$this->guideSheet])) {
            $this->createGuideSheet();
            $command->info("CREATED SHEET: {$this->guideSheet}");

            $spreadsheet = $this->sheets->spreadsheets->get($this->spreadsheetId);
            $sheetIds = $this->sheetIdsFromSpreadsheet($spreadsheet);
        }

        $this->writeGuide();
        $this->formatGuide((int) $sheetIds[$this->guideSheet]);
        $this->applyHeaderNotesAndMarkers($sheetIds);

        $command->info('Panduan input, note header, dan tanda kolom berhasil diterapkan.');
    }

    private function sheetIdsFromSpreadsheet($spreadsheet): array
    {
        $sheetIds = [];

        foreach ($spreadsheet->getSheets() ?? [] as $sheet) {
            $properties = $sheet->getProperties();

            if (!$properties) {
                continue;
            }

            $sheetIds[$properties->getTitle()] = $properties->getSheetId();
        }

        return $sheetIds;
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
        $client->setApplicationName('Wedding Dream Sync V2 Guide');
        $client->setAuthConfig($jsonPath);
        $client->setScopes([
            Sheets::SPREADSHEETS,
        ]);

        return $client;
    }

    private function createGuideSheet(): void
    {
        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                [
                    'addSheet' => [
                        'properties' => [
                            'title' => $this->guideSheet,
                            'index' => 0,
                            'gridProperties' => [
                                'rowCount' => 100,
                                'columnCount' => 8,
                                'frozenRowCount' => 1,
                            ],
                            'tabColor' => [
                                'red' => 0.10,
                                'green' => 0.45,
                                'blue' => 0.38,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $body);
    }

    private function writeGuide(): void
    {
        $rows = [
            [
                'Bagian',
                'Arti',
                'Cara Pakai',
                'Wajib Diisi',
                'Opsional',
                'Jangan Diisi',
                'Kapan SYNC?',
                'Catatan',
            ],
            [
                'LEGENDA WARNA HEADER',
                'KUNING = WAJIB, HIJAU = ISI TERAKHIR, BIRU = OPSIONAL, ABU-ABU = SISTEM',
                'Lihat warna header di setiap sheet input.',
                'Isi semua kolom kuning untuk row baru.',
                'Kolom biru boleh kosong.',
                'Kolom abu-abu jangan disentuh.',
                'Kolom hijau sync_action diisi paling akhir.',
                'Header juga punya note. Hover di nama kolom untuk membaca penjelasan.',
            ],
            [
                'ATURAN ROW BARU',
                'Row baru dari Sheet tidak langsung masuk web.',
                'Isi data dari kiri ke kanan. Setelah lengkap, pilih sync_action = SYNC.',
                'Kolom kuning + sync_action.',
                'Kolom biru jika diperlukan.',
                'Kolom abu-abu.',
                'Pilih SYNC setelah semua data lengkap.',
                'Setelah berhasil, sistem akan mengubah sync_action menjadi SYNCED. Untuk menghapus data, kosongkan isi data pada baris tersebut. Jangan hapus row fisik langsung.',
            ],
            [
                'ATURAN ROW LAMA',
                'Row yang sudah punya web_id adalah data lama.',
                'Cukup edit cell yang ingin diubah.',
                'Tidak perlu ubah sync_action.',
                'Boleh update kolom biru.',
                'Jangan edit kolom abu-abu.',
                'Tidak wajib pilih SYNC lagi.',
                'Sistem tetap mencatat audit log.',
            ],
            [
                'INPUT_PERSIAPAN',
                'Checklist persiapan nikah.',
                '1) acara → 2) tugas → 3) pic → 4) status → 5) opsional lain → 6) sync_action = SYNC.',
                'acara, tugas, pic, status',
                'kategori, prioritas, deadline, progress_percent, dependensi, catatan',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'Kategori boleh pilih dropdown atau ketik baru. Untuk budget, jika kategori dikosongkan sistem akan memakai Lainnya.',
            ],
            [
                'INPUT_DOKUMEN',
                'Checklist dokumen nikah.',
                '1) acara → 2) kategori Dokumen Nikah → 3) dokumen → 4) pic → 5) status → 6) sync_action = SYNC.',
                'acara, kategori, dokumen, pic, status',
                'deadline, progress_percent, catatan',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'Dokumen tidak memakai prioritas.',
            ],
            [
                'BUDGET_CPP',
                'Budget pihak CPP.',
                '1) item → 2) status_bayar → 3) kategori/nominal/catatan jika ada → 4) sync_action = SYNC.',
                'item, status_bayar',
                'kategori, estimasi, aktual, catatan',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'Nominal cukup angka, contoh 1500000.',
            ],
            [
                'BUDGET_CPW',
                'Budget pihak CPW.',
                '1) item → 2) status_bayar → 3) kategori/nominal/catatan jika ada → 4) sync_action = SYNC.',
                'item, status_bayar',
                'kategori, estimasi, aktual, catatan',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'Nominal cukup angka, contoh 1500000.',
            ],
            [
                'TAMU_CPP',
                'Data tamu pihak CPP.',
                '1) nama → 2) no_wa → 3) grup → 4) jumlah_undangan → 5) opsional lain → 6) sync_action = SYNC.',
                'nama, no_wa, grup, jumlah_undangan',
                'alamat, RSVP, attendance, amplop, souvenir, catatan_sync',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'No WA gunakan format 62..., contoh 6281234567890.',
            ],
            [
                'TAMU_CPW',
                'Data tamu pihak CPW.',
                '1) nama → 2) no_wa → 3) grup → 4) jumlah_undangan → 5) opsional lain → 6) sync_action = SYNC.',
                'nama, no_wa, grup, jumlah_undangan',
                'alamat, RSVP, attendance, amplop, souvenir, catatan_sync',
                'web_id, sync_key, last_modified, row_hash',
                'Isi sync_action = SYNC paling akhir.',
                'No WA gunakan format 62..., contoh 6281234567890.',
            ],
        ];

        $this->sheets->spreadsheets_values->clear(
            $this->spreadsheetId,
            $this->quoteSheet($this->guideSheet) . '!A1:H100',
            new ClearValuesRequest()
        );

        $body = new ValueRange([
            'values' => $rows,
        ]);

        $this->sheets->spreadsheets_values->update(
            $this->spreadsheetId,
            $this->quoteSheet($this->guideSheet) . '!A1:H' . count($rows),
            $body,
            ['valueInputOption' => 'RAW']
        );
    }

    private function formatGuide(int $sheetId): void
    {
        $requests = [
            [
                'updateSheetProperties' => [
                    'properties' => [
                        'sheetId' => $sheetId,
                        'gridProperties' => [
                            'frozenRowCount' => 1,
                        ],
                    ],
                    'fields' => 'gridProperties.frozenRowCount',
                ],
            ],
            [
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 8,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'backgroundColor' => $this->rgb(0.13, 0.32, 0.29),
                            'horizontalAlignment' => 'CENTER',
                            'verticalAlignment' => 'MIDDLE',
                            'wrapStrategy' => 'WRAP',
                            'textFormat' => [
                                'bold' => true,
                                'foregroundColor' => $this->rgb(1, 1, 1),
                            ],
                        ],
                    ],
                    'fields' => 'userEnteredFormat(backgroundColor,horizontalAlignment,verticalAlignment,wrapStrategy,textFormat)',
                ],
            ],
            [
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 1,
                        'endRowIndex' => 100,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 8,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'verticalAlignment' => 'TOP',
                            'wrapStrategy' => 'WRAP',
                            'textFormat' => [
                                'fontSize' => 10,
                            ],
                        ],
                    ],
                    'fields' => 'userEnteredFormat(verticalAlignment,wrapStrategy,textFormat.fontSize)',
                ],
            ],
            [
                'setBasicFilter' => [
                    'filter' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 0,
                            'endRowIndex' => 100,
                            'startColumnIndex' => 0,
                            'endColumnIndex' => 8,
                        ],
                    ],
                ],
            ],
        ];

        $widths = [160, 260, 420, 260, 360, 280, 260, 360];

        foreach ($widths as $index => $width) {
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
                    ],
                    'fields' => 'pixelSize',
                ],
            ];
        }

        $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ])
        );
    }

    private function applyHeaderNotesAndMarkers(array $sheetIds): void
    {
        $requests = [];

        foreach ((array) config('wedding-sync-v2.modules', []) as $module => $definition) {
            $sheetName = $definition['sheet'] ?? null;
            $headers = array_values($definition['headers'] ?? []);

            if (!$sheetName || !isset($sheetIds[$sheetName]) || empty($headers)) {
                continue;
            }

            $sheetId = (int) $sheetIds[$sheetName];

            foreach ($headers as $index => $header) {
                $type = $this->inputTypeFor($sheetName, $header);
                $note = $this->noteFor($sheetName, $header, $type);
                $format = $this->headerFormatFor($type);

                $requests[] = [
                    'updateCells' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 0,
                            'endRowIndex' => 1,
                            'startColumnIndex' => $index,
                            'endColumnIndex' => $index + 1,
                        ],
                        'rows' => [
                            [
                                'values' => [
                                    [
                                        'note' => $note,
                                        'userEnteredFormat' => $format,
                                    ],
                                ],
                            ],
                        ],
                        'fields' => 'note,userEnteredFormat(backgroundColor,horizontalAlignment,verticalAlignment,wrapStrategy,textFormat)',
                    ],
                ];
            }
        }

        if (empty($requests)) {
            return;
        }

        $this->sheets->spreadsheets->batchUpdate(
            $this->spreadsheetId,
            new BatchUpdateSpreadsheetRequest([
                'requests' => $requests,
            ])
        );
    }

    private function inputTypeFor(string $sheetName, string $header): string
    {
        if (in_array($header, $this->systemColumns, true)) {
            return 'system';
        }

        if ($header === 'sync_action') {
            return 'final';
        }

        $required = [
            'INPUT_PERSIAPAN' => ['acara', 'tugas', 'pic', 'status'],
            'INPUT_DOKUMEN' => ['acara', 'kategori', 'dokumen', 'pic', 'status'],
            'BUDGET_CPP' => ['item', 'status_bayar'],
            'BUDGET_CPW' => ['item', 'status_bayar'],
            'TAMU_CPP' => ['nama', 'no_wa', 'grup', 'jumlah_undangan'],
            'TAMU_CPW' => ['nama', 'no_wa', 'grup', 'jumlah_undangan'],
        ];

        if (in_array($header, $required[$sheetName] ?? [], true)) {
            return 'required';
        }

        return 'optional';
    }

    private function headerFormatFor(string $type): array
    {
        $format = [
            'horizontalAlignment' => 'CENTER',
            'verticalAlignment' => 'MIDDLE',
            'wrapStrategy' => 'WRAP',
            'textFormat' => [
                'bold' => true,
                'fontSize' => 9,
                'foregroundColor' => $this->rgb(0.08, 0.08, 0.08),
            ],
        ];

        if ($type === 'required') {
            $format['backgroundColor'] = $this->rgb(1.00, 0.90, 0.45);
            return $format;
        }

        if ($type === 'final') {
            $format['backgroundColor'] = $this->rgb(0.63, 0.86, 0.67);
            return $format;
        }

        if ($type === 'system') {
            $format['backgroundColor'] = $this->rgb(0.82, 0.82, 0.82);
            $format['textFormat']['foregroundColor'] = $this->rgb(0.35, 0.35, 0.35);
            return $format;
        }

        $format['backgroundColor'] = $this->rgb(0.78, 0.89, 1.00);

        return $format;
    }

    private function noteFor(string $sheetName, string $header, string $type): string
    {
        $prefix = match ($type) {
            'required' => '[WAJIB DIISI]',
            'final' => '[ISI TERAKHIR]',
            'system' => '[JANGAN DIISI - KOLOM SISTEM]',
            default => '[OPSIONAL]',
        };

        if ($type === 'system') {
            return $prefix . "\nKolom ini dipakai sistem untuk mencocokkan data Web dan Sheet. Jangan diubah manual.";
        }

        if ($header === 'sync_action') {
            return $prefix . "\nUntuk row baru dari Sheet: isi semua data sampai lengkap, lalu pilih SYNC. Setelah berhasil, sistem akan mengubahnya menjadi SYNCED. Untuk menghapus data lama, kosongkan isi kolom input pada baris tersebut. Jangan hapus row fisik langsung dari Sheet.";
        }

        $common = [
            'acara' => 'Pilih acara dari dropdown agar data masuk ke event yang benar.',
            'pic' => 'Pilih pihak yang bertanggung jawab: CPW, CPP, atau Bersama.',
            'status' => 'Pilih status dari dropdown.',
            'deadline' => 'Gunakan format yyyy-mm-dd, contoh 2026-06-20.',
            'progress_percent' => 'Isi angka 0 sampai 100.',
            'catatan' => 'Isi catatan tambahan jika ada.',
            'kategori' => 'Pilih dari dropdown. Untuk kategori fleksibel seperti Persiapan/Budget, boleh mengetik kategori baru. Pada Budget, jika kosong akan otomatis menjadi Lainnya.',
            'prioritas' => 'Pilih prioritas dari dropdown.',
            'status_bayar' => 'Pilih status bayar dari dropdown.',
            'estimasi' => 'Isi angka saja, contoh 1500000. Tidak perlu Rp.',
            'aktual' => 'Isi angka saja, contoh 1500000. Tidak perlu Rp.',
            'nama' => 'Isi nama tamu.',
            'no_wa' => 'Gunakan format 62..., contoh 6281234567890.',
            'alamat' => 'Isi alamat tamu jika ada.',
            'grup' => 'Pilih dropdown atau ketik grup baru.',
            'jumlah_undangan' => 'Isi angka, contoh 1 atau 2.',
            'catatan_sync' => 'Catatan internal untuk admin/sinkronisasi.',
            'dependensi' => 'Isi tugas yang menjadi ketergantungan jika ada.',
            'rsvp_status' => 'Status RSVP tamu jika sudah ada.',
            'rsvp_count' => 'Jumlah tamu yang konfirmasi hadir.',
            'rsvp_note' => 'Catatan RSVP dari tamu.',
            'invitation_status' => 'Status pengiriman undangan.',
            'attendance_status' => 'Status kehadiran hari-H.',
            'actual_attendance_count' => 'Jumlah aktual tamu yang hadir.',
            'envelope_amount' => 'Nominal amplop, isi angka saja.',
            'souvenir_status' => 'Status souvenir.',
            'souvenir_count' => 'Jumlah souvenir.',
        ];

        $specific = match ($header) {
            'tugas' => 'Isi nama tugas/checklist persiapan.',
            'dokumen' => 'Isi nama dokumen nikah.',
            'item' => 'Isi nama item biaya.',
            default => $common[$header] ?? 'Isi hanya jika diperlukan.',
        };

        return $prefix . "\n" . $specific;
    }

    private function rgb(float $red, float $green, float $blue): array
    {
        return [
            'red' => $red,
            'green' => $green,
            'blue' => $blue,
        ];
    }

    private function quoteSheet(string $sheet): string
    {
        return "'" . str_replace("'", "''", $sheet) . "'";
    }
}
