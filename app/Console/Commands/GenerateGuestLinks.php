<?php

namespace App\Console\Commands;

use App\Models\Guest;
use App\Models\GuestLink;
use App\Models\WeddingProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateGuestLinks extends Command
{
    protected $signature = 'guest-links:generate';

    protected $description = 'Generate or sync personal invitation links for existing guests';

    public function handle(): int
    {
        $defaultProfile = WeddingProfile::firstOrCreate(
            ['id' => 1],
            [
                'groom_name' => 'Nama Pria',
                'bride_name' => 'Nama Wanita',
                'slug' => 'nama-pria-dan-nama-wanita',
            ]
        );

        $guests = Guest::query()->get();

        if ($guests->isEmpty()) {
            $this->warn('Belum ada data tamu.');
            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;
        $fixedCodes = 0;

        $this->info('Sync guest links dari tabel guests...');

        $bar = $this->output->createProgressBar($guests->count());
        $bar->start();

        foreach ($guests as $guest) {
            $invitationCode = $guest->invitation_code;

            /*
             * Jika invitation_code kosong ATAU masih UUID panjang,
             * maka ganti menjadi kode pendek 6 karakter.
             */
            if (! $this->isValidInvitationCode($invitationCode)) {
                $invitationCode = $this->makeUniqueInvitationCode($guest->id);

                $guest->update([
                    'invitation_code' => $invitationCode,
                ]);

                $fixedCodes++;
            }

            /*
             * Jika token bentrok dengan guest lain,
             * tetap generate kode pendek 6 karakter, bukan UUID.
             */
            while (
                GuestLink::where('token', $invitationCode)
                    ->where('guest_id', '!=', $guest->id)
                    ->exists()
                ||
                Guest::where('invitation_code', $invitationCode)
                    ->where('id', '!=', $guest->id)
                    ->exists()
            ) {
                $invitationCode = $this->makeUniqueInvitationCode($guest->id);

                $guest->update([
                    'invitation_code' => $invitationCode,
                ]);

                $fixedCodes++;
            }

            $guestSlug = Str::slug($guest->name) ?: 'tamu-' . $guest->id;
            $profileId = $guest->wedding_profile_id ?: $defaultProfile->id;

            $guestLink = GuestLink::where('guest_id', $guest->id)->first();

            if ($guestLink) {
                $guestLink->update([
                    'wedding_profile_id' => $profileId,
                    'guest_name' => $guest->name,
                    'guest_slug' => $guestSlug,
                    'token' => $invitationCode,
                    'is_active' => true,
                ]);

                $updated++;
            } else {
                GuestLink::create([
                    'wedding_profile_id' => $profileId,
                    'guest_id' => $guest->id,
                    'guest_name' => $guest->name,
                    'guest_slug' => $guestSlug,
                    'token' => $invitationCode,
                    'is_active' => true,
                    'device_warning_threshold' => 3,
                    'open_count' => 0,
                    'unique_device_count' => 0,
                    'is_suspected_shared' => false,
                    'suspicion_reason' => null,
                ]);

                $created++;
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('Selesai sync guest links.');
        $this->line("Created: {$created}");
        $this->line("Updated: {$updated}");
        $this->line("Fixed invitation_code: {$fixedCodes}");

        return self::SUCCESS;
    }

    private function isValidInvitationCode(?string $code): bool
    {
        if (! $code) {
            return false;
        }

        return preg_match('/^[A-Z2-9]{6}$/', $code) === 1;
    }

    private function makeUniqueInvitationCode(?int $ignoreGuestId = null): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';

            for ($i = 0; $i < 6; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $guestQuery = Guest::where('invitation_code', $code);

            if ($ignoreGuestId) {
                $guestQuery->where('id', '!=', $ignoreGuestId);
            }

            $guestLinkQuery = GuestLink::where('token', $code);

            if ($ignoreGuestId) {
                $guestLinkQuery->where('guest_id', '!=', $ignoreGuestId);
            }
        } while (
            $guestQuery->exists()
            || $guestLinkQuery->exists()
        );

        return $code;
    }
}