<?php

namespace App\Http\Controllers;

use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Http\Request;

class WeddingEventController extends Controller
{
    public function index()
    {
        $profile = WeddingProfile::first();

        $events = WeddingEvent::query()
            ->orderByRaw("
                CASE
                    WHEN event_side = 'both' THEN 1
                    WHEN event_side = 'cpw' THEN 2
                    WHEN event_side = 'cpp' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('event_date')
            ->get();

        return view('wedding_events.index', compact('profile', 'events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => ['required', 'string', 'max:255'],
            'event_side' => ['required', 'in:cpw,cpp,both'],
            'event_date' => ['nullable', 'date'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $profile = WeddingProfile::first();

        $validated['wedding_profile_id'] = $profile?->id;

        WeddingEvent::create($validated);

        return redirect()
            ->route('wedding-events.index')
            ->with('success', 'Acara berhasil ditambahkan.');
    }

    public function edit(WeddingEvent $weddingEvent)
    {
        return view('wedding_events.edit', compact('weddingEvent'));
    }

    public function update(Request $request, WeddingEvent $weddingEvent)
    {
        $validated = $request->validate([
            'event_name' => ['required', 'string', 'max:255'],
            'event_side' => ['required', 'in:cpw,cpp,both'],
            'event_date' => ['nullable', 'date'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $weddingEvent->update($validated);

        return redirect()
            ->route('wedding-events.index')
            ->with('success', 'Acara berhasil diperbarui.');
    }

    public function destroy(WeddingEvent $weddingEvent)
    {
        $weddingEvent->delete();

        return redirect()
            ->route('wedding-events.index')
            ->with('success', 'Acara berhasil dihapus.');
    }
}