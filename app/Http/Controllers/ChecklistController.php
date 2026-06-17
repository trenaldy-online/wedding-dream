<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\WeddingEvent;
use App\Models\WeddingProfile;
use Illuminate\Http\Request;

class ChecklistController extends Controller
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

        $itemQuery = ChecklistItem::query();

        if ($selectedEventId === 'global') {
            $itemQuery->whereNull('wedding_event_id');
        } elseif ($selectedEventId) {
            $itemQuery->where('wedding_event_id', $selectedEventId);
        }

        $items = $itemQuery
            ->with('weddingEvent')
            ->orderByRaw("
                CASE
                    WHEN status = 'todo' THEN 1
                    WHEN status = 'in_progress' THEN 2
                    WHEN status = 'done' THEN 3
                    WHEN status = 'postponed' THEN 4
                    WHEN status = 'cancelled' THEN 5
                    ELSE 6
                END
            ")
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date', 'asc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summaryQuery = ChecklistItem::query();

        if ($selectedEventId === 'global') {
            $summaryQuery->whereNull('wedding_event_id');
        } elseif ($selectedEventId) {
            $summaryQuery->where('wedding_event_id', $selectedEventId);
        }

        $totalItems = (clone $summaryQuery)->count();
        $doneItems = (clone $summaryQuery)->where('status', 'done')->count();
        $inProgressItems = (clone $summaryQuery)->where('status', 'in_progress')->count();
        $todoItems = (clone $summaryQuery)->where('status', 'todo')->count();

        $cppItems = (clone $summaryQuery)->where('assigned_to', 'cpp')->count();
        $cpwItems = (clone $summaryQuery)->where('assigned_to', 'cpw')->count();
        $bothItems = (clone $summaryQuery)->where('assigned_to', 'both')->count();

        $progressPercent = $totalItems > 0
            ? min(100, round(($doneItems / $totalItems) * 100))
            : 0;

        $categoryOptions = $this->checklistCategoryOptions();
        $priorityOptions = $this->checklistPriorityOptions();
        $statusOptions = $this->checklistStatusOptions();

        return view('checklists.index', compact(
            'profile',
            'events',
            'selectedEventId',
            'items',
            'totalItems',
            'doneItems',
            'inProgressItems',
            'todoItems',
            'cppItems',
            'cpwItems',
            'bothItems',
            'progressPercent',
            'categoryOptions',
            'priorityOptions',
            'statusOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['nullable', 'exists:wedding_events,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'in:Wajib,Penting,Opsional,Bisa Ditunda'],
            'assigned_to' => ['required', 'in:cpp,cpw,both'],
            'status' => ['required', 'in:todo,in_progress,done,postponed,cancelled'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $validated['category'] = trim((string) ($validated['category'] ?? '')) ?: 'Persiapan';
        $validated['priority'] = $validated['priority'] ?? 'Wajib';

        $validated['category'] = trim((string) ($validated['category'] ?? '')) ?: 'Persiapan';
        $validated['priority'] = $validated['priority'] ?? 'Wajib';

        $validated['category'] = trim((string) ($validated['category'] ?? ''));

        if ($validated['category'] === '') {
            $validated['category'] = 'Persiapan';
        }

        if (in_array(strtolower($validated['category']), ['dokumen', 'dokumen nikah'], true)) {
            $validated['category'] = 'Dokumen Nikah';
        }

        $validated['priority'] = $validated['priority'] ?? 'Wajib';

        $profile = WeddingProfile::first();

        if (! empty($validated['wedding_event_id'])) {
            $event = WeddingEvent::findOrFail($validated['wedding_event_id']);
            $validated['wedding_profile_id'] = $event->wedding_profile_id;
        } else {
            $validated['wedding_profile_id'] = $profile?->id;
            $validated['wedding_event_id'] = null;
        }

        $validated['completed_at'] = $validated['status'] === 'done'
            ? now()
            : null;

        ChecklistItem::create($validated);

        return redirect()
            ->route('checklists.index', ['event_id' => $validated['wedding_event_id'] ?: 'global'])
            ->with('success', 'Checklist berhasil ditambahkan.');
    }

    public function edit(ChecklistItem $checklist)
    {
        $events = WeddingEvent::query()
            ->orderByRaw("CASE WHEN event_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('event_date')
            ->orderBy('event_name')
            ->get();

        $categoryOptions = $this->checklistCategoryOptions();
        $priorityOptions = $this->checklistPriorityOptions();
        $statusOptions = $this->checklistStatusOptions();

        return view('checklists.edit', compact(
            'checklist',
            'events',
            'categoryOptions',
            'priorityOptions',
            'statusOptions'
        ));
    }

    public function update(Request $request, ChecklistItem $checklist)
    {
        $validated = $request->validate([
            'wedding_event_id' => ['nullable', 'exists:wedding_events,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'in:Wajib,Penting,Opsional,Bisa Ditunda'],
            'assigned_to' => ['required', 'in:cpp,cpw,both'],
            'status' => ['required', 'in:todo,in_progress,done,postponed,cancelled'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $validated['category'] = trim((string) ($validated['category'] ?? ''));

        if ($validated['category'] === '') {
            $validated['category'] = 'Persiapan';
        }

        if (in_array(strtolower($validated['category']), ['dokumen', 'dokumen nikah'], true)) {
            $validated['category'] = 'Dokumen Nikah';
        }

        $validated['priority'] = $validated['priority'] ?? 'Wajib';

        $profile = WeddingProfile::first();

        if (! empty($validated['wedding_event_id'])) {
            $event = WeddingEvent::findOrFail($validated['wedding_event_id']);
            $validated['wedding_profile_id'] = $event->wedding_profile_id;
        } else {
            $validated['wedding_profile_id'] = $profile?->id;
            $validated['wedding_event_id'] = null;
        }

        $validated['completed_at'] = $validated['status'] === 'done'
            ? ($checklist->completed_at ?? now())
            : null;

        $checklist->update($validated);

        return redirect()
            ->route('checklists.index', ['event_id' => $validated['wedding_event_id'] ?: 'global'])
            ->with('success', 'Checklist berhasil diperbarui.');
    }

    public function toggle(ChecklistItem $checklist)
    {
        $isDone = $checklist->status === 'done';

        $checklist->update([
            'status' => $isDone ? 'todo' : 'done',
            'completed_at' => $isDone ? null : now(),
        ]);

        return redirect()
            ->route('checklists.index', ['event_id' => $checklist->wedding_event_id ?: 'global'])
            ->with('success', $isDone ? 'Checklist ditandai belum selesai.' : 'Checklist ditandai selesai.');
    }

    public function destroy(ChecklistItem $checklist)
    {
        $eventId = $checklist->wedding_event_id;

        $checklist->delete();

        return redirect()
            ->route('checklists.index', ['event_id' => $eventId ?: 'global'])
            ->with('success', 'Checklist berhasil dihapus.');
    }
    private function checklistCategoryOptions()
    {
        $defaults = collect([
            'Persiapan',
            'Dokumen Nikah',
            'Keluarga',
            'Busana',
            'Mahar',
            'Vendor',
            'Acara',
            'Kesehatan',
            'Lainnya',
        ]);

        $fromDatabase = ChecklistItem::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return $defaults
            ->merge($fromDatabase)
            ->filter()
            ->map(fn ($category) => trim((string) $category))
            ->unique()
            ->values();
    }

    private function checklistPriorityOptions(): array
    {
        return [
            'Wajib' => 'Wajib',
            'Penting' => 'Penting',
            'Opsional' => 'Opsional',
            'Bisa Ditunda' => 'Bisa Ditunda',
        ];
    }

    private function checklistStatusOptions(): array
    {
        return [
            'todo' => 'Belum',
            'in_progress' => 'Proses',
            'done' => 'Selesai',
            'postponed' => 'Ditunda',
            'cancelled' => 'Batal',
        ];
    }

    private function checklistStatusLabel(?string $status): string
    {
        return $this->checklistStatusOptions()[$status ?: 'todo'] ?? 'Belum';
    }


}
