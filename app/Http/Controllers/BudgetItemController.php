<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Http\Request;

class BudgetItemController extends Controller
{
    public function index(Request $request)
    {
        $profile = WeddingProfile::first();
        $events = WeddingEvent::orderBy('event_date')->get();

        $selectedEventId = $request->input('event_id');

        $baseQuery = BudgetItem::query();

        if ($selectedEventId) {
            $baseQuery->where('wedding_event_id', $selectedEventId);
        }

        $items = (clone $baseQuery)
            ->with('weddingEvent')
            ->orderByRaw("
                CASE
                    WHEN payment_status = 'unpaid' THEN 1
                    WHEN payment_status = 'partial' THEN 2
                    WHEN payment_status = 'paid' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $totalEstimated = (clone $baseQuery)->sum('estimated_amount');
        $totalActual = (clone $baseQuery)->sum('actual_amount');

        $totalItems = (clone $baseQuery)->count();
        $paidItems = (clone $baseQuery)->where('payment_status', 'paid')->count();
        $partialItems = (clone $baseQuery)->where('payment_status', 'partial')->count();
        $unpaidItems = (clone $baseQuery)->where('payment_status', 'unpaid')->count();

        return view('budget.index', compact(
            'profile',
            'events',
            'selectedEventId',
            'items',
            'totalEstimated',
            'totalActual',
            'totalItems',
            'paidItems',
            'partialItems',
            'unpaidItems'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['required', 'exists:wedding_events,id'],
            'category' => ['required', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'estimated_amount' => ['required', 'numeric', 'min:0'],
            'actual_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:unpaid,partial,paid'],
            'note' => ['nullable', 'string'],
        ]);

        $event = WeddingEvent::findOrFail($validated['wedding_event_id']);

        $validated['wedding_profile_id'] = $event->wedding_profile_id;
        $validated['actual_amount'] = $validated['actual_amount'] ?? 0;

        BudgetItem::create($validated);

        return redirect()
            ->route('budget-items.index', ['event_id' => $validated['wedding_event_id']])
            ->with('success', 'Item budget berhasil ditambahkan.');
    }

    public function edit(BudgetItem $budgetItem)
    {
        $profile = WeddingProfile::first();

        $events = WeddingEvent::query()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get();

        return view('budget.edit', compact('budgetItem', 'profile', 'events'));
    }

    public function update(Request $request, BudgetItem $budgetItem)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['required', 'exists:wedding_events,id'],
            'category' => ['required', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'estimated_amount' => ['required', 'numeric', 'min:0'],
            'actual_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:unpaid,partial,paid'],
            'note' => ['nullable', 'string'],
        ]);

        $event = WeddingEvent::findOrFail($validated['wedding_event_id']);

        $validated['wedding_profile_id'] = $event->wedding_profile_id;
        $validated['actual_amount'] = $validated['actual_amount'] ?? 0;

        $budgetItem->update($validated);

        return redirect()
            ->route('budget-items.index', ['event_id' => $validated['wedding_event_id']])
            ->with('success', 'Item budget berhasil diperbarui.');
    }

    public function destroy(BudgetItem $budgetItem)
    {
        $eventId = $budgetItem->wedding_event_id;

        $budgetItem->delete();

        return redirect()
            ->route('budget-items.index', ['event_id' => $eventId])
            ->with('success', 'Item budget berhasil dihapus.');
    }
}