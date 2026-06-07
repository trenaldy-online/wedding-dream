<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\Guest;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $profile = WeddingProfile::first();

        $totalEstimated = BudgetItem::sum('estimated_amount');
        $totalActual = BudgetItem::sum('actual_amount');
        $sisaBudget = $totalEstimated - $totalActual;

        $totalGuests = Guest::count();

        $totalInvitedPeople = Guest::where('rsvp_status', 'attend')
            ->sum('total_invited');

        $totalSent = Guest::whereNotNull('invitation_sent_at')
            ->count();

        $events = WeddingEvent::query()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get()
            ->map(function ($event) {
                $eventBudgetQuery = BudgetItem::where('wedding_event_id', $event->id);
                $eventGuestQuery = Guest::where('wedding_event_id', $event->id);

                $event->total_estimated = (clone $eventBudgetQuery)->sum('estimated_amount');
                $event->total_actual = (clone $eventBudgetQuery)->sum('actual_amount');
                $event->budget_remaining = $event->total_estimated - $event->total_actual;

                $event->total_budget_items = (clone $eventBudgetQuery)->count();
                $event->total_guests = (clone $eventGuestQuery)->count();

                $event->total_attending_people = (clone $eventGuestQuery)
                    ->where('rsvp_status', 'attend')
                    ->sum('total_invited');

                $event->total_sent = (clone $eventGuestQuery)
                    ->whereNotNull('invitation_sent_at')
                    ->count();

                $event->total_pending_rsvp = (clone $eventGuestQuery)
                    ->where('rsvp_status', 'pending')
                    ->count();

                return $event;
            });

        $nextEvent = WeddingEvent::query()
            ->whereNotNull('event_date')
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->first();

        if (! $nextEvent) {
            $nextEvent = WeddingEvent::query()
                ->whereNotNull('event_date')
                ->orderByDesc('event_date')
                ->first();
        }

        $sideSummaries = collect([
            [
                'key' => 'cpw',
                'label' => 'Pihak CPW',
                'description' => 'Khusus acara dari pihak calon pengantin wanita.',
                'class' => 'cpw',
            ],
            [
                'key' => 'cpp',
                'label' => 'Pihak CPP',
                'description' => 'Khusus acara dari pihak calon pengantin pria.',
                'class' => 'cpp',
            ],
            [
                'key' => 'both',
                'label' => 'Acara Bersama',
                'description' => 'Acara yang dikelola bersama oleh kedua pihak.',
                'class' => 'both',
            ],
        ])->map(function ($side) {
            $eventIds = WeddingEvent::where('event_side', $side['key'])
                ->pluck('id');

            $budgetQuery = BudgetItem::whereIn('wedding_event_id', $eventIds);
            $guestQuery = Guest::whereIn('wedding_event_id', $eventIds);

            $totalEstimated = (clone $budgetQuery)->sum('estimated_amount');
            $totalActual = (clone $budgetQuery)->sum('actual_amount');
            $remaining = $totalEstimated - $totalActual;

            $budgetPercent = $totalEstimated > 0
                ? min(100, round(($totalActual / $totalEstimated) * 100))
                : 0;

            return [
                'key' => $side['key'],
                'label' => $side['label'],
                'description' => $side['description'],
                'class' => $side['class'],
                'event_count' => $eventIds->count(),
                'total_estimated' => $totalEstimated,
                'total_actual' => $totalActual,
                'remaining' => $remaining,
                'budget_percent' => $budgetPercent,
                'total_guests' => (clone $guestQuery)->count(),
                'total_attending_people' => (clone $guestQuery)
                    ->where('rsvp_status', 'attend')
                    ->sum('total_invited'),
                'total_sent' => (clone $guestQuery)
                    ->whereNotNull('invitation_sent_at')
                    ->count(),
                'total_pending_rsvp' => (clone $guestQuery)
                    ->where('rsvp_status', 'pending')
                    ->count(),
            ];
        });

        return view('dashboard', compact(
            'profile',
            'events',
            'nextEvent',
            'sideSummaries',
            'totalEstimated',
            'totalActual',
            'sisaBudget',
            'totalGuests',
            'totalInvitedPeople',
            'totalSent'
        ));
    }
}