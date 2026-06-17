<?php

namespace App\Services\WeddingSync;

use App\Models\BudgetItem;
use App\Models\ChecklistItem;
use App\Models\Guest;
use App\Models\SyncDifference;
use App\Models\WeddingEvent;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class WebToStagingSheetExporter
{
    public function __construct(
        private GoogleSheetsClient $sheets,
        private SyncHasher $hasher
    ) {
    }

    public function export(SyncDifference $difference, ?int $adminId = null): Model
    {
        if ($difference->status !== 'pending') {
            throw new RuntimeException('Data ini sudah tidak berstatus pending.');
        }

        if (! in_array($difference->difference_type, ['web_only', 'different'], true)) {
            throw new RuntimeException('Hanya data web_only atau different yang bisa di-export ke staging sheet.');
        }

        $record = $this->recordFromDifference($difference);

        [$sheetName, $headers, $keyHeader, $keyValue, $row] = match ($difference->module) {
            'events' => $this->eventRow($record),
            'guests' => $this->guestRow($record),
            'budget_items' => $this->budgetRow($record),
            'checklist_items' => $this->checklistRow($record),
            default => throw new RuntimeException('Module belum didukung: ' . $difference->module),
        };

        $this->sheets->ensureSheetWithHeaders($sheetName, $headers);

        $alreadyExists = $this->sheets->rowExistsByKey($sheetName, $keyHeader, $keyValue);
        $sheetWriteAction = 'appended';

        if ($alreadyExists) {
            $updated = $this->sheets->updateRowByKey($sheetName, $keyHeader, $keyValue, $row, $headers);

            if (!$updated) {
                throw new RuntimeException("Row {$keyValue} sudah terdeteksi ada, tetapi gagal di-update di sheet {$sheetName}.");
            }

            $sheetWriteAction = 'updated';
        } else {
            $this->sheets->appendRow($sheetName, $row);
        }

        $record->update([
            'sync_source' => 'web_exported',
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => $this->appendUniqueNote($record->sync_note ?? null, "Staged to {$sheetName}."),
        ]);

        $difference->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $adminId,
            'sheet_name' => $sheetName,
            'sheet_payload' => [
                'sheet_name' => $sheetName,
                'key_header' => $keyHeader,
                'key_value' => $keyValue,
                'row' => $row,
                'already_exists' => $alreadyExists,
                'sheet_write_action' => $sheetWriteAction,
                'exported_at' => now()->toDateTimeString(),
            ],
            'note' => trim(
                ($difference->note ?? '') .
                "\nStaged Web → {$sheetName}. " .
                ($sheetWriteAction === 'updated' ? 'Existing row updated.' : 'New row appended.')
            ),
        ]);

        return $record;
    }


    private function appendUniqueNote(?string $currentNote, string $newNote): string
    {
        $currentNote = trim((string) $currentNote);
        $newNote = trim($newNote);

        if ($currentNote === '') {
            return $newNote;
        }

        if (str_contains($currentNote, $newNote)) {
            return $currentNote;
        }

        return $currentNote . "\n" . $newNote;
    }


    private function isDocumentChecklist($record, SyncDifference $difference): bool
    {
        if ($difference->sheet_name === 'INPUT_DOKUMEN') {
            return true;
        }

        $payload = is_array($difference->web_payload)
            ? $difference->web_payload
            : json_decode($difference->web_payload ?? '{}', true);

        $category = strtolower(trim((string) (
            $payload['category']
            ?? $record->category
            ?? ''
        )));

        return in_array($category, ['dokumen', 'dokumen nikah'], true);
    }

    private function recordFromDifference(SyncDifference $difference): Model
    {
        $modelClass = $difference->web_model;
        $webId = $difference->web_id;

        if (!$modelClass || !$webId || !class_exists($modelClass)) {
            throw new RuntimeException('web_model atau web_id tidak valid.');
        }

        $record = $modelClass::query()->find($webId);

        if (!$record) {
            throw new RuntimeException('Data web tidak ditemukan. Web ID: ' . $webId);
        }

        return $record;
    }

    private function eventRow(Model $record): array
    {
        /** @var WeddingEvent $record */
        $sheetName = 'WEB_EXPORT_EVENT';

        $headers = [
            'event_key',
            'event_name',
            'pihak',
            'tanggal',
            'venue',
            'active',
            'sync_action',
            'export_note',
        ];

        $eventKey = $record->sheet_key ?: $this->eventKey($record);

        $record->update([
            'sheet_key' => $eventKey,
            'web_hash' => $this->hasher->make([
                'event_key' => $eventKey,
                'event_name' => $record->event_name,
                'event_side' => $record->event_side,
                'event_date' => $this->dateValue($record->event_date),
                'venue_name' => $record->venue_name,
            ]),
        ]);

        return [
            $sheetName,
            $headers,
            'event_key',
            $eventKey,
            [
                $eventKey,
                $record->event_name,
                strtoupper((string) $record->event_side),
                $this->dateValue($record->event_date),
                $record->venue_name,
                'YES',
                'UPSERT',
                'Staged from Web. Review before moving to MASTER_EVENT.',
            ],
        ];
    }

    private function guestRow(Model $record): array
    {
        /** @var Guest $record */
        $record->loadMissing('weddingEvent');

        $side = $this->sideFromEvent($record->weddingEvent);
        $sheetName = $side === 'cpp' ? 'WEB_EXPORT_TAMU_CPP' : 'WEB_EXPORT_TAMU_CPW';

        $headers = [
            'guest_key',
            'nama_tamu',
            'nomor_wa',
            'grup',
            'alamat',
            'event_key',
            'max_invited',
            'rsvp_status',
            'rsvp_count',
            'invitation_status',
            'attendance_status',
            'actual_attendance_count',
            'envelope_amount',
            'souvenir_status',
            'souvenir_count',
            'catatan',
            'is_active',
            'sync_action',
            'export_note',
        ];

        $guestKey = $record->sheet_key ?: $this->makeGuestKey($record, $side);
        $eventKey = strtoupper($record->weddingEvent?->sheet_key ?: $record->weddingEvent?->event_side ?: $side);

        $invitationStatus = $record->invitation_sent_at ? 'sent' : 'pending';

        $record->update([
            'sheet_key' => $guestKey,
            'web_hash' => $this->hasher->make([
                'guest_key' => $guestKey,
                'name' => $record->name,
                'phone' => $this->normalizePhone($record->phone),
                'group_name' => $record->group_name,
                'address' => $record->address,
                'event_key' => $eventKey,
                'total_invited' => (int) ($record->total_invited ?: 1),
                'rsvp_status' => $record->rsvp_status ?: 'pending',
                'rsvp_count' => (int) ($record->rsvp_count ?? 0),
                'invitation_status' => $invitationStatus,
                'attendance_status' => $record->attendance_status ?: 'not_arrived',
                'actual_attendance_count' => (int) ($record->actual_attendance_count ?? 0),
                'envelope_amount' => (int) ($record->envelope_amount ?? 0),
                'souvenir_status' => $record->souvenir_status ?: 'not_given',
                'souvenir_count' => (int) ($record->souvenir_count ?? 0),
                'sync_note' => $record->sync_note,
            ]),
        ]);

        return [
            $sheetName,
            $headers,
            'guest_key',
            $guestKey,
            [
                $guestKey,
                $record->name,
                $this->normalizePhone($record->phone),
                $record->group_name,
                $record->address,
                $eventKey,
                (int) ($record->total_invited ?: 1),
                $this->rsvpStatusToSheet($record->rsvp_status),
                (int) ($record->rsvp_count ?? 0),
                $this->invitationStatusToSheet($invitationStatus),
                $this->attendanceStatusToSheet($record->attendance_status),
                (int) ($record->actual_attendance_count ?? 0),
                (int) ($record->envelope_amount ?? 0),
                $this->souvenirStatusToSheet($record->souvenir_status),
                (int) ($record->souvenir_count ?? 0),
                $record->sync_note ?: 'Staged from Web.',
                'YES',
                'UPSERT',
                'Review before moving to INPUT_TAMU_CPW / INPUT_TAMU_CPP.',
            ],
        ];
    }

    private function budgetRow(Model $record): array
    {
        /** @var BudgetItem $record */
        $record->loadMissing('weddingEvent');

        $sheetName = 'WEB_EXPORT_BUDGET';

        $headers = [
            'budget_key',
            'pihak',
            'kategori',
            'item',
            'vendor',
            'estimasi',
            'aktual',
            'status_bayar',
            'deadline_bayar',
            'tanggal_bayar',
            'catatan',
            'sync_action',
            'export_note',
        ];

        $side = strtoupper($record->weddingEvent?->sheet_key ?: $record->weddingEvent?->event_side ?: 'BOTH');
        $budgetKey = $record->sheet_key ?: $this->makeBudgetKey($record, strtolower($side));

        $record->update([
            'sheet_key' => $budgetKey,
            'web_hash' => $this->hasher->make([
                'budget_key' => $budgetKey,
                'side' => $side,
                'category' => $record->category,
                'item_name' => $record->item_name,
                'estimated_amount' => $record->estimated_amount,
                'actual_amount' => $record->actual_amount,
            ]),
        ]);

        return [
            $sheetName,
            $headers,
            'budget_key',
            $budgetKey,
            [
                $budgetKey,
                $side,
                $record->category,
                $record->item_name,
                null,
                (int) $record->estimated_amount,
                (int) $record->actual_amount,
                $this->paymentStatusToSheet($record->payment_status),
                null,
                null,
                $record->sync_note ?: $record->note ?: 'Staged from Web.',
                'UPSERT',
                'Review before moving to INPUT_BUDGET.',
            ],
        ];
    }

    private function checklistRow(Model $record): array
    {
        /** @var ChecklistItem $record */
        $record->loadMissing('weddingEvent');

        $category = strtolower(trim((string) $record->category));

        if (in_array($category, ['dokumen', 'dokumen nikah'], true)) {
            return $this->documentRow($record);
        }

        $sheetName = 'WEB_EXPORT_PERSIAPAN';

        $headers = [
            'task_key',
            'pihak',
            'kategori',
            'tugas',
            'pic',
            'deadline',
            'prioritas',
            'status',
            'catatan',
            'sync_action',
            'export_note',
        ];

        $side = strtoupper($record->weddingEvent?->sheet_key ?: $record->weddingEvent?->event_side ?: $record->assigned_to ?: 'BOTH');
        $taskKey = $record->sheet_key ?: $this->makeTaskKey($record, strtolower($side));

        $record->update([
            'sheet_key' => $taskKey,
            'web_hash' => $this->hasher->make([
                'task_key' => $taskKey,
                'side' => $side,
                'title' => $record->title,
                'category' => $record->category,
                'status' => $record->status,
            ]),
        ]);

        return [
            $sheetName,
            $headers,
            'task_key',
            $taskKey,
            [
                $taskKey,
                $side,
                $record->category,
                $record->title,
                $record->assigned_to,
                $this->dateValue($record->due_date),
                $record->priority ?: 'Wajib',
                $this->checklistStatusToSheet($record->status),
                $record->sync_note ?: $record->note ?: 'Staged from Web.',
                'UPSERT',
                'Review before moving to INPUT_PERSIAPAN / INPUT_DOKUMEN.',
            ],
        ];
    }

    private function documentRow(ChecklistItem $record): array
    {
        $sheetName = 'WEB_EXPORT_DOKUMEN';

        $headers = [
            'doc_key',
            'pihak',
            'kategori',
            'dokumen',
            'detail',
            'instansi',
            'deadline',
            'status',
            'catatan',
            'sync_action',
            'export_note',
        ];

        $side = strtoupper($record->assigned_to ?: 'BOTH');
        $docKey = $record->sheet_key ?: $this->makeDocumentKey($record, strtolower($side));

        $record->update([
            'sheet_key' => $docKey,
            'web_hash' => $this->hasher->make([
                'doc_key' => $docKey,
                'side' => $side,
                'title' => $record->title,
                'status' => $record->status,
            ]),
        ]);

        return [
            $sheetName,
            $headers,
            'doc_key',
            $docKey,
            [
                $docKey,
                $side,
                'Dokumen',
                $record->title,
                $record->note,
                null,
                $this->dateValue($record->due_date),
                $this->checklistStatusToSheet($record->status),
                $record->sync_note ?: $record->note ?: 'Staged from Web.',
                'UPSERT',
                'Review before moving to INPUT_DOKUMEN.',
            ],
        ];
    }

    private function eventKey(WeddingEvent $event): string
    {
        return match (strtolower((string) $event->event_side)) {
            'cpp' => 'CPP',
            'cpw' => 'CPW',
            default => 'BOTH',
        };
    }

    private function makeGuestKey(Guest $guest, string $side): string
    {
        return strtoupper($side ?: 'both') . '-GST-WEB-' . str_pad((string) $guest->id, 4, '0', STR_PAD_LEFT);
    }

    private function makeBudgetKey(BudgetItem $budget, string $side): string
    {
        return strtoupper($side ?: 'both') . '-BDG-WEB-' . str_pad((string) $budget->id, 4, '0', STR_PAD_LEFT);
    }

    private function makeTaskKey(ChecklistItem $task, string $side): string
    {
        return strtoupper($side ?: 'both') . '-TSK-WEB-' . str_pad((string) $task->id, 4, '0', STR_PAD_LEFT);
    }

    private function makeDocumentKey(ChecklistItem $task, string $side): string
    {
        return strtoupper($side ?: 'both') . '-DOC-WEB-' . str_pad((string) $task->id, 4, '0', STR_PAD_LEFT);
    }

    private function sideFromEvent(?WeddingEvent $event): string
    {
        $value = strtolower((string) ($event?->event_side ?: $event?->sheet_key));

        if (str_contains($value, 'cpp') || str_contains($value, 'pria')) {
            return 'cpp';
        }

        if (str_contains($value, 'cpw') || str_contains($value, 'wanita')) {
            return 'cpw';
        }

        return 'cpw';
    }

    private function normalizePhone(mixed $value): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $value);

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }

    private function rsvpStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return match ($value) {
            'attend' => 'Hadir',
            'not_attend' => 'Tidak Hadir',
            default => 'Belum Konfirmasi',
        };
    }

    private function invitationStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return $value === 'sent' ? 'Terkirim' : 'Belum Dikirim';
    }

    private function attendanceStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return $value === 'arrived' ? 'Hadir' : 'Belum Hadir';
    }

    private function souvenirStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return $value === 'given' ? 'Ya' : 'Tidak';
    }

    private function paymentStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if ($value === 'paid' || str_contains($value, 'lunas')) {
            return 'Lunas';
        }

        if ($value === 'partial' || str_contains($value, 'dp')) {
            return 'DP';
        }

        return 'Belum Lunas';
    }

    private function checklistStatusToSheet(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if ($value === 'done' || str_contains($value, 'selesai')) {
            return 'Selesai';
        }

        if ($value === 'in_progress' || str_contains($value, 'proses')) {
            return 'Proses';
        }

        return 'Belum';
    }

    private function dateValue(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }
}
