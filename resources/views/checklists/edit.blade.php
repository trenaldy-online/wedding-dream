@extends('layouts.app', ['title' => 'Edit Checklist'])

@push('styles')
@include('styles.form-edit')
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
@php
    $currentChecklistCategory = old('category', isset($checklist) ? ($checklist->category ?? '') : 'Persiapan');
    $isDocumentChecklist = in_array(strtolower(trim((string) $currentChecklistCategory)), ['dokumen', 'dokumen nikah'], true);
@endphp

<section class="checklist-page">
    <div class="checklist-overview-grid">
        <div class="checklist-hero-card">
            <div class="checklist-kicker">
                Edit Checklist
            </div>

            <h1 class="checklist-title">
                {{ $checklist->title }}
            </h1>

            <p class="checklist-subtitle">
                Perbarui data checklist, kategori, penanggung jawab, deadline, status, dan catatan.
            </p>

            <div class="checklist-progress-box">
                <div class="checklist-progress-header">
                    <div>
                        <div class="checklist-progress-label">Status Saat Ini</div>
                        <div class="checklist-progress-value">
                            {{ ($statusOptions ?? [])[$checklist->status] ?? 'Belum' }}
                        </div>
                    </div>

                    <div class="checklist-status-chip">
                        {{ strtoupper($checklist->assigned_to) }}
                    </div>
                </div>

                <div class="checklist-progress-track">
                    <div
                        class="checklist-progress-fill"
                        style="width: {{ $checklist->status === 'done' ? 100 : ($checklist->status === 'in_progress' ? 50 : 10) }}%;"
                    ></div>
                </div>
            </div>
        </div>

        <div class="checklist-side-card">
            <div class="checklist-side-title">
                Detail Singkat
            </div>

            <div class="checklist-side-list">
                <div class="checklist-side-item">
                    <span>Untuk</span>
                    <strong>
                        @if ($checklist->assigned_to === 'cpp')
                            CPP
                        @elseif ($checklist->assigned_to === 'cpw')
                            CPW
                        @else
                            Bersama
                        @endif
                    </strong>
                </div>

                <div class="checklist-side-item">
                    <span>Kategori</span>
                    <strong>{{ $checklist->category ?: 'Lainnya' }}</strong>
                </div>

                <div class="checklist-side-item">
                    <span>Prioritas</span>
                    <strong>{{ $isDocumentChecklist ? '-' : ($checklist->priority ?: 'Wajib') }}</strong>
                </div>

                <div class="checklist-side-item">
                    <span>Deadline</span>
                    <strong>
                        {{ $checklist->due_date ? $checklist->due_date->format('d/m/Y') : '-' }}
                    </strong>
                </div>

                <div class="checklist-side-item">
                    <span>Dibuat</span>
                    <strong>{{ $checklist->created_at->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="checklist-form-card">
        <div class="checklist-card-header">
            <div>
                <h2>Form Edit Checklist</h2>
                <p>Ubah data checklist sesuai kebutuhan persiapan pernikahan.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('checklists.update', $checklist) }}">
            @csrf
            @method('PUT')

            <div class="form-grid-1">
                <div class="form-group">
                    <label class="form-label">pilih event / acara tujuan checklist</label>

                    <select name="wedding_event_id" class="form-select">
                        <option value="">Checklist Umum</option>

                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ (string) old('wedding_event_id', $checklist->wedding_event_id) === (string) $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-help">
                        Pilih acara tertentu, atau biarkan Checklist Umum jika tidak khusus untuk satu acara.
                    </div>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Checklist</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="{{ old('title', $checklist->title) }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        list="checklist-category-options"
                        value="{{ old('category', $checklist->category ?: 'Persiapan') }}"
                        placeholder="Contoh: Persiapan, Dokumen Nikah, Vendor"
                    >

                    <datalist id="checklist-category-options">
                        @foreach (($categoryOptions ?? collect()) as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>

                    <div class="form-help">
                        Gunakan kategori <strong>Dokumen Nikah</strong> jika item ini adalah berkas/dokumen. Selain itu akan masuk ke Persiapan Nikah.
                    </div>
                </div>
            </div>

            <div class="form-grid-2" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">PIC / Penanggung Jawab?</label>
                    <select name="assigned_to" class="form-select">
                        <option value="both" {{ old('assigned_to', $checklist->assigned_to) === 'both' ? 'selected' : '' }}>
                            Bersama
                        </option>

                        <option value="cpp" {{ old('assigned_to', $checklist->assigned_to) === 'cpp' ? 'selected' : '' }}>
                            CPP - Calon Pengantin Pria
                        </option>

                        <option value="cpw" {{ old('assigned_to', $checklist->assigned_to) === 'cpw' ? 'selected' : '' }}>
                            CPW - Calon Pengantin Wanita
                        </option>
                    </select>
                </div>

                <div class="form-group js-priority-group" style="{{ $isDocumentChecklist ? 'display:none;' : '' }}">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-select" {{ $isDocumentChecklist ? 'disabled' : '' }}>
                        @foreach (($priorityOptions ?? ['Wajib' => 'Wajib']) as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', $checklist->priority ?: 'Wajib') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-help">
                        Pilihan dibuat sama dengan dropdown di Google Sheet: Wajib, Penting, Opsional, Bisa Ditunda.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach (($statusOptions ?? ['todo' => 'Belum']) as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $checklist->status) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <div class="form-help">
                        Status mengikuti sheet: Belum, Proses, Selesai, Ditunda, Batal.
                    </div>
                </div>
            </div>

            <div class="form-grid-1" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input
                        type="date"
                        name="due_date"
                        class="form-control"
                        value="{{ old('due_date', $checklist->due_date?->format('Y-m-d')) }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea
                        name="note"
                        class="form-control"
                        rows="5"
                        placeholder="Tambahkan catatan jika diperlukan"
                    >{{ old('note', $checklist->note) }}</textarea>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('checklists.index') }}" class="btn-soft-inline">
                    Kembali
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</section>

<script id="checklist-priority-toggle-script">
document.addEventListener('DOMContentLoaded', function () {
    const categoryFields = document.querySelectorAll('input[name="category"], select[name="category"]');

    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase()
            .replace(/[_-]+/g, ' ')
            .replace(/\s+/g, ' ');
    }

    function isDocumentCategory(value) {
        const text = normalize(value);

        return text === 'dokumen' || text === 'dokumen nikah';
    }

    categoryFields.forEach(function (categoryField) {
        const form = categoryField.closest('form') || document;
        const priorityField = form.querySelector('[name="priority"]');

        if (!priorityField) {
            return;
        }

        const priorityGroup = form.querySelector('.js-priority-group')
            || priorityField.closest('.form-group')
            || priorityField.closest('.mb-3')
            || priorityField.parentElement;

        function refreshPriorityVisibility() {
            const isDocument = isDocumentCategory(categoryField.value);

            if (priorityGroup) {
                priorityGroup.style.display = isDocument ? 'none' : '';
            }

            priorityField.disabled = isDocument;

            if (isDocument) {
                priorityField.value = '';
                return;
            }

            if (!priorityField.value) {
                priorityField.value = 'Wajib';
            }
        }

        categoryField.addEventListener('input', refreshPriorityVisibility);
        categoryField.addEventListener('change', refreshPriorityVisibility);

        refreshPriorityVisibility();
    });
});
</script>

@endsection