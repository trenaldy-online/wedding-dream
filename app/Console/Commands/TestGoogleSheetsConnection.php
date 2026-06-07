<?php

namespace App\Console\Commands;

use App\Services\WeddingSync\GoogleSheetsClient;
use Illuminate\Console\Command;
use Throwable;

class TestGoogleSheetsConnection extends Command
{
    protected $signature = 'wedding:sheet-test 
                            {sheet=MASTER_EVENT : Nama tab Google Sheet yang ingin dibaca}
                            {--range=A1:Z20 : Range data yang ingin dibaca}';

    protected $description = 'Test koneksi Laravel ke Google Sheet wedding';

    public function handle(GoogleSheetsClient $sheets): int
    {
        $sheetName = (string) $this->argument('sheet');
        $range = (string) $this->option('range');

        $this->info('Menguji koneksi ke Google Sheet...');
        $this->line("Sheet: {$sheetName}");
        $this->line("Range: {$range}");
        $this->newLine();

        try {
            $values = $sheets->getValues($sheetName, $range);

            if (empty($values)) {
                $this->warn('Koneksi berhasil, tetapi data kosong pada range tersebut.');
                return self::SUCCESS;
            }

            $this->info('Koneksi berhasil. Data mentah:');
            $this->newLine();

            foreach ($values as $rowIndex => $row) {
                $displayRowNumber = $rowIndex + 1;
                $this->line($displayRowNumber . '. ' . json_encode($row, JSON_UNESCAPED_UNICODE));
            }

            $this->newLine();

            $rows = $sheets->getRowsWithHeader($sheetName, $range);

            $this->info('Data dengan header ternormalisasi:');
            $this->line('Total row data: ' . count($rows));

            foreach (array_slice($rows, 0, 5) as $row) {
                $this->line(json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Gagal membaca Google Sheet.');
            $this->newLine();
            $this->error($e->getMessage());

            $this->newLine();
            $this->warn('Cek beberapa hal berikut:');
            $this->line('1. GOOGLE_SHEET_ID di .env sudah benar.');
            $this->line('2. storage/app/google/service-account.json sudah ada.');
            $this->line('3. Google Sheets API sudah Enable di Google Cloud.');
            $this->line('4. Google Sheet sudah di-share sebagai Editor ke client_email service account.');
            $this->line('5. Nama tab sheet benar, misalnya MASTER_EVENT.');

            return self::FAILURE;
        }
    }
}
