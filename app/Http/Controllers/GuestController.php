<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\GuestLink;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $profile = WeddingProfile::first();

        $events = WeddingEvent::query()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get();

        $selectedEventId = $request->input('event_id');

        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [10, 25, 50])) {
            $perPage = 10;
        }

        $guestQuery = Guest::query();

        if ($selectedEventId) {
            $guestQuery->where('wedding_event_id', $selectedEventId);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $guestQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('group_name')) {
            $guestQuery->where('group_name', $request->group_name);
        }

        if ($request->filled('rsvp_status')) {
            $guestQuery->where('rsvp_status', $request->rsvp_status);
        }

        if ($request->filled('sent_status')) {
            if ($request->sent_status === 'sent') {
                $guestQuery->whereNotNull('invitation_sent_at');
            }

            if ($request->sent_status === 'not_sent') {
                $guestQuery->whereNull('invitation_sent_at');
            }
        }

        $trackingStatus = $request->input('tracking_status');

        if ($trackingStatus === 'warning') {
            $guestQuery->whereHas('guestLink', function ($query) {
                $query->where('is_suspected_shared', true);
            });
        }

        $totalFiltered = (clone $guestQuery)->count();

        $guests = $guestQuery
            ->with(['weddingEvent', 'guestLink.weddingProfile', 'guestLink.sessions'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        /*
         * Summary mengikuti filter acara.
         * Kalau event_id dipilih, angka summary hanya untuk acara tersebut.
         * Kalau tidak dipilih, summary menampilkan semua acara.
         */
        $summaryQuery = Guest::query();

        if ($selectedEventId) {
            $summaryQuery->where('wedding_event_id', $selectedEventId);
        }

        $totalGuests = (clone $summaryQuery)->count();

        $totalInvitedPeople = (clone $summaryQuery)
            ->where('rsvp_status', 'attend')
            ->sum('total_invited');

        $totalSent = (clone $summaryQuery)
            ->whereNotNull('invitation_sent_at')
            ->count();

        /*
         * Group juga mengikuti filter acara.
         * Jadi saat memilih Acara CPW, opsi grup yang muncul hanya dari tamu CPW.
         */
        $groupsQuery = Guest::whereNotNull('group_name')
            ->where('group_name', '!=', '');

        if ($selectedEventId) {
            $groupsQuery->where('wedding_event_id', $selectedEventId);
        }

        $groups = $groupsQuery
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        $trackingSummaryQuery = Guest::query();

        if ($selectedEventId) {
            $trackingSummaryQuery->where('wedding_event_id', $selectedEventId);
        }

        $trackingGuestIds = $trackingSummaryQuery->pluck('id');

        $totalOpenedLinks = GuestLink::whereIn('guest_id', $trackingGuestIds)
            ->where('open_count', '>', 0)
            ->count();

        $totalUnopenedLinks = GuestLink::whereIn('guest_id', $trackingGuestIds)
            ->where(function ($query) {
                $query->whereNull('open_count')
                    ->orWhere('open_count', 0);
            })
            ->count();

        $totalWarningLinks = GuestLink::whereIn('guest_id', $trackingGuestIds)
            ->where('is_suspected_shared', true)
            ->count();

        $averageDeviceCount = round(
            (float) GuestLink::whereIn('guest_id', $trackingGuestIds)
                ->avg('unique_device_count'),
            1
        );

        return view('guests.index', compact(
            'guests',
            'profile',
            'events',
            'selectedEventId',
            'totalGuests',
            'totalInvitedPeople',
            'totalSent',
            'totalFiltered',
            'groups',
            'perPage',
            'trackingStatus',
            'totalOpenedLinks',
            'totalUnopenedLinks',
            'totalWarningLinks',
            'averageDeviceCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['required', 'exists:wedding_events,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'rsvp_status' => ['required', 'in:pending,attend,not_attend'],
        ]);

        $event = WeddingEvent::findOrFail($validated['wedding_event_id']);

        $validated['wedding_profile_id'] = $event->wedding_profile_id;
        $validated['invitation_code'] = $this->makeUniqueInvitationCode();

        /*
         * Admin tidak menentukan jumlah hadir.
         * Pending dan attend default 1.
         * Tidak hadir otomatis 0.
         * Jumlah final akan diisi oleh tamu saat RSVP.
         */
        if ($validated['rsvp_status'] === 'not_attend') {
            $validated['total_invited'] = 0;
        } else {
            $validated['total_invited'] = 1;
        }

        $guest = Guest::create($validated);

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

        return redirect()
            ->route('guests.index', ['event_id' => $validated['wedding_event_id']])
            ->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function edit(Guest $guest)
    {
        $profile = WeddingProfile::first();

        $events = WeddingEvent::query()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get();

        $groups = Guest::whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->distinct()
            ->orderBy('group_name')
            ->pluck('group_name');

        return view('guests.edit', compact(
            'guest',
            'profile',
            'events',
            'groups'
        ));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['required', 'exists:wedding_events,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'rsvp_status' => ['required', 'in:pending,attend,not_attend'],
        ]);

        $event = WeddingEvent::findOrFail($validated['wedding_event_id']);

        $validated['wedding_profile_id'] = $event->wedding_profile_id;

        if ($validated['rsvp_status'] === 'pending') {
            $validated['total_invited'] = 1;
        }

        if ($validated['rsvp_status'] === 'not_attend') {
            $validated['total_invited'] = 0;
        }

        if ($validated['rsvp_status'] === 'attend') {
            $validated['total_invited'] = max(1, (int) $guest->total_invited);
        }

        $guest->update($validated);

        $guest->guestLink()->updateOrCreate(
            ['guest_id' => $guest->id],
            [
                'wedding_profile_id' => $guest->wedding_profile_id,
                'guest_name' => $guest->name,
                'guest_slug' => Str::slug($guest->name) ?: 'tamu-' . $guest->id,
                'token' => $guest->invitation_code,
                'is_active' => true,
                'device_warning_threshold' => 3,
            ]
        );

        return redirect()
            ->route('guests.index', ['event_id' => $validated['wedding_event_id']])
            ->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function markSent(Guest $guest)
    {
        $guest->update([
            'invitation_sent_at' => now(),
        ]);

        return redirect()
            ->route('guests.index', ['event_id' => $guest->wedding_event_id])
            ->with('success', 'Status undangan berhasil ditandai terkirim.');
    }

    public function destroy(Guest $guest)
    {
        $eventId = $guest->wedding_event_id;

        $guest->delete();

        return redirect()
            ->route('guests.index', ['event_id' => $eventId])
            ->with('success', 'Tamu berhasil dihapus.');
    }

    private function makeUniqueInvitationCode(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';

            for ($i = 0; $i < 6; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (
            Guest::where('invitation_code', $code)->exists()
            || GuestLink::where('token', $code)->exists()
        );

        return $code;
    }

    public function resetTracking(Request $request, Guest $guest)
    {
        $guestLink = $guest->guestLink;

        if ($guestLink) {
            $guestLink->sessions()->delete();

            $guestLink->update([
                'open_count' => 0,
                'unique_device_count' => 0,
                'first_opened_at' => null,
                'last_opened_at' => null,
                'is_suspected_shared' => false,
                'suspicion_reason' => null,
            ]);
        }

        return redirect()
            ->route('guests.index', $request->query())
            ->with('success', 'Tracking link tamu berhasil direset.');
    }
}
