<?php

namespace App\Console\Commands;

use App\Models\Guest;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Console\Command;

class RepairGuestEventLinks extends Command
{
    protected $signature = 'wedding:repair-guest-event-links';

    protected $description = 'Repair imported guest event links based on sheet_key prefix CPP-GST / CPW-GST';

    public function handle(): int
    {
        $profile = WeddingProfile::first();

        if (!$profile) {
            $this->error('WeddingProfile belum ada.');
            return self::FAILURE;
        }

        $cppEvent = $this->eventForSide($profile->id, 'cpp', 'CPP', 'Acara Pihak Pria');
        $cpwEvent = $this->eventForSide($profile->id, 'cpw', 'CPW', 'Acara Pihak Wanita');

        $cppUpdated = Guest::query()
            ->where('sheet_key', 'like', 'CPP-GST-%')
            ->update([
                'wedding_event_id' => $cppEvent->id,
                'last_checked_at' => now(),
            ]);

        $cpwUpdated = Guest::query()
            ->where('sheet_key', 'like', 'CPW-GST-%')
            ->update([
                'wedding_event_id' => $cpwEvent->id,
                'last_checked_at' => now(),
            ]);

        $this->info("CPP guests repaired: {$cppUpdated}");
        $this->info("CPW guests repaired: {$cpwUpdated}");

        $autoBoth = WeddingEvent::query()
            ->where('sheet_key', 'AUTO-EVENT-BOTH')
            ->whereDoesntHave('guests')
            ->whereDoesntHave('budgetItems')
            ->whereDoesntHave('checklistItems')
            ->first();

        if ($autoBoth) {
            $autoBoth->delete();
            $this->info('Unused AUTO-EVENT-BOTH deleted.');
        } else {
            $this->line('AUTO-EVENT-BOTH tidak dihapus karena masih dipakai atau tidak ada.');
        }

        return self::SUCCESS;
    }

    private function eventForSide(int $profileId, string $side, string $sheetKey, string $name): WeddingEvent
    {
        $event = WeddingEvent::query()
            ->where('sheet_key', $sheetKey)
            ->first();

        if ($event) {
            return $event;
        }

        $event = WeddingEvent::query()
            ->where('event_side', $side)
            ->first();

        if ($event) {
            $event->update([
                'sheet_key' => $sheetKey,
                'last_checked_at' => now(),
            ]);

            return $event;
        }

        return WeddingEvent::create([
            'wedding_profile_id' => $profileId,
            'event_name' => $name,
            'event_side' => $side,
            'event_date' => null,
            'venue_name' => null,
            'venue_address' => null,
            'note' => 'Created by repair guest event links command.',

            'sheet_key' => $sheetKey,
            'sheet_row' => null,
            'sync_source' => 'sheet',
            'sheet_hash' => null,
            'web_hash' => null,
            'last_synced_at' => now(),
            'last_checked_at' => now(),
            'is_dummy' => false,
            'sync_note' => 'Created by repair guest event links command.',
        ]);
    }
}
