<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FreshWeddingDemoSeeder extends Seeder
{
    private array $columnCache = [];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'sync_differences',
            'sync_runs',
            'guest_links',
            'guests',
            'budget_items',
            'checklist_items',
            'wedding_events',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        Schema::enableForeignKeyConstraints();

        $profileId = $this->ensureWeddingProfile();

        $cpwEventId = $this->insertDynamic('wedding_events', [
            'wedding_profile_id' => $profileId,
            'event_key' => 'CPW',
            'event_side' => 'cpw',
            'side' => 'CPW',
            'event_name' => 'Akad dan Resepsi CPW',
            'name' => 'Akad dan Resepsi CPW',
            'title' => 'Akad dan Resepsi CPW',
            'event_date' => now()->addMonths(3)->toDateString(),
            'event_time' => '09:00',
            'location' => 'Gedung Wanita',
            'address' => 'Jl. Contoh CPW No. 1',
            'sync_source' => 'web',
            'sheet_key' => 'CPW',
            'is_dummy' => false,
        ]);

        $cppEventId = $this->insertDynamic('wedding_events', [
            'wedding_profile_id' => $profileId,
            'event_key' => 'CPP',
            'event_side' => 'cpp',
            'side' => 'CPP',
            'event_name' => 'Ngunduh Mantu CPP',
            'name' => 'Ngunduh Mantu CPP',
            'title' => 'Ngunduh Mantu CPP',
            'event_date' => now()->addMonths(3)->addDays(7)->toDateString(),
            'event_time' => '10:00',
            'location' => 'Gedung Pria',
            'address' => 'Jl. Contoh CPP No. 2',
            'sync_source' => 'web',
            'sheet_key' => 'CPP',
            'is_dummy' => false,
        ]);

        $this->seedGuests($profileId, $cpwEventId, 'CPW', [
            ['Ibu Ratna', '081234560001', 'Keluarga', 2, 'Jl. Melati No. 1'],
            ['Pak Budi Santoso', '081234560002', 'Tetangga', 1, 'Jl. Mawar No. 2'],
            ['Salsa Amalia', '081234560003', 'Teman', 2, 'Jl. Kenanga No. 3'],
        ]);

        $this->seedGuests($profileId, $cppEventId, 'CPP', [
            ['Pak Ahmad', '081234560101', 'Keluarga', 2, 'Jl. Anggrek No. 1'],
            ['Ibu Sari', '081234560102', 'Tetangga', 1, 'Jl. Dahlia No. 2'],
            ['Rizky Pratama', '081234560103', 'Teman', 2, 'Jl. Cempaka No. 3'],
        ]);

        $this->seedBudgets($profileId, $cpwEventId, 'CPW', [
            ['Dekorasi', 'Dekorasi pelaminan CPW', 5000000],
            ['MUA', 'Makeup pengantin CPW', 3000000],
            ['Konsumsi', 'Katering keluarga CPW', 8000000],
        ]);

        $this->seedBudgets($profileId, $cppEventId, 'CPP', [
            ['Venue', 'Sewa gedung CPP', 7000000],
            ['Dokumentasi', 'Foto dan video CPP', 4500000],
            ['Souvenir', 'Souvenir tamu CPP', 2500000],
        ]);

        $this->seedChecklist($profileId, $cpwEventId, 'Persiapan', [
            ['Booking gedung', 'Konfirmasi jadwal dan DP gedung'],
            ['Finalisasi dekorasi', 'Pilih tema dan vendor dekorasi'],
            ['Konfirmasi katering', 'Finalisasi jumlah pax katering'],
        ]);

        $this->seedChecklist($profileId, $cpwEventId, 'Dokumen Nikah', [
            ['KTP calon pengantin', 'Siapkan scan dan fotokopi KTP'],
            ['Kartu keluarga', 'Siapkan dokumen KK kedua pihak'],
            ['Pas foto', 'Siapkan pas foto sesuai ketentuan KUA'],
        ]);

        $this->command?->info('Fresh wedding demo seed selesai.');
    }

    private function ensureWeddingProfile(): int
    {
        if (!Schema::hasTable('wedding_profiles')) {
            return 1;
        }

        $existing = DB::table('wedding_profiles')->orderBy('id')->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return $this->insertDynamic('wedding_profiles', [
            'bride_name' => 'Dinda',
            'groom_name' => 'Teguh',
            'name' => 'Wedding Dinda & Teguh',
            'title' => 'Wedding Dinda & Teguh',
            'slug' => 'wedding-dinda-teguh',
            'wedding_date' => now()->addMonths(3)->toDateString(),
            'is_active' => true,
        ]);
    }

    private function seedGuests(int $profileId, int $eventId, string $side, array $rows): void
    {
        foreach ($rows as $index => $row) {
            [$name, $phone, $group, $totalInvited, $address] = $row;

            $number = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $sheetKey = "{$side}-GST-WEB-{$number}";
            $code = "{$side}-DEMO-{$number}";

            $guestId = $this->insertDynamic('guests', [
                'wedding_profile_id' => $profileId,
                'wedding_event_id' => $eventId,
                'name' => $name,
                'phone' => $this->normalizePhone($phone),
                'invitation_code' => $code,
                'address' => $address,
                'group_name' => $group,
                'total_invited' => $totalInvited,
                'rsvp_status' => 'pending',
                'rsvp_count' => 0,
                'rsvp_confirmed_at' => null,
                'rsvp_note' => null,
                'invitation_sent_at' => null,
                'attendance_status' => 'not_arrived',
                'actual_attendance_count' => 0,
                'checked_in_at' => null,
                'envelope_amount' => 0,
                'souvenir_status' => 'not_given',
                'souvenir_count' => 0,
                'sheet_key' => $sheetKey,
                'sync_source' => 'web',
                'is_dummy' => false,
                'sync_note' => 'Seeder fresh start.',
            ]);

            $this->insertDynamic('guest_links', [
                'wedding_profile_id' => $profileId,
                'guest_id' => $guestId,
                'guest_name' => $name,
                'guest_slug' => Str::slug($name) ?: 'tamu-' . $guestId,
                'token' => $code,
                'is_active' => true,
                'device_warning_threshold' => 3,
                'open_count' => 0,
                'unique_device_count' => 0,
                'is_suspected_shared' => false,
            ]);
        }
    }

    private function seedBudgets(int $profileId, int $eventId, string $side, array $rows): void
    {
        foreach ($rows as $index => $row) {
            [$category, $item, $amount] = $row;

            $number = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $this->insertDynamic('budget_items', [
                'wedding_profile_id' => $profileId,
                'wedding_event_id' => $eventId,
                'side' => $side,
                'owner_side' => $side,
                'payer_side' => $side,
                'category' => $category,
                'name' => $item,
                'title' => $item,
                'item_name' => $item,
                'description' => $item,
                'estimated_amount' => $amount,
                'actual_amount' => 0,
                'amount' => $amount,
                'payment_status' => 'unpaid',
                'status' => 'unpaid',
                'sheet_key' => "{$side}-BDG-WEB-{$number}",
                'sync_source' => 'web',
                'is_dummy' => false,
                'sync_note' => 'Seeder fresh start.',
            ]);
        }
    }

    private function seedChecklist(int $profileId, int $eventId, string $category, array $rows): void
    {
        foreach ($rows as $index => $row) {
            [$title, $note] = $row;

            $prefix = $category === 'Dokumen Nikah' ? 'DOC' : 'CHK';
            $number = str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $this->insertDynamic('checklist_items', [
                'wedding_profile_id' => $profileId,
                'wedding_event_id' => $eventId,
                'category' => $category,
                'type' => $category,
                'module' => $category,
                'name' => $title,
                'title' => $title,
                'item_name' => $title,
                'description' => $note,
                'note' => $note,
                'status' => $this->checklistDefaultStatus(),
                'is_completed' => false,
                'completed_at' => null,
                'due_date' => now()->addWeeks(2)->toDateString(),
                'sheet_key' => "{$prefix}-WEB-{$number}",
                'sync_source' => 'web',
                'is_dummy' => false,
                'sync_note' => 'Seeder fresh start.',
            ]);
        }
    }

    private function checklistDefaultStatus(): string
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'mysql') {
            return 'pending';
        }

        try {
            $row = \Illuminate\Support\Facades\DB::selectOne(
                "SHOW COLUMNS FROM checklist_items LIKE 'status'"
            );

            $type = strtolower((string) ($row->Type ?? ''));

            if (preg_match("/enum\((.*)\)/", $type, $matches)) {
                $values = str_getcsv($matches[1], ',', "'");

                foreach ([
                    'pending',
                    'todo',
                    'not_started',
                    'belum',
                    'belum_selesai',
                    'incomplete',
                    'open',
                    'draft',
                    'process',
                    'proses',
                    'not_done',
                ] as $candidate) {
                    if (in_array($candidate, $values, true)) {
                        return $candidate;
                    }
                }

                return $values[0] ?? 'pending';
            }
        } catch (\Throwable $e) {
            return 'pending';
        }

        return 'pending';
    }

    private function insertDynamic(string $table, array $data): int
    {
        if (!Schema::hasTable($table)) {
            return 1;
        }

        $columns = $this->columns($table);

        if (in_array('created_at', $columns, true) && !array_key_exists('created_at', $data)) {
            $data['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true) && !array_key_exists('updated_at', $data)) {
            $data['updated_at'] = now();
        }

        $filtered = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $columns, true)) {
                $filtered[$key] = $value;
            }
        }

        foreach ($this->requiredColumns($table) as $column) {
            if (!array_key_exists($column, $filtered)) {
                $filtered[$column] = $this->guessRequiredValue($column);
            }
        }

        if (in_array('id', $columns, true)) {
            return (int) DB::table($table)->insertGetId($filtered);
        }

        DB::table($table)->insert($filtered);

        return 1;
    }

    private function columns(string $table): array
    {
        if (!isset($this->columnCache[$table])) {
            $this->columnCache[$table] = Schema::getColumnListing($table);
        }

        return $this->columnCache[$table];
    }

    private function requiredColumns(string $table): array
    {
        if (DB::getDriverName() !== 'mysql') {
            return [];
        }

        $rows = DB::select(
            "SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?",
            [$table]
        );

        $required = [];

        foreach ($rows as $row) {
            $column = $row->COLUMN_NAME;

            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'], true)) {
                continue;
            }

            if (str_contains((string) $row->EXTRA, 'auto_increment')) {
                continue;
            }

            if ($row->IS_NULLABLE === 'NO' && $row->COLUMN_DEFAULT === null) {
                $required[] = $column;
            }
        }

        return $required;
    }

    private function guessRequiredValue(string $column): mixed
    {
        $column = strtolower($column);

        if (str_ends_with($column, '_id')) {
            return 1;
        }

        if (str_contains($column, 'date')) {
            return now()->toDateString();
        }

        if (str_contains($column, '_at')) {
            return now();
        }

        if (str_starts_with($column, 'is_') || str_starts_with($column, 'has_')) {
            return false;
        }

        if (
            str_contains($column, 'amount') ||
            str_contains($column, 'price') ||
            str_contains($column, 'total') ||
            str_contains($column, 'count') ||
            str_contains($column, 'qty') ||
            str_contains($column, 'number')
        ) {
            return 0;
        }

        if (str_contains($column, 'status')) {
            return 'pending';
        }

        if (str_contains($column, 'phone') || str_contains($column, 'wa')) {
            return '6281234567890';
        }

        if (str_contains($column, 'email')) {
            return 'demo@example.com';
        }

        if (str_contains($column, 'slug')) {
            return 'demo-' . Str::random(6);
        }

        if (str_contains($column, 'key') || str_contains($column, 'code') || str_contains($column, 'token')) {
            return 'DEMO-' . strtoupper(Str::random(6));
        }

        return 'Demo';
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
