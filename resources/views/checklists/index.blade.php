@extends('layouts.app', ['title' => 'Checklist CPW CPP'])

@push('styles')
<style>
/* CHECKLIST PAGE */

.checklist-page {
    display: flex;
    flex-direction: column;
    gap: 26px;
}

.checklist-overview-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 22px;
}

.checklist-hero-card,
.checklist-side-card,
.checklist-metric-card,
.checklist-form-card,
.checklist-table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 24px;
    box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
}

.checklist-hero-card {
    padding: 28px;
    background: linear-gradient(135deg, #fffdf8 0%, #fff6d7 100%);
}

.checklist-kicker {
    display: inline-block;
    background: rgba(255, 255, 255, 0.82);
    color: var(--gold-dark);
    font-size: 12px;
    font-weight: 900;
    padding: 8px 12px;
    border-radius: 999px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin-bottom: 14px;
}

.checklist-title {
    font-family: "Playfair Display", Georgia, serif;
    color: var(--navy);
    font-size: 42px;
    line-height: 1.1;
    margin: 0 0 12px;
}

.checklist-subtitle {
    color: var(--muted);
    line-height: 1.8;
    margin: 0;
    max-width: 760px;
}

.checklist-progress-box {
    margin-top: 24px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 18px;
}

.checklist-progress-header {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
    margin-bottom: 14px;
}

.checklist-progress-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.checklist-progress-value {
    color: var(--navy);
    font-size: 28px;
    font-weight: 900;
}

.checklist-status-chip {
    border-radius: 999px;
    padding: 9px 13px;
    font-size: 12px;
    font-weight: 900;
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.checklist-progress-track {
    width: 100%;
    height: 10px;
    background: #ebe7df;
    border-radius: 999px;
    overflow: hidden;
}

.checklist-progress-fill {
    height: 100%;
    background: var(--gold);
    border-radius: 999px;
}

.checklist-side-card {
    padding: 24px;
}

.checklist-side-title {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin-bottom: 18px;
}

.checklist-side-list {
    display: grid;
    gap: 12px;
}

.checklist-side-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fffdf8;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 14px 16px;
}

.checklist-side-item span {
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
}

.checklist-side-item strong {
    color: var(--navy);
    font-size: 18px;
}

.checklist-metric-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.checklist-metric-card {
    padding: 22px;
}

.checklist-metric-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.checklist-metric-value {
    color: var(--navy);
    font-size: 30px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 8px;
}

.checklist-metric-value.gold {
    color: var(--gold-dark);
}

.checklist-metric-value.green {
    color: #059669;
}

.checklist-metric-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.7;
}

.checklist-workspace-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
    align-items: start;
}

.checklist-form-card {
    padding: 24px;
}

.checklist-table-card {
    overflow: hidden;
}

.checklist-card-header {
    margin-bottom: 22px;
}

.checklist-card-header.table-header {
    padding: 24px 26px;
    margin-bottom: 0;
    border-bottom: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
}

.checklist-card-header h2 {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin: 0 0 6px;
}

.checklist-card-header p {
    color: var(--muted);
    margin: 0;
    line-height: 1.6;
    font-size: 14px;
}

.checklist-count-badge {
    background: var(--gold);
    color: white;
    border-radius: 10px;
    padding: 8px 12px;
    font-weight: 900;
    font-size: 13px;
    white-space: nowrap;
}

.checklist-submit-btn {
    width: 100%;
    height: 50px;
    border: 0;
    border-radius: 13px;
    background: var(--gold);
    color: white;
    font-weight: 900;
    cursor: pointer;
    box-shadow: 0 12px 24px rgba(216, 181, 50, 0.28);
}

.checklist-submit-btn:hover {
    background: var(--gold-dark);
}

.checklist-table-wrapper {
    overflow-x: auto;
}

.checklist-table {
    width: 100%;
    border-collapse: collapse;
}

.checklist-table thead {
    background: #fafafa;
}

.checklist-table th {
    text-align: left;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    padding: 16px 20px;
}

.checklist-table td {
    padding: 16px 20px;
    border-top: 1px solid var(--soft-border);
    vertical-align: middle;
}

.checklist-item-name {
    color: var(--navy);
    font-weight: 900;
    margin-bottom: 4px;
}

.checklist-item-note {
    color: var(--muted-light);
    font-size: 12px;
    line-height: 1.5;
    max-width: 280px;
}

.checklist-person-pill,
.checklist-category-pill,
.checklist-status-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 900;
    white-space: nowrap;
}

.checklist-person-pill.cpp {
    background: #dbeafe;
    color: #1e40af;
}

.checklist-person-pill.cpw {
    background: #fce7f3;
    color: #9d174d;
}

