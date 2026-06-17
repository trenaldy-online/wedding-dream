<?php

namespace App\Services\WeddingSync;

use App\Models\BudgetItem;
use App\Models\ChecklistItem;
use App\Models\Guest;
use App\Models\WeddingEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SyncPayloadBuilder
{
    public function sheetPayloads(string $module, string $sheetName, array $rows): array
    {
        $payloads = [];

        foreach ($rows as $row) {
            $payload = match ($module) {
                'events' => $this->sheetEventPayload($row),
                'guests' => $this->sheetGuestPayload($row),
                'budget_items' => $this->sheetBudgetPayload($row),
                'checklist_items' => $this->sheetChecklistPayload($row),
                default => null,
            };

            if (!$payload) {
                continue;
            }

            if ($this->shouldSkipSheetPayload($payload)) {
                continue;
            }

            $payload['_sheet_name'] = $sheetName;
            $payload['_sheet_row'] = $row['_sheet_row'] ?? null;

            $payloads[] = $payload;
        }

        return $payloads;
    }

    public function webPayloads(string $module, ?string $sheetName = null): Collection
    {
        return match ($module) {
            'events' => WeddingEvent::query()
                ->get()
                ->map(fn (WeddingEvent $event) => $this->webEventPayload($event)),

            'guests' => $this->webGuestPayloads($sheetName),

            'budget_items' => BudgetItem::query()
                ->with('weddingEvent')
                ->get()
                ->map(fn (BudgetItem $budget) => $this->webBudgetPayload($budget)),

            'checklist_items' => $this->webChecklistPayloads($sheetName),

            default => collect(),
        };
    }

    public function compareFields(string $module): array
    {
        return match ($module) {
            'events' => [
                'record_key',
                'event_name',
                'event_side',
                'event_date',
                'venue_name',
                'is_active',
            ],

            'guests' => [
                'record_key',
                'name',
                'phone',
                'group_name',
                'address',
                'event_key',
                'total_invited',
                'rsvp_status',
                'rsvp_count',
                'invitation_status',
                'attendance_status',
                'actual_attendance_count',
                'envelope_amount',
                'souvenir_status',
                'souvenir_count',
                'is_active',
                'sync_note',
            ],

            'budget_items' => [
                'record_key',
                'event_side',
                'category',
                'item_name',
                'estimated_amount',
                'actual_amount',
                'payment_status',
                'sync_note',
            ],

            'checklist_items' => [
                'record_key',
                'category',
                'title',
                'assigned_to',
                'due_date',
                'priority',
                'status',
                'sync_note',
            ],

            default => [],
        };
    }

    private function sheetEventPayload(array $row): ?array
    {
        $key = $this->clean($row['event_key'] ?? null);
        $name = $this->clean($row['event_name'] ?? null);

        if (!$key && !$name) {
            return null;
        }

        return [
            'module' => 'events',
            'record_key' => $key,
            'web_id' => $this->clean($row['website_event_id'] ?? null),
            'event_name' => $name,
            'event_side' => strtoupper($this->clean($row['pihak'] ?? null)),
            'event_date' => $this->normalizeDate($row['tanggal'] ?? null),
            'venue_name' => $this->clean($row['venue'] ?? null),
            'is_active' => $this->normalizeYesNo($row['active'] ?? 'YES'),
            'sync_action' => strtoupper($this->clean($row['sync_action'] ?? 'UPSERT')),
        ];
    }

    private function sheetGuestPayload(array $row): ?array
    {
        $guestKey = $this->clean($row['guest_key'] ?? null);
        $sourceWebKey = $this->clean($row['source_web_key'] ?? null);
        $key = $this->pickRecordKey($row, ['source_web_key', 'guest_key']);
        $name = $this->clean($row['nama_tamu'] ?? null);

        if (!$key && !$name) {
            return null;
        }

        $legacy = $this->extractLegacyGuestNote($row['catatan'] ?? null);

        $rsvpStatus = $this->normalizeRsvpStatus(
            $row['rsvp_status']
            ?? $row['rsvp']
            ?? $legacy['rsvp_status']
            ?? null
        );

        $invitationStatus = $this->normalizeInvitationStatus(
            $row['invitation_status']
            ?? $row['status_undangan']
            ?? $legacy['invitation_status']
            ?? null
        );

        $attendanceStatus = $this->normalizeAttendanceStatus(
            $row['attendance_status']
            ?? $row['kehadiran']
            ?? $legacy['attendance_status']
            ?? null
        );

        $souvenirStatus = $this->normalizeSouvenirStatus(
            $row['souvenir_status']
            ?? $row['souvenir']
            ?? $legacy['souvenir_status']
            ?? null
        );

        return [
            'module' => 'guests',
            'record_key' => $key,
            'guest_key' => $guestKey,
            'source_web_key' => $sourceWebKey,
            'origin' => $sourceWebKey ? 'web' : 'sheet',
            'name' => $name,
            'phone' => $this->normalizePhone($row['nomor_wa'] ?? $row['kontak'] ?? null),
            'group_name' => $this->clean($row['grup'] ?? $row['kelompok'] ?? null),
            'address' => $this->clean($row['alamat'] ?? null),
            'event_key' => strtoupper($this->clean($row['event_key'] ?? $row['pihak'] ?? null)),
            'total_invited' => $this->normalizeInteger(
                $row['max_invited']
                ?? $row['jumlah_undangan']
                ?? null,
                1
            ),
            'rsvp_status' => $rsvpStatus,
            'rsvp_count' => $this->normalizeInteger(
                $row['rsvp_count']
                ?? $row['estimasi_hadir']
                ?? $row['jumlah_rsvp']
                ?? null,
                0
            ),
            'invitation_status' => $invitationStatus,
            'attendance_status' => $attendanceStatus,
            'actual_attendance_count' => $this->normalizeInteger(
                $row['actual_attendance_count']
                ?? $row['jumlah_hadir_aktual']
                ?? $row['jumlah_hadir']
                ?? null,
                0
            ),
            'envelope_amount' => $this->normalizeAmount(
                $row['envelope_amount']
                ?? $row['nominal_amplop']
                ?? $legacy['envelope_amount']
                ?? null
            ),
            'souvenir_status' => $souvenirStatus,
            'souvenir_count' => $this->normalizeInteger(
                $row['souvenir_count']
                ?? $row['jumlah_souvenir']
                ?? null,
                $souvenirStatus === 'given' ? 1 : 0
            ),
            'is_active' => $this->normalizeYesNo($row['is_active'] ?? 'YES'),
            'sync_note' => $this->clean(
                $row['catatan_manual']
                ?? $row['note']
                ?? $legacy['sync_note']
                ?? $row['catatan']
                ?? null
            ),
            'sheet_updated_at' => $this->normalizeDateTime($row['sheet_updated_at'] ?? null),
            'sync_action' => strtoupper($this->clean($row['sync_action'] ?? 'UPSERT')),
        ];
    }

    private function sheetBudgetPayload(array $row): ?array
    {
        $blankLike = function ($value): string {
            $value = trim((string) ($value ?? ''));

            $invalidTokens = [
                '#N/A',
                '#VALUE!',
                '#REF!',
                '#ERROR!',
                '#DIV/0!',
                'N/A',
                'NULL',
                '-',
            ];

            if ($value === '') {
                return '';
            }

            if (in_array(strtoupper($value), $invalidTokens, true)) {
                return '';
            }

            return $value;
        };

        $key = $blankLike($this->pickRecordKey($row, ['source_web_key', 'budget_key']));

        $category = $blankLike($row['kategori'] ?? $row['category'] ?? null);
        $itemName = $blankLike($row['item'] ?? $row['item_name'] ?? $row['nama_item'] ?? null);
        $vendor = $blankLike($row['vendor'] ?? null);
        $eventSide = strtoupper($blankLike($row['pihak'] ?? $row['event_side'] ?? $row['side'] ?? null));
        $syncNote = $blankLike($row['catatan'] ?? $row['sync_note'] ?? null);
        $syncAction = strtoupper($blankLike($row['sync_action'] ?? 'UPSERT'));

        $estimatedAmount = $this->normalizeAmount($row['estimasi'] ?? $row['estimated_amount'] ?? null);
        $actualAmount = $this->normalizeAmount($row['aktual'] ?? $row['actual_amount'] ?? null);

        /*
         * Guard utama:
         * INPUT_BUDGET bisa menghasilkan row ghost dari formula.
         * Kunci saja tidak cukup untuk dianggap data valid.
         * Data budget valid minimal harus punya salah satu:
         * category / item / vendor / estimated_amount > 0 / actual_amount > 0.
         */
        $hasCoreBudgetValue =
            $category !== ''
            || $itemName !== ''
            || $vendor !== ''
            || ((float) $estimatedAmount > 0)
            || ((float) $actualAmount > 0);

        if (!$hasCoreBudgetValue) {
            return null;
        }

        /*
         * Kalau row punya isi bisnis tapi belum punya key, tetap boleh dibaca
         * supaya item manual dari sheet bisa menjadi sheet_only.
         */
        $paymentStatus = $this->normalizePaymentStatus($row['status_bayar'] ?? $row['status'] ?? $row['payment_status'] ?? null);

        return [
            'module' => 'budget_items',
            'record_key' => $key,
            'event_side' => $eventSide,
            'category' => $category ?: null,
            'item_name' => $itemName ?: null,
            'vendor' => $vendor ?: null,
            'estimated_amount' => $estimatedAmount,
            'actual_amount' => $actualAmount,
            'payment_status' => $paymentStatus,
            'tanggal_bayar' => $this->normalizeDate($row['tanggal_bayar'] ?? null),
            'deadline_bayar' => $this->normalizeDate($row['deadline_bayar'] ?? null),
            'sync_note' => $syncNote ?: null,
            'sync_action' => $syncAction ?: 'UPSERT',
        ];
    }

    private function sheetChecklistPayload(array $row): ?array
    {
        /*
         * Fungsi ini dipakai untuk:
         * 1. INPUT_PERSIAPAN
         * 2. INPUT_DOKUMEN
         *
         * INPUT_DOKUMEN tetap masuk ke tabel checklist_items,
         * tetapi kategori utamanya dinormalisasi menjadi "Dokumen".
         */

        $isDocumentRow = isset($row['doc_key'])
            || isset($row['dokumen'])
            || isset($row['nama_dokumen'])
            || isset($row['document_name']);

        $key = $this->pickRecordKey($row, ['source_web_key', 'task_key', 'doc_key', 'checklist_key']);

        $title = $this->clean(
            $row['tugas']
            ?? $row['dokumen']
            ?? $row['nama_dokumen']
            ?? $row['document_name']
            ?? $row['nama_file']
            ?? null
        );

        if (!$key && !$title) {
            return null;
        }

        $documentCategory = $this->clean($row['kategori'] ?? null);
        $documentDetail = $this->clean($row['detail'] ?? null);
        $documentInstitution = $this->clean($row['instansi'] ?? null);
        $manualNote = $this->clean(
            $row['catatan']
            ?? $row['keterangan']
            ?? $row['note']
            ?? null
        );

        if ($isDocumentRow) {
            $category = 'Dokumen';

            $noteParts = [];

            if ($documentCategory) {
                $noteParts[] = 'Jenis dokumen: ' . $documentCategory;
            }

            if ($documentDetail) {
                $noteParts[] = 'Detail: ' . $documentDetail;
            }

            if ($documentInstitution) {
                $noteParts[] = 'Instansi: ' . $documentInstitution;
            }

            if ($manualNote) {
                $noteParts[] = 'Catatan: ' . $manualNote;
            }

            $syncNote = implode(' | ', $noteParts);
        } else {
            $category = $documentCategory;
            $syncNote = $manualNote;
        }

        $assignedTo = $this->normalizeAssignedTo(
            $row['pic']
            ?? $row['pihak']
            ?? $row['penanggung_jawab']
            ?? $row['responsible']
            ?? null
        );

        return [
            'module' => 'checklist_items',
            'record_key' => $key,
            'source_web_key' => $this->clean($row['source_web_key'] ?? null),
            'event_side' => $assignedTo,
            'category' => $category,
            'title' => $title,
            'assigned_to' => $assignedTo,
            'due_date' => $this->normalizeDate(
                $row['deadline']
                ?? $row['target_tanggal']
                ?? $row['tanggal_target']
                ?? null
            ),
            'priority' => $this->clean(
                $row['prioritas']
                ?? $row['priority']
                ?? null
            ),
            'status' => $this->normalizeChecklistStatus(
                $row['status']
                ?? $row['status_dokumen']
                ?? $row['progress']
                ?? null
            ),
            'sync_note' => $syncNote ?: null,
            'sync_action' => strtoupper($this->clean($row['sync_action'] ?? 'UPSERT')),
        ];
    }

    private function webGuestPayloads(?string $sheetName = null): Collection
    {
        $query = Guest::query()
            ->with('weddingEvent');

        /*
         * Penting:
         * INPUT_TAMU_CPW hanya dibandingkan dengan tamu CPW.
         * INPUT_TAMU_CPP hanya dibandingkan dengan tamu CPP.
         * Kalau tidak difilter, tamu CPW bisa dianggap web_only di sheet CPP,
         * lalu saat export staging akan muncul ganda.
         */
        if ($sheetName === 'INPUT_TAMU_CPW') {
            $query->whereHas('weddingEvent', function ($q) {
                $q->where('event_side', 'cpw')
                    ->orWhere('sheet_key', 'CPW');
            });
        }

        if ($sheetName === 'INPUT_TAMU_CPP') {
            $query->whereHas('weddingEvent', function ($q) {
                $q->where('event_side', 'cpp')
                    ->orWhere('sheet_key', 'CPP');
            });
        }

        return $query
            ->get()
            ->map(fn (Guest $guest) => $this->webGuestPayload($guest));
    }

private function webEventPayload(WeddingEvent $event): array
    {
        return [
            'module' => 'events',
            'web_model' => WeddingEvent::class,
            'web_id' => $event->id,
            'record_key' => $event->sheet_key ?: $event->event_side,
            'event_name' => $this->clean($event->event_name),
            'event_side' => strtoupper($this->clean($event->event_side)),
            'event_date' => optional($event->event_date)->format('Y-m-d'),
            'venue_name' => $this->clean($event->venue_name),
            'is_active' => 'YES',
            'is_dummy' => (bool) ($event->is_dummy ?? false),
            'sync_source' => $this->clean($event->sync_source ?? null),
            'sync_note' => $this->clean($event->sync_note ?? null),
        ];
    }

    private function webGuestPayload(Guest $guest): array
    {
        $invitationStatus = $guest->invitation_sent_at ? 'sent' : 'pending';

        return [
            'module' => 'guests',
            'web_model' => Guest::class,
            'web_id' => $guest->id,
            'record_key' => $guest->sheet_key,
            'name' => $this->clean($guest->name),
            'phone' => $this->normalizePhone($guest->phone),
            'group_name' => $this->clean($guest->group_name),
            'address' => $this->clean($guest->address),
            'event_key' => strtoupper($this->clean(
                $guest->weddingEvent?->sheet_key ?: $guest->weddingEvent?->event_side
            )),
            'total_invited' => $this->normalizeInteger($guest->total_invited ?? 1, 1),
            'rsvp_status' => $this->normalizeRsvpStatus($guest->rsvp_status ?? null),
            'rsvp_count' => $this->normalizeInteger($guest->rsvp_count ?? 0, 0),
            'invitation_status' => $invitationStatus,
            'attendance_status' => $this->normalizeAttendanceStatus($guest->attendance_status ?? null),
            'actual_attendance_count' => $this->normalizeInteger($guest->actual_attendance_count ?? 0, 0),
            'envelope_amount' => $this->normalizeAmount($guest->envelope_amount ?? 0),
            'souvenir_status' => $this->normalizeSouvenirStatus($guest->souvenir_status ?? null),
            'souvenir_count' => $this->normalizeInteger($guest->souvenir_count ?? 0, 0),
            'is_active' => 'YES',
            'is_dummy' => (bool) ($guest->is_dummy ?? false),
            'sync_source' => $this->clean($guest->sync_source ?? null),
            'sync_note' => $this->clean($guest->sync_note ?? null),
            'sheet_updated_at' => optional($guest->sheet_updated_at)->format('Y-m-d H:i:s'),
            'web_updated_at' => optional($guest->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    private function webBudgetPayload(BudgetItem $budget): array
    {
        return [
            'module' => 'budget_items',
            'web_model' => BudgetItem::class,
            'web_id' => $budget->id,
            'record_key' => $budget->sheet_key,
            'event_side' => strtoupper($this->clean(
                $budget->weddingEvent?->sheet_key ?: $budget->weddingEvent?->event_side
            )),
            'category' => $this->clean($budget->category),
            'item_name' => $this->clean($budget->item_name),
            'estimated_amount' => $this->normalizeAmount($budget->estimated_amount ?? null),
            'actual_amount' => $this->normalizeAmount($budget->actual_amount ?? null),
            'payment_status' => $this->normalizePaymentStatus($budget->payment_status ?? null),
            'sync_note' => $this->clean($budget->sync_note ?? $budget->note),
            'is_dummy' => (bool) ($budget->is_dummy ?? false),
            'sync_source' => $this->clean($budget->sync_source ?? null),
        ];
    }

    private function webChecklistPayloads(?string $sheetName = null): Collection
    {
        $query = ChecklistItem::query()
            ->with('weddingEvent');

        if ($sheetName === 'INPUT_DOKUMEN') {
            $query->whereIn('category', ['Dokumen', 'Dokumen Nikah']);
        } elseif ($sheetName === 'INPUT_PERSIAPAN') {
            $query->where(function ($q) {
                $q->whereNull('category')
                    ->orWhereNotIn('category', ['Dokumen', 'Dokumen Nikah']);
            });
        }

        return $query->get()
            ->map(fn (ChecklistItem $task) => $this->webChecklistPayload($task));
    }

    private function webChecklistPayload(ChecklistItem $task): array
    {
        $rawCategory = $this->clean($task->category);
        $isDocumentTask = in_array(strtolower(trim((string) $rawCategory)), ['dokumen', 'dokumen nikah'], true);

        $assignedTo = $this->normalizeAssignedTo($task->assigned_to);

        $eventSide = $this->clean(
            $task->weddingEvent?->sheet_key ?: $task->weddingEvent?->event_side
        );

        if ($eventSide === '') {
            $eventSide = $this->inferEventSideFromRecordKey($task->sheet_key);
        }

        if ($eventSide === '') {
            $eventSide = $assignedTo;
        }

        return [
            'module' => 'checklist_items',
            'web_model' => ChecklistItem::class,
            'web_id' => $task->id,
            'record_key' => $task->sheet_key,
            'event_side' => $eventSide,
            'category' => $this->normalizeChecklistCategory($rawCategory, $isDocumentTask),
            'title' => $this->clean($task->title),
            'assigned_to' => $assignedTo,
            'due_date' => optional($task->due_date)->format('Y-m-d'),
            'priority' => $isDocumentTask
                ? null
                : ($this->clean($task->priority ?? null) ?: 'Wajib'),
            'status' => $this->normalizeChecklistStatus($task->status),
            'sync_note' => $this->clean($task->sync_note ?? $task->note),
            'is_dummy' => (bool) ($task->is_dummy ?? false),
            'sync_source' => $this->clean($task->sync_source ?? null),
        ];
    }

    private function inferEventSideFromRecordKey(?string $key): string
    {
        $key = strtoupper(trim((string) $key));

        if (preg_match('/^(CPP|CPW|BOTH)-/', $key, $matches)) {
            return strtolower($matches[1]);
        }

        return '';
    }

    private function normalizeChecklistCategory(?string $value, bool $isDocumentRow = false): string
    {
        $value = trim((string) $value);
        $lower = strtolower($value);

        if ($isDocumentRow || in_array($lower, ['dokumen', 'dokumen nikah'], true)) {
            return 'Dokumen';
        }

        return $value;
    }

private function normalizeAssignedTo(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return 'both';
        }

        if (str_contains($value, 'cpp')
            || str_contains($value, 'pria')
            || str_contains($value, 'cowo')
            || str_contains($value, 'calon pengantin pria')) {
            return 'cpp';
        }

        if (str_contains($value, 'cpw')
            || str_contains($value, 'wanita')
            || str_contains($value, 'cewe')
            || str_contains($value, 'calon pengantin wanita')) {
            return 'cpw';
        }

        if (str_contains($value, 'bersama')
            || str_contains($value, 'berdua')
            || str_contains($value, 'both')) {
            return 'both';
        }

        return 'both';
    }

    private function normalizeChecklistStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return 'todo';
        }

        if (str_contains($value, 'selesai')
            || str_contains($value, 'done')
            || str_contains($value, 'complete')) {
            return 'done';
        }

        if (str_contains($value, 'proses')
            || str_contains($value, 'progress')
            || str_contains($value, 'jalan')) {
            return 'in_progress';
        }

        return 'todo';
    }

    private function extractLegacyGuestNote(mixed $value): array
    {
        $text = $this->clean($value);

        $result = [
            'rsvp_status' => null,
            'invitation_status' => null,
            'attendance_status' => null,
            'envelope_amount' => null,
            'souvenir_status' => null,
            'sync_note' => $text,
        ];

        if (!$text || !str_contains($text, ':')) {
            return $result;
        }

        $parts = preg_split('/\\s*\\|\\s*/', $text) ?: [];
        $foundStructuredPart = false;

        foreach ($parts as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            if (preg_match('/^RSVP\\s+lama\\s*:\\s*(.*)$/iu', $part, $matches)) {
                $result['rsvp_status'] = $this->normalizeRsvpStatus($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }

            if (preg_match('/^Status\\s+Undangan\\s*:\\s*(.*)$/iu', $part, $matches)) {
                $result['invitation_status'] = $this->normalizeInvitationStatus($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }

            if (preg_match('/^Kehadiran\\s*:\\s*(.*)$/iu', $part, $matches)) {
                $result['attendance_status'] = $this->normalizeAttendanceStatus($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }

            if (preg_match('/^Nominal\\s+amplop\\s*:\\s*(.*)$/iu', $part, $matches)) {
                $result['envelope_amount'] = $this->normalizeAmount($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }

            if (preg_match('/^Souvenir\\s*:\\s*(.*)$/iu', $part, $matches)) {
                $result['souvenir_status'] = $this->normalizeSouvenirStatus($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }

            if (preg_match('/^Catatan\\s*:\\s*(.*)$/isu', $part, $matches)) {
                $result['sync_note'] = $this->clean($matches[1] ?? null);
                $foundStructuredPart = true;
                continue;
            }
        }

        if (!$foundStructuredPart) {
            $result['sync_note'] = $text;
        }

        return $result;
    }

    private function normalizeRsvpStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\\s+/', ' ', $value);

        if ($value === '') {
            return 'pending';
        }

        if (in_array($value, ['attend', 'hadir', 'ya', 'yes', 'confirmed', 'konfirmasi hadir'], true)) {
            return 'attend';
        }

        if (str_contains($value, 'tidak') || str_contains($value, 'not attend') || str_contains($value, 'decline')) {
            return 'not_attend';
        }

        if (str_contains($value, 'belum') || str_contains($value, 'pending') || str_contains($value, 'konfirmasi')) {
            return 'pending';
        }

        return $value;
    }

    private function normalizeInvitationStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        if ($value === '') {
            return 'pending';
        }

        /*
         * Penting:
         * "Belum Dikirim" mengandung kata "dikirim",
         * jadi kondisi "belum" harus dicek lebih dulu.
         */
        if (str_contains($value, 'belum')
            || str_contains($value, 'pending')
            || str_contains($value, 'not sent')
            || str_contains($value, 'belum terkirim')) {
            return 'pending';
        }

        if (str_contains($value, 'terkirim')
            || str_contains($value, 'sudah dikirim')
            || str_contains($value, 'sudah terkirim')
            || str_contains($value, 'sent')) {
            return 'sent';
        }

        return 'pending';
    }

    private function normalizeAttendanceStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\\s+/', ' ', $value);

        if ($value === '') {
            return 'not_arrived';
        }

        if ((str_contains($value, 'hadir') || str_contains($value, 'arrived') || str_contains($value, 'check in'))
            && !str_contains($value, 'belum')
            && !str_contains($value, 'tidak')) {
            return 'arrived';
        }

        return 'not_arrived';
    }

    private function normalizeSouvenirStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\\s+/', ' ', $value);

        if ($value === '') {
            return 'not_given';
        }

        if (in_array($value, ['ya', 'yes', 'given', 'diberikan', 'sudah diberikan', 'sudah'], true)) {
            return 'given';
        }

        return 'not_given';
    }

    private function normalizeDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::createFromDate(1899, 12, 30)
                    ->addDays((int) $value)
                    ->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return $this->clean($value);
        }
    }

    private function shouldSkipSheetPayload(array $payload): bool
    {
        if (($payload['sync_action'] ?? 'UPSERT') === 'DELETE') {
            return true;
        }

        if (($payload['is_active'] ?? 'YES') === 'NO') {
            return true;
        }

        return false;
    }

    private function normalizeAmount($value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $value = trim((string) $value);

        if ($value === '') {
            return '0';
        }

        $value = str_replace(['Rp', 'rp', 'IDR', 'idr'], '', $value);
        $value = trim($value);

        // Database decimal: 1000000.00 atau 1000000,00
        if (preg_match('/^\d+[\.,]\d{1,2}$/', $value)) {
            $value = preg_replace('/[\.,]\d{1,2}$/', '', $value);
            return ltrim($value, '0') ?: '0';
        }

        // International format: 1,000,000.00
        if (preg_match('/^\d{1,3}(,\d{3})+\.\d{1,2}$/', $value)) {
            $value = preg_replace('/\.\d{1,2}$/', '', $value);
            $value = str_replace(',', '', $value);
            return ltrim($value, '0') ?: '0';
        }

        // Indonesian format: 1.000.000,00
        if (preg_match('/^\d{1,3}(\.\d{3})+,\d{1,2}$/', $value)) {
            $value = preg_replace('/,\d{1,2}$/', '', $value);
            $value = str_replace('.', '', $value);
            return ltrim($value, '0') ?: '0';
        }

        // Thousands only: 1.000.000 atau 1,000,000
        if (preg_match('/^\d{1,3}([.,]\d{3})+$/', $value)) {
            $value = str_replace(['.', ','], '', $value);
            return ltrim($value, '0') ?: '0';
        }

        $digits = preg_replace('/[^0-9]/', '', $value);

        if ($digits === '') {
            return '0';
        }

        return ltrim($digits, '0') ?: '0';
    }

