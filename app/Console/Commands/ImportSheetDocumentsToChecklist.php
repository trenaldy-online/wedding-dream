<?php

namespace App\Console\Commands;

use App\Models\ChecklistItem;
use App\Models\SyncDifference;
use App\Models\WeddingProfile;
use App\Services\WeddingSync\SyncHasher;
use Illuminate\Console\Command;

class ImportSheetDocumentsToChecklist extends Command
{
    protected $signature = 'wedding:import-sheet-documents
                            {--dry-run : Simulasi saja, tidak menyimpan ke database}
                            {--mark-resolved : Tandai sync difference sebagai resolved setelah import}';

    protected $description = 'Import data INPUT_DOKUMEN dari sync_differences ke halaman checklist';

    public function handle(SyncHasher $hasher): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $markResolved = (bool) $this->option('mark-resolved');

        $profile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $differences = SyncDifference::query()
            ->where('module', 'checklist_items')
            ->where('sheet_name', 'INPUT_DOKUMEN')
            ->where('difference_type', 'sheet_only')
            ->where('status', 'pending')
            ->orderBy('sheet_row')
            ->get();

        if ($differences->isEmpty()) {
            $this->info('Tidak ada dokumen pending dari INPUT_DOKUMEN yang perlu diimport.');
            return self::SUCCESS;
        }

        $this->info('Total dokumen yang ditemukan: ' . $differences->count());

        if ($dryRun) {
            $this->warn('Mode DRY RUN aktif. Data tidak akan disimpan.');
        }

        $previewRows = [];

        foreach ($differences as $difference) {
            $payload = $difference->sheet_payload ?? [];

            $recordKey = $payload['record_key'] ?? null;
            $title = $payload['title'] ?? null;

            if (!$recordKey || !$title) {
                $this->warn('Lewati baris karena record_key/title kosong. Sheet row: ' . ($difference->sheet_row ?? '-'));
                continue;
            }

            $status = $payload['status'] ?? 'todo';

            $data = [
                'wedding_profile_id' => $profile->id,
                'wedding_event_id' => null,

                'title' => $title,
                'category' => 'Dokumen Nikah',
                'assigned_to' => $payload['assigned_to'] ?? 'both',
                'status' => $status,
                'due_date' => $payload['due_date'] ?? null,
                'note' => $payload['sync_note'] ?? null,
                'completed_at' => $status === 'done' ? now() : null,

                'sheet_key' => $recordKey,
                'sheet_row' => $difference->sheet_row,
                'sync_source' => 'sheet',
                'sheet_hash' => $hasher->make($payload),
                'web_hash' => null,
                'last_synced_at' => now(),
                'last_checked_at' => now(),
                'is_dummy' => false,
                'sync_note' => $payload['sync_note'] ?? null,
            ];

            $previewRows[] = [
                $recordKey,
                $title,
                $data['assigned_to'],
                $status,
            ];

            if (!$dryRun) {
                ChecklistItem::updateOrCreate(
                    ['sheet_key' => $recordKey],
                    $data
                );

                if ($markResolved) {
                    $difference->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                        'note' => trim(($difference->note ?? '') . "\nImported to checklist_items from INPUT_DOKUMEN."),
                    ]);
                }
            }
        }

        $this->table(
            ['Key', 'Dokumen', 'Pihak', 'Status'],
            $previewRows
        );

        if ($dryRun) {
            $this->info('Dry run selesai. Belum ada data yang disimpan.');
            $this->line('Kalau datanya sudah benar, jalankan:');
            $this->line('php artisan wedding:import-sheet-documents --mark-resolved');
        } else {
            $this->info('Import dokumen ke checklist selesai.');
        }

        return self::SUCCESS;
    }
}
