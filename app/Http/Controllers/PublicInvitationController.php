<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\GuestLinkSession;
use App\Models\WeddingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicInvitationController extends Controller
{
    public function show(string $slug)
    {
        $profile = WeddingProfile::where('slug', $slug)
            ->firstOrFail();

        $events = $profile->events()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get();

        return view('invitations.show', compact('profile', 'events'));
    }

    public function showGuest(Request $request, string $slug, string $code)
    {
        $guest = Guest::with(['weddingProfile.sections', 'weddingEvent', 'guestLink'])
            ->where('invitation_code', $code)
            ->firstOrFail();

        $correctGuestSlug = $this->makeGuestSlug($guest);

        if ($slug !== $correctGuestSlug) {
            return redirect()->route('invitation.guest', [
                'slug' => $correctGuestSlug,
                'code' => $guest->invitation_code,
            ]);
        }

        $profile = $guest->weddingProfile;

        abort_if(! $profile, 404);

        $guestLink = $this->ensureGuestLink($guest);

        $trackingCookie = $this->trackGuestLinkOpen($request, $guestLink);

        $profile->ensureDefaultSections();

        $sections = $profile->sections()
            ->orderBy('sort_order')
            ->get();

        return response()
            ->view('templates.anselma.preview', [
                'profile' => $profile,
                'sections' => $sections,
                'guest' => $guest,
                'guestName' => $guest->name,
            ])
            ->withCookie($trackingCookie);
    }

    public function submitRsvp(Request $request, string $slug, string $code)
    {
        $validated = $request->validate([
            'rsvp_status' => ['required', 'in:attend,not_attend'],
            'total_invited' => ['required_if:rsvp_status,attend', 'nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $guest = Guest::with(['weddingProfile', 'guestLink'])
            ->where('invitation_code', $code)
            ->firstOrFail();

        $correctGuestSlug = $this->makeGuestSlug($guest);

        $totalInvited = $validated['rsvp_status'] === 'attend'
            ? (int) $validated['total_invited']
            : 0;

        $guest->update([
            'rsvp_status' => $validated['rsvp_status'],
            'total_invited' => $totalInvited,
        ]);

        return redirect()
            ->route('invitation.guest', [
                'slug' => $correctGuestSlug,
                'code' => $guest->invitation_code,
            ])
            ->with('success', 'Terima kasih, konfirmasi kehadiran Anda sudah tersimpan.');
    }

    public function trackActivity(Request $request, string $slug, string $code)
    {
        $validated = $request->validate([
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'max_scroll_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $guest = Guest::with(['guestLink'])
            ->where('invitation_code', $code)
            ->firstOrFail();

        $correctGuestSlug = $this->makeGuestSlug($guest);

        if ($slug !== $correctGuestSlug) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid guest link.',
            ], 404);
        }

        $guestLink = $this->ensureGuestLink($guest);

        $sessionToken = $request->cookie('guest_invitation_session');

        if (! $sessionToken) {
            return response()->json([
                'ok' => false,
                'message' => 'Tracking session not found.',
            ]);
        }

        $deviceHash = hash('sha256', implode('|', [
            $guestLink->id,
            $sessionToken,
            $request->userAgent() ?: 'unknown-user-agent',
        ]));

        $session = GuestLinkSession::where('guest_link_id', $guestLink->id)
            ->where('device_hash', $deviceHash)
            ->first();

        if (! $session) {
            return response()->json([
                'ok' => false,
                'message' => 'Guest link session not found.',
            ]);
        }

        $durationSeconds = (int) ($validated['duration_seconds'] ?? 0);
        $maxScrollPercent = (int) ($validated['max_scroll_percent'] ?? 0);

        $session->update([
            'duration_seconds' => max((int) $session->duration_seconds, $durationSeconds),
            'max_scroll_percent' => max((int) $session->max_scroll_percent, $maxScrollPercent),
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    private function makeGuestSlug(Guest $guest): string
    {
        return $guest->guestLink?->guest_slug
            ?: (Str::slug($guest->name) ?: 'tamu-' . $guest->id);
    }

    private function ensureGuestLink(Guest $guest)
    {
        $guestSlug = Str::slug($guest->name) ?: 'tamu-' . $guest->id;

        $guestLink = $guest->guestLink()->updateOrCreate(
            [
                'guest_id' => $guest->id,
            ],
            [
                'wedding_profile_id' => $guest->wedding_profile_id,
                'guest_name' => $guest->name,
                'guest_slug' => $guestSlug,
                'token' => $guest->invitation_code,
                'is_active' => true,
                'device_warning_threshold' => 3,
            ]
        );

        $guest->setRelation('guestLink', $guestLink);

        return $guestLink;
    }

    private function trackGuestLinkOpen(Request $request, $guestLink)
    {
        $cookieName = 'guest_invitation_session';

        $sessionToken = $request->cookie($cookieName);

        if (! $sessionToken) {
            $sessionToken = Str::random(64);
        }

        $deviceHash = hash('sha256', implode('|', [
            $guestLink->id,
            $sessionToken,
            $request->userAgent() ?: 'unknown-user-agent',
        ]));

        $session = GuestLinkSession::firstOrNew([
            'guest_link_id' => $guestLink->id,
            'device_hash' => $deviceHash,
        ]);

        if ($session->exists) {
            $session->open_count = ((int) $session->open_count) + 1;
        } else {
            $session->session_token = $sessionToken;
            $session->opened_at = now();
            $session->open_count = 1;
        }

        $session->ip_address = $request->ip();
        $session->user_agent = $request->userAgent();
        $session->last_seen_at = now();
        $session->save();

        $openCount = (int) $guestLink->sessions()->sum('open_count');

        $uniqueDeviceCount = (int) $guestLink->sessions()
            ->distinct('device_hash')
            ->count('device_hash');

        $threshold = (int) ($guestLink->device_warning_threshold ?: 3);

        $isSuspectedShared = $uniqueDeviceCount > $threshold;

        $guestLink->update([
            'open_count' => $openCount,
            'unique_device_count' => $uniqueDeviceCount,
            'first_opened_at' => $guestLink->first_opened_at ?: now(),
            'last_opened_at' => now(),
            'is_suspected_shared' => $isSuspectedShared,
            'suspicion_reason' => $isSuspectedShared
                ? 'Link dibuka dari lebih dari ' . $threshold . ' perangkat berbeda.'
                : null,
        ]);

        return cookie(
            $cookieName,
            $sessionToken,
            60 * 24 * 180,
            null,
            null,
            false,
            true,
            false,
            'Lax'
        );
    }
}