private function clean(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
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

    private function normalizeYesNo(mixed $value): string
    {
        $value = strtoupper(trim((string) $value));

        return in_array($value, ['YES', 'YA', 'Y', 'TRUE', '1', 'AKTIF'], true)
            ? 'YES'
            : 'NO';
    }

    private function normalizeInteger(mixed $value, int $default = 0): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return (int) preg_replace('/[^0-9]/', '', (string) $value) ?: $default;
    }

    private function normalizeMoney(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $raw = trim((string) $value);
        $raw = str_replace(['Rp', 'rp', 'IDR', 'idr', ' '], '', $raw);

        if (str_contains($raw, ',') && str_contains($raw, '.')) {
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        } else {
            $raw = str_replace(',', '', $raw);
            $raw = str_replace('.', '', $raw);
        }

        $raw = preg_replace('/[^0-9\-]/', '', $raw);

        if ($raw === '' || $raw === '-') {
            return '0';
        }

        return (string) ((int) $raw);
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::createFromDate(1899, 12, 30)
                    ->addDays((int) $value)
                    ->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return trim((string) $value);
        }
    }
    private function normalizePaymentStatus($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        if (in_array($value, ['lunas', 'paid', 'sudah lunas', 'sudah bayar', 'selesai'], true)) {
            return 'paid';
        }

        if (in_array($value, ['belum lunas', 'belum bayar', 'unpaid', 'pending', 'belum'], true)) {
            return 'unpaid';
        }

        if (in_array($value, ['dp', 'partial', 'cicil', 'cicilan', 'sebagian'], true)) {
            return 'partial';
        }

        return $value;
    }


private function pickRecordKey(array $row, array $candidates): string
    {
        foreach ($candidates as $candidate) {
            $value = $this->clean($row[$candidate] ?? null);

            /*
             * clean() pada beberapa kondisi bisa menghasilkan null.
             * Karena method ini wajib return string, paksa semua nilai menjadi string.
             */
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

}