.checklist-person-pill.both {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.checklist-category-pill {
    background: #f3f4f6;
    color: var(--muted);
}

.checklist-status-pill.done {
    background: #dcfce7;
    color: #166534;
}

.checklist-status-pill.progress {
    background: #fef3c7;
    color: #92400e;
}

.checklist-status-pill.todo {
    background: #f3f4f6;
    color: #6b7280;
}

.checklist-muted {
    color: var(--muted-light);
}

.checklist-action-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.checklist-action-group form {
    margin: 0;
}

.checklist-action-group a.checklist-mini-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.checklist-mini-action {
    height: 34px;
    border: 0;
    border-radius: 10px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 900;
    cursor: pointer;
    white-space: nowrap;
}

.checklist-mini-action.edit {
    background: #fffdf7;
    border: 1px solid var(--border);
    color: var(--gold-dark);
}

.checklist-mini-action.edit:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.checklist-mini-action.mark {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.checklist-mini-action.mark:hover {
    background: #f4dc8a;
}

.checklist-mini-action.delete {
    background: #fee2e2;
    color: #991b1b;
}

.checklist-mini-action.delete:hover {
    background: #fecaca;
}

.checklist-empty-state {
    text-align: center;
    padding: 36px 20px;
    color: var(--muted);
}

.checklist-pagination {
    padding: 18px 22px 24px;
    border-top: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

@media (max-width: 1100px) {
    .checklist-overview-grid,
    .checklist-workspace-grid {
        grid-template-columns: 1fr;
    }

    .checklist-metric-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .checklist-title {
        font-size: 34px;
    }

    .checklist-progress-header,
    .checklist-card-header.table-header,
    .checklist-pagination {
        flex-direction: column;
        align-items: flex-start;
    }

    .checklist-table {
        min-width: 820px;
    }
}
</style>
@endpush

@section('content')
<section class="checklist-page">
    <div class="checklist-overview-grid">
        <div class="checklist-hero-card">
            <div class="checklist-kicker">
                Checklist CPW & CPP
            </div>

            <h1 class="checklist-title">
                Persiapan Calon Pengantin
            </h1>

            <p class="checklist-subtitle">
                Catat dokumen, kebutuhan pribadi, keluarga, vendor, dan persiapan acara
                untuk calon pengantin pria, calon pengantin wanita, maupun kebutuhan bersama.
            </p>

            <div class="checklist-progress-box">
                <div class="checklist-progress-header">
                    <div>
                        <div class="checklist-progress-label">Progress Checklist</div>
                        <div class="checklist-progress-value">{{ $progressPercent }}%</div>
                    </div>

                    <div class="checklist-status-chip">
                        {{ $doneItems }} dari {{ $totalItems }} selesai
                    </div>
                </div>

                <div class="checklist-progress-track">
                    <div class="checklist-progress-fill" style="width: {{ $progressPercent }}%;"></div>
                </div>
            </div>
        </div>

        <div class="checklist-side-card">
            <div class="checklist-side-title">
                Ringkasan
            </div>

            <div class="checklist-side-list">
                <div class="checklist-side-item">
                    <span>Total Checklist</span>
                    <strong>{{ $totalItems }}</strong>
                </div>

                <div class="checklist-side-item">
                    <span>Selesai</span>
                    <strong>{{ $doneItems }}</strong>
                </div>

                <div class="checklist-side-item">
                    <span>Dalam Proses</span>
                    <strong>{{ $inProgressItems }}</strong>
                </div>

                <div class="checklist-side-item">
                    <span>Belum Dikerjakan</span>
                    <strong>{{ $todoItems }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="checklist-metric-grid">
        <div class="checklist-metric-card">
            <div class="checklist-metric-label">Untuk CPP</div>
            <div class="checklist-metric-value">{{ $cppItems }}</div>
            <div class="checklist-metric-desc">
                Kebutuhan khusus calon pengantin pria.
            </div>
        </div>

        <div class="checklist-metric-card">
            <div class="checklist-metric-label">Untuk CPW</div>
            <div class="checklist-metric-value gold">{{ $cpwItems }}</div>
            <div class="checklist-metric-desc">
                Kebutuhan khusus calon pengantin wanita.
            </div>
        </div>

        <div class="checklist-metric-card">
            <div class="checklist-metric-label">Bersama</div>
            <div class="checklist-metric-value green">{{ $bothItems }}</div>
            <div class="checklist-metric-desc">
                Kebutuhan yang dikerjakan bersama.
            </div>
        </div>
    </div>

    <section class="guest-filter-card">
        <form method="GET" action="{{ route('checklists.index') }}" class="guest-filter-form">
            <div class="guest-filter-main" style="grid-template-columns: 1fr auto;">
                <div class="guest-filter-group">
                    <label class="form-label">Filter Acara</label>

                    <select name="event_id" class="form-select">
                        <option value="">Semua Checklist</option>
                        <option value="global" {{ request('event_id') === 'global' ? 'selected' : '' }}>
                            Checklist Umum
                        </option>

                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ (string) request('event_id') === (string) $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="guest-filter-actions">
                    <button class="guest-filter-btn" type="submit">
                        Filter
                    </button>

                    <a href="{{ route('checklists.index') }}" class="guest-reset-btn">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    <div class="checklist-workspace-grid">
        <div class="checklist-form-card">
            <div class="checklist-card-header">
                <div>
                    <h2>Tambah Checklist</h2>
                    <p>Masukkan kebutuhan persiapan CPW, CPP, atau bersama.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('checklists.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Acara</label>

                    <select name="wedding_event_id" class="form-select">
                        <option value="">Checklist Umum</option>

                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ (string) old('wedding_event_id', $selectedEventId !== 'global' ? $selectedEventId : '') === (string) $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-help">
                        Pilih acara tertentu, atau biarkan Checklist Umum jika tidak khusus untuk satu acara.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Checklist</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Contoh: KTP CPP"
                        value="{{ old('title') }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">-- Pilih Kategori --</option>

                        @foreach (['Dokumen', 'Keluarga', 'Busana', 'Mahar', 'Vendor', 'Acara', 'Kesehatan', 'Lainnya'] as $category)
                            <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Untuk Siapa?</label>
                    <select name="assigned_to" class="form-select">
                        <option value="both" {{ old('assigned_to') === 'both' ? 'selected' : '' }}>
                            Bersama
                        </option>
                        <option value="cpp" {{ old('assigned_to') === 'cpp' ? 'selected' : '' }}>
                            CPP - Calon Pengantin Pria
                        </option>
                        <option value="cpw" {{ old('assigned_to') === 'cpw' ? 'selected' : '' }}>
                            CPW - Calon Pengantin Wanita
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="todo" {{ old('status') === 'todo' ? 'selected' : '' }}>
                            Belum Dikerjakan
                        </option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>
                            Dalam Proses
                        </option>
                        <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>
                            Selesai
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Target Tanggal</label>
                    <input
                        type="date"
                        name="due_date"
                        class="form-control"
                        value="{{ old('due_date') }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea
                        name="note"
                        class="form-control"
                        rows="4"
                        placeholder="Contoh: file sudah disiapkan, tinggal print"
                    >{{ old('note') }}</textarea>
                </div>

                <button class="checklist-submit-btn" type="submit">
                    + Simpan Checklist
                </button>
            </form>
        </div>

        <div class="checklist-table-card">
            <div class="checklist-card-header table-header">
                <div>
                    <h2>Daftar Checklist</h2>
                    <p>Semua kebutuhan CPW, CPP, dan bersama.</p>
                </div>

                <div class="checklist-count-badge">
                    {{ $items->total() }} Item
                </div>
            </div>

            <div class="checklist-table-wrapper">
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th>Checklist</th>
                            <th>Acara</th>
                            <th>Untuk</th>
                            <th>Kategori</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <div class="checklist-item-name">
                                        {{ $item->title }}
                                    </div>

                                    @if ($item->note)
                                        <div class="checklist-item-note">
                                            {{ $item->note }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span class="checklist-category-pill">
                                        {{ $item->weddingEvent?->event_name ?: 'Umum' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($item->assigned_to === 'cpp')
                                        <span class="checklist-person-pill cpp">CPP</span>
                                    @elseif ($item->assigned_to === 'cpw')
                                        <span class="checklist-person-pill cpw">CPW</span>
                                    @else
                                        <span class="checklist-person-pill both">Bersama</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="checklist-category-pill">
                                        {{ $item->category ?: 'Lainnya' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($item->due_date)
                                        {{ $item->due_date->translatedFormat('d M Y') }}
                                    @else
                                        <span class="checklist-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($item->status === 'done')
                                        <span class="checklist-status-pill done">Selesai</span>
                                    @elseif ($item->status === 'in_progress')
                                        <span class="checklist-status-pill progress">Proses</span>
                                    @else
                                        <span class="checklist-status-pill todo">Belum</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="checklist-action-group">
                                        <a href="{{ route('checklists.edit', $item) }}" class="checklist-mini-action edit">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('checklists.toggle', $item) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button class="checklist-mini-action mark" type="submit">
                                                {{ $item->status === 'done' ? 'Batal' : 'Selesai' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('checklists.destroy', $item) }}" onsubmit="return confirm('Hapus checklist ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="checklist-mini-action delete" type="submit">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="checklist-empty-state">
                                    Belum ada checklist. Tambahkan checklist pertama dari form sebelah kiri.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="checklist-pagination">
                    <div class="pagination-info">
                        Menampilkan {{ $items->firstItem() }} - {{ $items->lastItem() }}
                        dari {{ $items->total() }} item
                    </div>

                    <div class="pagination-actions">
                        @if ($items->onFirstPage())
                            <span class="pagination-btn disabled">Sebelumnya</span>
                        @else
                            <a href="{{ $items->previousPageUrl() }}" class="pagination-btn">
                                Sebelumnya
                            </a>
                        @endif

                        <span class="pagination-current">
                            Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}
                        </span>

                        @if ($items->hasMorePages())
                            <a href="{{ $items->nextPageUrl() }}" class="pagination-btn">
                                Berikutnya
                            </a>
                        @else
                            <span class="pagination-btn disabled">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection