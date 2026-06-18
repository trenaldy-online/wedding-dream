<?php

namespace App\Services\WeddingSync;

use App\Models\BudgetItem;
use App\Models\ChecklistItem;
use App\Models\Guest;
use App\Models\GuestLink;
use App\Models\SyncDifference;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class SheetToWebImporter
{
    public function __construct(
        private SyncHasher $hasher
    ) {
    }

    public function import(SyncDifference $difference, ?int $adminId = null): object
    {
        if ($difference->status !== 'pending') {
            throw new RuntimeException('Data ini sudah tidak berstatus pending.');
        }

        if (! in_array($difference->difference_type, ['sheet_only', 'different'], true)) {
            throw new RuntimeException('Hanya data sheet_only atau different yang bisa di-import dari Sheet ke Web.');
        }

        $payload = $difference->sheet_payload ?? [];

        if (empty($payload)) {
            throw new RuntimeException('Sheet payload kosong.');
        }

        return DB::transaction(function () use ($difference, $payload, $adminId) {
            $record = match ($difference->module) {
                'events' => $this->importEvent($payload),
                'guests' => $this->importGuest($payload, $difference),
                'budget_items' => $this->importBudgetItem($payload),
                'checklist_items' => $this->importChecklistItem($payload, $difference->sheet_name),
                default => throw new RuntimeException('Module belum didukung: ' . $difference->module),
            };

            $difference->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => $adminId,
                'web_model' => get_class($record),
                'web_id' => $record->id,
                'web_payload' => [
                    'id' => $record->id,
                    'sheet_key' => $record->sheet_key ?? null,
                    'imported_at' => now()->toDateTimeString(),
                ],
                'note' => trim(($difference->note ?? '') . "\nImported Sheet → Web by admin."),
            ]);

            return $record;
        });
    }

    private function importEvent(array $payload): WeddingEvent
    {
        $profile = $this->profile();

        $recordKey = $this->value($payload, 'record_key');

        if (!$recordKey) {
            throw new RuntimeException('event_key / record_key kosong.');
        }

        $eventSide = $this->normalizeSide($payload['event_side'] ?? null);

        $data = [
            'wedding_profile_id' => $profile->id,
            'event_name' => $this->value($payload, 'event_name') ?: 'Acara Wedding',
            'event_side' => $eventSide,
            'event_date' => $payload['event_date'] ?? null,
            'venue_name' => $this->value($payload, 'venue_name'),
            'venue_address' => null,
            'note' => $this->value($payload, 'sync_note'),

            'sheet_key' => $recordKey,
            'sheet_row' => $payload['_sheet_row'] ?? null,
            'sync_source' => 'sheet',
            'sheet_hash' => $this->hasher->make($payload),
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => $this->value($payload, 'sync_note'),
        ];

        return WeddingEvent::updateOrCreate(
            ['sheet_key' => $recordKey],
            $data
        );
    }

    private function importGuest(array $payload, ?SyncDifference $difference = null): Guest
    {
        $recordKey = $this->value($payload, 'record_key');

        if (!$recordKey) {
            throw new RuntimeException('guest_key / record_key kosong.');
        }

        $event = $this->findOrCreateEventForPayload($payload);
        $profile = $event?->weddingProfile ?: $this->profile();

        $existing = null;

        if ($difference?->web_id && $difference?->web_model === Guest::class) {
            $existing = Guest::find($difference->web_id);
        }

        if (!$existing) {
            $existing = Guest::where('sheet_key', $recordKey)->first();
        }

        $sourceWebKey = $this->value($payload, 'source_web_key');

        if (!$existing && $sourceWebKey) {
            $existing = Guest::where('sheet_key', $sourceWebKey)->first();
        }

        $invitationCode = $existing?->invitation_code ?: $this->makeUniqueInvitationCode();

        $totalInvited = $this->payloadInt($payload, 'total_invited', 1, 1);

        $rsvpStatus = $this->normalizeRsvpStatus($payload['rsvp_status'] ?? null);
        $rsvpCount = $this->payloadInt($payload, 'rsvp_count', 0, 0);

        if ($rsvpStatus === 'attend' && $rsvpCount < 1) {
            $rsvpCount = 1;
        }

        if ($rsvpStatus === 'not_attend') {
            $rsvpCount = 0;
        }

        $invitationStatus = $this->normalizeInvitationStatus($payload['invitation_status'] ?? null);

        $attendanceStatus = $this->normalizeAttendanceStatus($payload['attendance_status'] ?? null);
        $actualAttendanceCount = $this->payloadInt($payload, 'actual_attendance_count', 0, 0);

        if ($attendanceStatus === 'arrived' && $actualAttendanceCount < 1) {
            $actualAttendanceCount = max(1, $rsvpCount);
        }

        $souvenirStatus = $this->normalizeSouvenirStatus($payload['souvenir_status'] ?? null);
        $souvenirCount = $this->payloadInt($payload, 'souvenir_count', 0, 0);

        if ($souvenirStatus === 'given' && $souvenirCount < 1) {
            $souvenirCount = 1;
        }

        if ($souvenirStatus === 'not_given') {
            $souvenirCount = 0;
        }

        $data = [
            'wedding_profile_id' => $profile->id,
            'wedding_event_id' => $event?->id,

            'name' => $this->value($payload, 'name') ?: 'Tamu',
            'phone' => $this->value($payload, 'phone'),
            'invitation_code' => $invitationCode,
            'address' => $this->value($payload, 'address'),
            'group_name' => $this->value($payload, 'group_name'),
            'total_invited' => $totalInvited,

            /*
             * Sistem 2: RSVP dari tamu.
             */
            'rsvp_status' => $rsvpStatus,
            'rsvp_count' => $rsvpCount,
            'rsvp_confirmed_at' => $this->resolveRsvpConfirmedAt($existing, $rsvpStatus),
            'rsvp_note' => $this->value($payload, 'rsvp_note'),

            /*
             * Status undangan tetap memakai timestamp.
             */
            'invitation_sent_at' => $this->resolveInvitationSentAt($existing, $invitationStatus),

            /*
             * Sistem 3: data hari-H / setelah acara.
             */
            'attendance_status' => $attendanceStatus,
            'actual_attendance_count' => $actualAttendanceCount,
            'checked_in_at' => $this->resolveCheckedInAt($existing, $attendanceStatus),
            'envelope_amount' => $this->payloadAmount($payload, 'envelope_amount'),
            'souvenir_status' => $souvenirStatus,
            'souvenir_count' => $souvenirCount,

            'sheet_key' => $recordKey,
            'sheet_row' => $payload['_sheet_row'] ?? null,
            'sync_source' => 'sheet',
            'sheet_hash' => $this->hasher->make($payload),
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'sheet_updated_at' => $this->normalizeDateTime($payload['sheet_updated_at'] ?? null),
            'is_dummy' => false,
            'sync_note' => $this->value($payload, 'sync_note'),
        ];

        if ($existing) {
            $existing->fill($data);
            $existing->save();

            $guest = $existing;
        } else {
            $guest = Guest::create($data);
        }

        $this->ensureGuestLink($guest);

        return $guest;
    }

    private function importBudgetItem(array $payload): BudgetItem
    {
        $recordKey = $this->value($payload, 'record_key');

        if (!$recordKey) {
            throw new RuntimeException('budget_key / record_key kosong.');
        }

        $event = $this->findOrCreateEventForPayload($payload);
        $profile = $event?->weddingProfile ?: $this->profile();

        $noteParts = [];

        if ($this->value($payload, 'vendor')) {
            $noteParts[] = 'Vendor: ' . $this->value($payload, 'vendor');
        }

        if ($this->value($payload, 'deadline_bayar')) {
            $noteParts[] = 'Deadline bayar: ' . $this->value($payload, 'deadline_bayar');
        }

        if ($this->value($payload, 'tanggal_bayar')) {
            $noteParts[] = 'Tanggal bayar: ' . $this->value($payload, 'tanggal_bayar');
        }

        if ($this->value($payload, 'sync_note')) {
            $noteParts[] = $this->value($payload, 'sync_note');
        }

        $data = [
            'wedding_profile_id' => $profile->id,
            'wedding_event_id' => $event?->id,

            'category' => $this->value($payload, 'category') ?: 'Lainnya',
            'item_name' => $this->value($payload, 'item_name') ?: 'Item Budget',
            'estimated_amount' => (float) ($payload['estimated_amount'] ?? 0),
            'actual_amount' => (float) ($payload['actual_amount'] ?? 0),
            'payment_status' => $this->normalizePaymentStatus($payload['payment_status'] ?? null),
            'note' => implode(' | ', array_filter($noteParts)) ?: null,

            'sheet_key' => $recordKey,
            'sheet_row' => $payload['_sheet_row'] ?? null,
            'sync_source' => 'sheet',
            'sheet_hash' => $this->hasher->make($payload),
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => $this->value($payload, 'sync_note'),
        ];

        return BudgetItem::updateOrCreate(
            ['sheet_key' => $recordKey],
            $data
        );
    }

    private function importChecklistItem(array $payload, ?string $sheetName = null): ChecklistItem
    {
        $recordKey = $this->value($payload, 'record_key');

        if (!$recordKey) {
            throw new RuntimeException('task_key/doc_key / record_key kosong.');
        }

        $isDocument = $sheetName === 'INPUT_DOKUMEN'
            || ($this->value($payload, 'category') === 'Dokumen Nikah');

        $event = $isDocument ? null : $this->findOrCreateEventForPayload($payload);
        $profile = $event?->weddingProfile ?: $this->profile();

        $status = $this->normalizeChecklistStatus($payload['status'] ?? null);

        $data = [
            'wedding_profile_id' => $profile->id,
            'wedding_event_id' => $event?->id,

            'title' => $this->value($payload, 'title') ?: 'Checklist',
            'category' => $isDocument ? 'Dokumen Nikah' : $this->value($payload, 'category'),
            'assigned_to' => $this->normalizeAssignedTo($payload['assigned_to'] ?? $payload['event_side'] ?? null),
            'status' => $status,
            'due_date' => $payload['due_date'] ?? null,
            'priority' => $isDocument ? null : ($this->value($payload, 'priority') ?: 'Wajib'),
            'note' => $this->value($payload, 'sync_note'),
            'completed_at' => $status === 'done' ? now() : null,

            'sheet_key' => $recordKey,
            'sheet_row' => $payload['_sheet_row'] ?? null,
            'sync_source' => 'sheet',
            'sheet_hash' => $this->hasher->make($payload),
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => $this->value($payload, 'sync_note'),
        ];

        return ChecklistItem::updateOrCreate(
            ['sheet_key' => $recordKey],
            $data
        );
    }

    private function payloadInt(array $payload, string $key, int $default = 0, int $min = 0): int
    {
        $value = $payload[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^0-9-]/', '', $value);
        }

        if ($value === '' || $value === null) {
            return $default;
        }

        return max($min, (int) $value);
    }

    private function payloadAmount(array $payload, string $key): int
    {
        $value = $payload[$key] ?? 0;

        if ($value === null || $value === '') {
            return 0;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^0-9]/', '', $value);
        }

        if ($value === '' || $value === null) {
            return 0;
        }

        return max(0, (int) $value);
    }

    private function normalizeRsvpStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        if ($value === '') {
            return 'pending';
        }

        if (in_array($value, ['attend', 'hadir', 'ya', 'yes', 'confirmed', 'konfirmasi hadir'], true)) {
            return 'attend';
        }

        if (str_contains($value, 'tidak') || str_contains($value, 'not attend') || str_contains($value, 'decline')) {
            return 'not_attend';
        }

        return 'pending';
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
        $value = preg_replace('/\s+/', ' ', $value);

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
        $value = preg_replace('/\s+/', ' ', $value);

        if ($value === '') {
            return 'not_given';
        }

        if (in_array($value, ['ya', 'yes', 'given', 'diberikan', 'sudah diberikan', 'sudah'], true)) {
            return 'given';
        }

        return 'not_given';
    }

    private function resolveRsvpConfirmedAt(?Guest $guest, string $rsvpStatus): mixed
    {
        if ($rsvpStatus === 'pending') {
            return null;
        }

        return $guest?->rsvp_confirmed_at ?: now();
    }

    private function resolveInvitationSentAt(?Guest $guest, string $invitationStatus): mixed
    {
        if ($invitationStatus !== 'sent') {
            return null;
        }

        return $guest?->invitation_sent_at ?: now();
    }

    private function resolveCheckedInAt(?Guest $guest, string $attendanceStatus): mixed
    {
        if ($attendanceStatus !== 'arrived') {
            return null;
        }

        return $guest?->checked_in_at ?: now();
    }

    private function normalizeDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return \Carbon\Carbon::createFromDate(1899, 12, 30)
                    ->addDays((int) $value)
                    ->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    private function findOrCreateEventForPayload(array $payload): ?WeddingEvent
    {
        /*
         * Prioritas mapping event:
         * 1. event_key dari Sheet, misalnya CPP / CPW
         * 2. event_side / assigned_to
         * 3. fallback both
         *
         * Ini penting supaya INPUT_TAMU_CPP tidak masuk ke AUTO-EVENT-BOTH.
         */

        $eventKey = strtoupper((string) $this->value($payload, 'event_key'));
        $side = $this->sideFromPayload($payload);

        if ($eventKey) {
            $event = WeddingEvent::where('sheet_key', $eventKey)->first();

            if ($event) {
                return $event;
            }

            $event = WeddingEvent::where('event_side', strtolower($eventKey))->first();

            if ($event) {
                if (!$event->sheet_key) {
                    $event->update([
                        'sheet_key' => $eventKey,
                        'last_checked_at' => now(),
                    ]);
                }

                return $event;
            }
        }

        $event = WeddingEvent::where('event_side', $side)->first();

        if ($event) {
            return $event;
        }

        $profile = $this->profile();

        $name = match ($side) {
            'cpw' => 'Acara Pihak Wanita',
            'cpp' => 'Acara Pihak Pria',
            default => 'Acara Bersama',
        };

        $sheetKey = $eventKey ?: 'AUTO-EVENT-' . strtoupper($side);

        return WeddingEvent::create([
            'wedding_profile_id' => $profile->id,
            'event_name' => $name,
            'event_side' => $side,
            'event_date' => null,
            'venue_name' => null,
            'venue_address' => null,
            'note' => 'Auto-created during Sheet → Web import.',

            'sheet_key' => $sheetKey,
            'sheet_row' => null,
            'sync_source' => 'sheet',
            'sheet_hash' => null,
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => 'Auto-created during Sheet → Web import.',
        ]);
    }

    private function sideFromPayload(array $payload): string
    {
        $eventKey = $this->value($payload, 'event_key');

        if ($eventKey) {
            return $this->normalizeSide($eventKey);
        }

        return $this->normalizeSide(
            $payload['event_side']
            ?? $payload['assigned_to']
            ?? $payload['record_key']
            ?? null
        );
    }

    private function ensureGuestLink(Guest $guest): void
    {
        if ($guest->guestLink) {
            $guest->guestLink->update([
                'wedding_profile_id' => $guest->wedding_profile_id,
                'guest_name' => $guest->name,
                'guest_slug' => Str::slug($guest->name) ?: 'tamu-' . $guest->id,
                'token' => $guest->invitation_code,
                'is_active' => true,
            ]);

            return;
        }

        GuestLink::create([
            'wedding_profile_id' => $guest->wedding_profile_id,
            'guest_id' => $guest->id,
            'guest_name' => $guest->name,
            'guest_slug' => Str::slug($guest->name) ?: 'tamu-' . $guest->id,
            'token' => $guest->invitation_code,
            'is_active' => true,
            'device_warning_threshold' => 3,
            'open_count' => 0,
            'unique_device_count' => 0,
            'is_suspected_shared' => false,
        ]);
    }

    private function makeUniqueInvitationCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (
            Guest::where('invitation_code', $code)->exists()
            || GuestLink::where('token', $code)->exists()
        );

        return $code;
    }

    private function profile(): WeddingProfile
    {
        $profile = WeddingProfile::first();

        if ($profile) {
            return $profile;
        }

        return WeddingProfile::create([
            'groom_name' => 'Aldy',
            'bride_name' => 'Dinda',
            'slug' => 'dinda-dan-aldy',
            'event_date' => null,
            'venue_name' => null,
            'venue_address' => null,
        ]);
    }

    private function normalizeSide(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if (str_contains($value, 'cpw') || str_contains($value, 'wanita') || str_contains($value, 'cewe')) {
            return 'cpw';
        }

        if (str_contains($value, 'cpp') || str_contains($value, 'pria') || str_contains($value, 'cowo')) {
            return 'cpp';
        }

        return 'both';
    }

    private function normalizeAssignedTo(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if (str_contains($value, 'cpw') || str_contains($value, 'wanita') || str_contains($value, 'cewe')) {
            return 'cpw';
        }

        if (str_contains($value, 'cpp') || str_contains($value, 'pria') || str_contains($value, 'cowo')) {
            return 'cpp';
        }

        return 'both';
    }

    private function normalizePaymentStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        if ($value === '') {
            return 'unpaid';
        }

        /*
         * Urutan penting:
         * "belum lunas" harus dicek sebelum "lunas",
         * karena "belum lunas" mengandung kata "lunas".
         */
        if (in_array($value, ['belum lunas', 'belum bayar', 'unpaid', 'pending', 'belum'], true)) {
            return 'unpaid';
        }

        if (in_array($value, ['dp', 'partial', 'cicil', 'cicilan', 'sebagian', 'bayar sebagian'], true)) {
            return 'partial';
        }

        if (in_array($value, ['lunas', 'paid', 'sudah lunas', 'sudah bayar', 'selesai'], true)) {
            return 'paid';
        }

        if (str_contains($value, 'belum') || str_contains($value, 'unpaid')) {
            return 'unpaid';
        }

        if (str_contains($value, 'dp') || str_contains($value, 'partial') || str_contains($value, 'sebagian') || str_contains($value, 'cicil')) {
            return 'partial';
        }

        if (str_contains($value, 'lunas') || str_contains($value, 'paid')) {
            return 'paid';
        }

        return 'unpaid';
    }

    private function normalizeChecklistStatus(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        if (str_contains($value, 'selesai') || str_contains($value, 'done') || str_contains($value, 'complete')) {
            return 'done';
        }

        if (str_contains($value, 'proses') || str_contains($value, 'progress')) {
            return 'in_progress';
        }

        return 'todo';
    }

    private function value(array $payload, string $key): ?string
    {
        $value = $payload[$key] ?? null;

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
