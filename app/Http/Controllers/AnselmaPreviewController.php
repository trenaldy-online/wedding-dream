<?php

namespace App\Http\Controllers;

use App\Models\WeddingProfile;

class AnselmaPreviewController extends Controller
{
    public function show()
    {
        $profile = WeddingProfile::with(['events', 'sections'])
            ->firstOrCreate(
                ['id' => 1],
                [
                    'groom_name' => 'Ansel',
                    'bride_name' => 'Varo',
                    'slug' => 'ansel-dan-varo',
                    'venue_name' => 'The Langham Jakarta',
                    'venue_address' => 'District 8, SCBD, Jakarta Selatan',
                ]
            );

        $profile->ensureDefaultSections();

        $profile->load([
            'events' => function ($query) {
                $query->orderBy('event_date');
            },
            'sections' => function ($query) {
                $query->orderBy('sort_order');
            },
        ]);

        $guestName = 'Katsudoto';

        $primaryEvent = $profile->events->first();

        $couple = [
            'groom' => $profile->groom_name ?: 'Ansel',
            'bride' => $profile->bride_name ?: 'Varo',
            'hashtag' => '#AnselVaroInLove',
            'guest' => $guestName,
            'date' => $primaryEvent?->event_date
                ? $primaryEvent->event_date->translatedFormat('l, d F Y')
                : 'Saturday, January 31st 2026',
            'venue' => $primaryEvent?->venue_name ?: ($profile->venue_name ?: 'The Langham Jakarta'),
            'address' => $primaryEvent?->venue_address ?: ($profile->venue_address ?: 'District 8, SCBD, Jakarta Selatan'),
        ];

        $events = $profile->events
            ->map(function ($event) {
                return [
                    'side' => match ($event->event_side) {
                        'cpw' => 'Pihak CPW',
                        'cpp' => 'Pihak CPP',
                        default => 'Acara Bersama',
                    },
                    'name' => $event->event_name,
                    'date' => $event->event_date
                        ? $event->event_date->translatedFormat('l, d F Y')
                        : '',
                    'time' => $event->event_date
                        ? 'Pukul ' . $event->event_date->translatedFormat('H:i') . ' WIB'
                        : '',
                    'date_iso' => $event->event_date
                        ? $event->event_date->toIso8601String()
                        : null,
                    'venue' => $event->venue_name,
                    'address' => $event->venue_address,
                    'maps_url' => $event->maps_url ?? 'https://maps.google.com',
                ];
            })
            ->values()
            ->toArray();

        $sections = $profile->sections->keyBy('section_key');

        return view('templates.anselma.preview', compact(
            'profile',
            'couple',
            'events',
            'sections',
            'guestName'
        ));
    }
}