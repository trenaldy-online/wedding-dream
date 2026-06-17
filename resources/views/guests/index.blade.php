@extends('layouts.app', ['title' => 'Tamu Undangan'])

@push('styles')
<style>
/* GUEST PAGE */

.guest-summary-note {
    color: var(--muted);
    font-size: 13px;
    margin-top: 8px;
}

.guest-name-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.guest-avatar {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    background: var(--gold-soft);
    color: var(--gold-dark);
    display: grid;
    place-items: center;
    font-weight: 900;
    flex-shrink: 0;
}

.guest-name-main {
    font-weight: 800;
    color: var(--navy);
    margin-bottom: 4px;
}

.guest-name-sub {
    color: var(--muted-light);
    font-size: 13px;
}

.guest-group-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    background: #f3f4f6;
    color: var(--muted);
    padding: 7px 11px;
    font-size: 12px;
    font-weight: 700;
}

.rsvp-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 800;
}

.rsvp-pending {
    background: #f3f4f6;
    color: #6b7280;
}

.rsvp-attend {
    background: #dcfce7;
    color: #166534;
}

.rsvp-not-attend {
    background: #fee2e2;
    color: #991b1b;
}

.sent-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 800;
}

.sent-yes {
    background: #dcfce7;
    color: #166534;
}

.sent-no {
    background: #fef3c7;
    color: #92400e;
}

.guest-action-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.guest-form-note {
    background: var(--gold-soft);
    border: 1px solid var(--border);
    color: var(--navy);
    border-radius: 16px;
    padding: 16px;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 22px;
}

.guest-form-note strong {
    color: var(--gold-dark);
}

.guest-public-link {
    overflow-wrap: anywhere;
    color: var(--gold-dark);
    font-weight: 800;
}

@media (max-width: 700px) {
    .guest-action-group {
        align-items: flex-start;
        flex-direction: column;
    }
}

/* COMPACT GUEST TABLE */

.guest-table-compact .clean-table th {
    padding: 14px 18px;
    font-size: 11px;
    line-height: 1.2;
}

.guest-table-compact .clean-table td {
    padding: 14px 18px;
}

.guest-table-compact .guest-avatar {
    width: 34px;
    height: 34px;
    font-size: 16px;
}

.guest-table-compact .guest-name-cell {
    gap: 10px;
}

.guest-table-compact .guest-name-main {
    font-size: 15px;
    margin-bottom: 2px;
}

.guest-table-compact .guest-name-sub {
    font-size: 12px;
}

.guest-table-compact .guest-group-pill,
.guest-table-compact .rsvp-pill,
.guest-table-compact .sent-pill {
    padding: 6px 10px;
    font-size: 11px;
}

.compact-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.compact-actions form {
    margin: 0;
}

.mini-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 32px;
    padding: 0 10px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
}

.mini-edit {
    background: #fffdf7;
    border-color: var(--border);
    color: var(--gold-dark);
}

.mini-edit:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.mini-wa {
    background: #16a34a;
    color: white;
}

.mini-wa:hover {
    background: #15803d;
    color: white;
}

.mini-mark {
    background: #fffdf7;
    border-color: var(--border);
    color: var(--gold-dark);
}

.mini-mark:hover {
    background: var(--gold-soft);
}

.mini-delete {
    background: #fee2e2;
    color: #991b1b;
}

.mini-delete:hover {
    background: #fecaca;
}

@media (max-width: 900px) {
    .guest-table-compact .clean-table {
        min-width: 1050px;
    }
}

/* COMPACT STATS FOR GUEST PAGE */

.stats-grid.compact-stats {
    gap: 16px;
    margin-bottom: 24px;
}

.stats-grid.compact-stats .stat-card {
    padding: 18px 20px;
    min-height: auto;
}

.stats-grid.compact-stats .stat-value {
    font-size: 24px;
}

.stats-grid.compact-stats .stat-label {
    margin-bottom: 8px;
}

/* GUEST PAGINATION */

.guest-pagination-simple {
    padding: 18px 22px 24px;
    border-top: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.pagination-info {
    color: var(--muted);
    font-size: 13px;
    font-weight: 600;
}

.pagination-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 34px;
    padding: 0 12px;
    border-radius: 10px;
    background: #fffdf7;
    border: 1px solid var(--border);
    color: var(--gold-dark);
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
}

.pagination-btn:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.pagination-btn.disabled {
    color: var(--muted-light);
    background: #f3f4f6;
    cursor: not-allowed;
}

.pagination-current {
    font-size: 13px;
    color: var(--muted);
    font-weight: 700;
}

/* GUEST FILTER */

.guest-filter-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 22px;
    padding: 20px;
    box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
    margin-bottom: 24px;
}

.guest-filter-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.guest-filter-main {
    display: grid;
    grid-template-columns: 1.7fr 1fr 1fr 1fr 1fr 0.7fr;
    gap: 14px;
    align-items: end;
}

.guest-filter-group {
    min-width: 0;
}

.guest-filter-group .form-label {
    margin-bottom: 7px;
    font-size: 12px;
}

.guest-filter-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.guest-filter-btn,
.guest-reset-btn {
    height: 40px;
    border-radius: 11px;
    padding: 0 16px;
    font-size: 13px;
    font-weight: 900;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.guest-filter-btn {
    border: 0;
    background: var(--gold);
    color: white;
    cursor: pointer;
}

.guest-filter-btn:hover {
    background: var(--gold-dark);
}

.guest-reset-btn {
    border: 1px solid var(--border);
    background: #fffdf7;
    color: var(--gold-dark);
}

.guest-reset-btn:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.guest-filter-result {
    margin-top: 14px;
    color: var(--muted);
    font-size: 13px;
}

.guest-filter-result strong {
    color: var(--navy);
}

@media (max-width: 1100px) {
    .guest-filter-main {
        grid-template-columns: repeat(2, 1fr);
    }

    .guest-filter-group.search {
        grid-column: span 2;
    }
}

@media (max-width: 700px) {
    .guest-filter-main {
        grid-template-columns: 1fr;
    }

    .guest-filter-group.search {
        grid-column: span 1;
    }

    .guest-filter-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .guest-filter-btn,
    .guest-reset-btn {
        width: 100%;
    }
}


/* GUEST LIFECYCLE PREVIEW */

.guest-note-preview {
    margin-top: 4px;
    color: #9ca3af;
    font-size: 11px;
    line-height: 1.35;
    max-width: 240px;
}

.guest-mini-stack {
    display: flex;
    flex-direction: column;
    gap: 7px;
    min-width: 150px;
}

.lifecycle-preview-box {
    min-width: 145px;
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.lifecycle-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 12px;
    line-height: 1.25;
}

.lifecycle-label {
    color: #6b7280;
    font-weight: 700;
}

.lifecycle-value {
    color: #111827;
    font-weight: 900;
    white-space: nowrap;
}

.lifecycle-status-grid {
    min-width: 190px;
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
}

.lifecycle-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    padding: 6px 9px;
    font-size: 11px;
    font-weight: 900;
    line-height: 1.1;
    white-space: nowrap;
}

.lifecycle-pill-muted {
    background: #f3f4f6;
    color: #6b7280;
}

.lifecycle-pill-green {
    background: #dcfce7;
    color: #166534;
}

.lifecycle-pill-yellow {
    background: #fef3c7;
    color: #92400e;
}

.lifecycle-pill-red {
    background: #fee2e2;
    color: #991b1b;
}

.lifecycle-pill-blue {
    background: #dbeafe;
    color: #1d4ed8;
}

.lifecycle-money {
    width: 100%;
    color: #6b7280;
    font-size: 12px;
    font-weight: 800;
    margin-top: 2px;
}

.link-track-box {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 135px;
}

.link-track-line {
    font-size: 12px;
    color: var(--muted);
    font-weight: 700;
}

.link-track-line strong {
    color: var(--navy);
}

.link-warning-pill {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    border-radius: 8px;
    padding: 6px 9px;
    font-size: 11px;
    font-weight: 900;
}

.link-warning-normal {
    background: #dcfce7;
    color: #166534;
}

.link-warning-danger {
    background: #fee2e2;
    color: #991b1b;
}

.tracking-detail-btn {
    border: none;
    border-radius: 8px;
    padding: 6px 9px;
    font-size: 11px;
    font-weight: 900;
    cursor: pointer;
    background: #eef2ff;
    color: #3730a3;
    width: fit-content;
}

.tracking-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.55);
}

.tracking-modal-overlay.is-open {
    display: flex;
}

.tracking-modal-card {
    width: min(920px, 100%);
    max-height: 86vh;
    overflow: hidden;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.25);
}

.tracking-modal-head {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: flex-start;
    padding: 18px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.tracking-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
    color: var(--navy);
}

.tracking-modal-subtitle {
    margin-top: 4px;
    font-size: 13px;
    color: var(--muted);
}

.tracking-modal-close {
    border: none;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    cursor: pointer;
    background: #f1f5f9;
    color: #0f172a;
    font-size: 20px;
    line-height: 1;
}

.tracking-modal-body {
    padding: 18px 20px;
    overflow: auto;
    max-height: calc(86vh - 78px);
}

.tracking-session-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.tracking-session-table th {
    text-align: left;
    padding: 10px;
    background: #f8fafc;
    color: #475569;
    font-size: 12px;
    font-weight: 900;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.tracking-session-table td {
    padding: 10px;
    border-bottom: 1px solid #eef2f7;
    vertical-align: top;
    color: #334155;
}

.tracking-user-agent {
    max-width: 280px;
    word-break: break-word;
    color: #64748b;
    font-size: 12px;
    line-height: 1.45;
}

.tracking-empty {
    padding: 18px;
    border-radius: 14px;
    background: #f8fafc;
    color: #64748b;
    font-size: 13px;
    font-weight: 700;
}

.tracking-reset-btn {
    border: none;
    border-radius: 9px;
    padding: 8px 11px;
    font-size: 12px;
    font-weight: 900;
    cursor: pointer;
    background: #fee2e2;
    color: #991b1b;
}

.tracking-reset-btn:hover {
    background: #fecaca;
}

.stat-value.red {
    color: #dc2626;
}

.tracking-stats {
    margin-top: -10px;
}
</style>
@endpush

@section('content')
@php
    $totalPendingSent = $totalGuests - $totalSent;

    $sentPercent = $totalGuests > 0
        ? min(100, round(($totalSent / $totalGuests) * 100))
        : 0;

    $publicUrl = $profile
        ? route('invitation.show', $profile->slug)
        : null;

    $defaultGroups = collect(['Keluarga', 'Teman', 'Kantor', 'Tetangga']);
    $groupOptions = $defaultGroups
        ->merge($groups ?? collect())
        ->filter()
        ->unique()
        ->values();
@endphp

<section class="page-heading">
    <div>
        <h1 class="page-title">
            Kelola Tamu Undangan
        </h1>

        <div class="page-date">
            <span>✦</span>
            Simpan daftar tamu, grup tamu, dan kirim undangan digital melalui WhatsApp.
        </div>
    </div>

    <div class="budget-status">
        <div class="budget-status-label">
            Status Pengiriman
        </div>

        <div class="budget-status-row">
            <div class="budget-status-track">
                <div class="budget-status-fill" style="width: {{ $sentPercent }}%;"></div>
            </div>

            <div class="budget-status-number">
                {{ $sentPercent }}%
            </div>
        </div>
    </div>
</section>

@if (! $profile)
    <div class="alert alert-warning">
        Profil undangan belum dibuat. Buat dulu di menu
        <a href="{{ route('profile.edit') }}">Undangan Digital</a>
        supaya link WhatsApp bisa otomatis dibuat.
    </div>
@endif

<section class="stats-grid compact-stats">
    <div class="stat-card">
        <div class="stat-label">Total Nama Tamu</div>
        <div class="stat-value">
            {{ $totalGuests }}
        </div>
        <div class="guest-summary-note">
            Jumlah baris tamu yang tersimpan.
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Orang Konfirmasi Hadir</div>
        <div class="stat-value gold">
            {{ $totalInvitedPeople }}
        </div>
        <div class="guest-summary-note">
            Berdasarkan jumlah orang yang diisi saat RSVP.
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Undangan Terkirim</div>
        <div class="stat-value green">
            {{ $totalSent }}
        </div>
        <div class="guest-summary-note">
            Sisa belum dikirim: {{ $totalPendingSent }} tamu.
        </div>
    </div>
</section>

<section class="stats-grid compact-stats tracking-stats">
    <div class="stat-card">
        <div class="stat-label">Link Sudah Dibuka</div>
        <div class="stat-value green">
            {{ $totalOpenedLinks ?? 0 }}
        </div>
        <div class="guest-summary-note">
            Tamu yang sudah membuka link personal.
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Link Belum Dibuka</div>
        <div class="stat-value">
            {{ $totalUnopenedLinks ?? 0 }}
        </div>
        <div class="guest-summary-note">
            Tamu yang belum membuka undangan.
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Warning Device</div>
        <div class="stat-value red">
            {{ $totalWarningLinks ?? 0 }}
        </div>
        <div class="guest-summary-note">
            Link terindikasi dibuka dari banyak device.
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Rata-rata Device</div>
        <div class="stat-value gold">
            {{ $averageDeviceCount ?? 0 }}
        </div>
        <div class="guest-summary-note">
            Rata-rata device unik per link tamu.
        </div>
    </div>
</section>

<section class="guest-filter-card">
    <form method="GET" action="{{ route('guests.index') }}" class="guest-filter-form">
        <div class="guest-filter-main">
            <div class="guest-filter-group search">
                <label class="form-label">Cari Tamu</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cari nama, nomor WA, atau alamat"
                    value="{{ request('search') }}"
                >
            </div>

            <div class="guest-filter-group">
                <label class="form-label">Acara</label>
                <select name="event_id" class="form-select">
                    <option value="">Semua Acara</option>

                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" {{ (string) request('event_id') === (string) $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="guest-filter-group">
                <label class="form-label">Grup</label>
                <select name="group_name" class="form-select">
                    <option value="">Semua Grup</option>

                    @foreach ($groups as $group)
                        <option value="{{ $group }}" {{ request('group_name') === $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="guest-filter-group">
                <label class="form-label">RSVP</label>
                <select name="rsvp_status" class="form-select">
                    <option value="">Semua RSVP</option>

                    <option value="pending" {{ request('rsvp_status') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>

                    <option value="attend" {{ request('rsvp_status') === 'attend' ? 'selected' : '' }}>
                        Hadir
                    </option>

                    <option value="not_attend" {{ request('rsvp_status') === 'not_attend' ? 'selected' : '' }}>
                        Tidak Hadir
                    </option>
                </select>
            </div>

            <div class="guest-filter-group">
                <label class="form-label">Status Kirim</label>
                <select name="sent_status" class="form-select">
                    <option value="">Semua Status</option>

                    <option value="sent" {{ request('sent_status') === 'sent' ? 'selected' : '' }}>
                        Terkirim
                    </option>

                    <option value="not_sent" {{ request('sent_status') === 'not_sent' ? 'selected' : '' }}>
                        Belum Dikirim
                    </option>
                </select>
            </div>

            <div class="guest-filter-group">
                <label class="form-label">Tracking</label>
                <select name="tracking_status" class="form-select">
                    <option value="">Semua Tracking</option>

                    <option value="warning" {{ request('tracking_status') === 'warning' ? 'selected' : '' }}>
                        Warning Only
                    </option>
                </select>
            </div>

            <div class="guest-filter-group small">
                <label class="form-label">Per Page</label>
                <select name="per_page" class="form-select">
                    @foreach ([10, 25, 50] as $number)
                        <option value="{{ $number }}" {{ (int) request('per_page', $perPage) === $number ? 'selected' : '' }}>
                            {{ $number }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="guest-filter-actions">
            <button class="guest-filter-btn" type="submit">
                Filter
            </button>

            <a href="{{ route('guests.index') }}" class="guest-reset-btn">
                Reset
            </a>
        </div>
    </form>

    <div class="guest-filter-result">
        Menampilkan <strong>{{ $totalFiltered }}</strong> hasil dari total <strong>{{ $totalGuests }}</strong> tamu.
    </div>
</section>

<section class="dashboard-grid">
    <div class="panel">
        <div class="panel-body">
            <h2 class="panel-title">Tambah Tamu</h2>

            <div class="guest-form-note" style="margin-top: 24px;">
                <strong>Tips:</strong> admin cukup mengisi nama, nomor WhatsApp, grup, dan alamat.
                Jumlah orang yang hadir akan diisi langsung oleh tamu saat melakukan RSVP.
            </div>

            <form method="POST" action="{{ route('guests.store') }}">
                @csrf

                {{-- Default sistem 2: RSVP dari tamu --}}
                <input type="hidden" name="rsvp_status" value="pending">
                <input type="hidden" name="rsvp_count" value="0">

                {{-- Default sistem 3: hari-H / setelah acara --}}
                <input type="hidden" name="invitation_status" value="pending">
                <input type="hidden" name="attendance_status" value="not_arrived">
                <input type="hidden" name="actual_attendance_count" value="0">
                <input type="hidden" name="envelope_amount" value="0">
                <input type="hidden" name="souvenir_status" value="not_given">
                <input type="hidden" name="souvenir_count" value="0">

                <div class="form-group">
                    <label class="form-label">Acara</label>
                    <select name="wedding_event_id" class="form-select">
                        <option value="">-- Pilih Acara --</option>

                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ (string) old('wedding_event_id', $selectedEventId) === (string) $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Tamu</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Contoh: Bapak Ahmad"
                        value="{{ old('name') }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        placeholder="Contoh: 08123456789"
                        value="{{ old('phone') }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Grup Tamu</label>

                    <input
                        type="text"
                        name="group_name"
                        class="form-control"
                        list="guest-group-options"
                        placeholder="Contoh: Keluarga, Teman, Kantor, Saudara Ibu"
                        value="{{ old('group_name') }}"
                    >

                    <datalist id="guest-group-options">
                        @foreach ($groupOptions as $group)
                            <option value="{{ $group }}"></option>
                        @endforeach
                    </datalist>

                    <div class="form-help">
                        Kamu bisa pilih dari saran atau mengetik grup baru secara manual.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Undangan</label>
                    <input
                        type="number"
                        name="total_invited"
                        class="form-control"
                        min="1"
                        value="{{ old('total_invited', 1) }}"
                    >
                    <div class="form-help">
                        Kuota maksimal orang yang diundang. Estimasi hadir akan diisi saat tamu RSVP.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea
                        name="address"
                        class="form-control"
                        rows="4"
                        placeholder="Opsional"
                    >{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea
                        name="sync_note"
                        class="form-control"
                        rows="3"
                        placeholder="Opsional, misalnya keluarga dekat, perlu follow up, dan sebagainya"
                    >{{ old('sync_note') }}</textarea>
                </div>

                <button class="btn-primary" type="submit">
                    + Simpan Tamu
                </button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Daftar Tamu</h2>

                @if ($publicUrl)
                    <div class="guest-summary-note" style="margin-top: 8px;">
                        Link undangan umum:
                        <a href="{{ $publicUrl }}" target="_blank" class="guest-public-link">
                            {{ $publicUrl }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="item-badge">
                {{ $totalFiltered }} Hasil
            </div>
        </div>

        <div class="table-wrapper guest-table-compact">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Nama Tamu</th>
                        <th>Acara & Grup</th>
                        <th>Kuota & Kehadiran</th>
                        <th>Status</th>
                        <th>Tracking Link</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($guests as $guest)
                        @php
                            $initial = strtoupper(substr($guest->name, 0, 1));

                            $guestInviteUrl = null;

                            if ($profile) {
                                $guestInviteUrl = $guest->guestLink?->public_url;

                                if (! $guestInviteUrl && $guest->invitation_code) {
                                    $guestInviteUrl = route('invitation.guest', [
                                        'slug' => \Illuminate\Support\Str::slug($guest->name) ?: 'tamu-' . $guest->id,
                                        'code' => $guest->invitation_code,
                                    ]);
                                }
                            }

                            $messageUrl = $guestInviteUrl ?: $publicUrl;

                            $message = $messageUrl
                                ? "Assalamu'alaikum {$guest->name}, kami mengundang Bapak/Ibu/Saudara/i untuk hadir dalam acara pernikahan {$profile->groom_name} dan {$profile->bride_name}. Silakan buka undangan digital pribadi berikut: {$messageUrl}"
                                : "Assalamu'alaikum {$guest->name}, berikut undangan digital kami.";

                            $waLink = $guest->formatted_phone
                                ? 'https://wa.me/' . $guest->formatted_phone . '?text=' . urlencode($message)
                                : null;
                        @endphp

                        <tr>
                            <td>
                                <div class="guest-name-cell">
                                    <div class="guest-avatar">
                                        {{ $initial }}
                                    </div>

                                    <div>
                                        <div class="guest-name-main">
                                            {{ $guest->name }}
                                        </div>

                                        <div class="guest-name-sub">
                                            {{ $guest->phone ?: 'Nomor belum diisi' }}
                                        </div>

                                        @if ($guest->sync_note)
                                            <div class="guest-note-preview" title="{{ $guest->sync_note }}">
                                                {{ \Illuminate\Support\Str::limit($guest->sync_note, 65) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="guest-mini-stack">
                                    <span class="guest-group-pill">
                                        {{ $guest->weddingEvent?->event_name ?: 'Belum ada acara' }}
                                    </span>

                                    <span class="guest-group-pill">
                                        {{ $guest->group_name ?: 'Tanpa Grup' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                <div class="lifecycle-preview-box">
                                    <div class="lifecycle-line">
                                        <span class="lifecycle-label">Kuota</span>
                                        <span class="lifecycle-value">{{ $guest->total_invited ?? 1 }} org</span>
                                    </div>

                                    <div class="lifecycle-line">
                                        <span class="lifecycle-label">RSVP</span>
                                        <span class="lifecycle-value">{{ $guest->rsvp_count ?? 0 }} org</span>
                                    </div>

                                    <div class="lifecycle-line">
                                        <span class="lifecycle-label">Hadir</span>
                                        <span class="lifecycle-value">{{ $guest->actual_attendance_count ?? 0 }} org</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @php
                                    $attendanceStatus = $guest->attendance_status ?? 'not_arrived';
                                    $souvenirStatus = $guest->souvenir_status ?? 'not_given';
                                    $envelopeAmount = (int) ($guest->envelope_amount ?? 0);
                                @endphp

                                <div class="lifecycle-status-grid">
                                    @if ($guest->rsvp_status === 'attend')
                                        <span class="lifecycle-pill lifecycle-pill-green">RSVP: Hadir</span>
                                    @elseif ($guest->rsvp_status === 'not_attend')
                                        <span class="lifecycle-pill lifecycle-pill-red">RSVP: Tidak</span>
                                    @else
                                        <span class="lifecycle-pill lifecycle-pill-muted">RSVP: Belum</span>
                                    @endif

                                    @if ($guest->invitation_sent_at)
                                        <span class="lifecycle-pill lifecycle-pill-green">Undangan: Terkirim</span>
                                    @else
                                        <span class="lifecycle-pill lifecycle-pill-yellow">Undangan: Belum</span>
                                    @endif

                                    @if ($attendanceStatus === 'arrived')
                                        <span class="lifecycle-pill lifecycle-pill-blue">Hari-H: Hadir</span>
                                    @else
                                        <span class="lifecycle-pill lifecycle-pill-muted">Hari-H: Belum</span>
                                    @endif

                                    @if ($souvenirStatus === 'given')
                                        <span class="lifecycle-pill lifecycle-pill-green">Souvenir: Sudah</span>
                                    @else
                                        <span class="lifecycle-pill lifecycle-pill-muted">Souvenir: Belum</span>
                                    @endif

                                    <div class="lifecycle-money">
                                        Amplop: Rp {{ number_format($envelopeAmount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </td>

                            @php
                                $guestLink = $guest->guestLink;

                                $linkOpenCount = $guestLink?->open_count ?? 0;
                                $linkDeviceCount = $guestLink?->unique_device_count ?? 0;
                                $isSuspectedShared = (bool) ($guestLink?->is_suspected_shared ?? false);
                                $suspicionReason = $guestLink?->suspicion_reason;

                                $trackingSessions = $guestLink?->sessions ?? collect();

                                $maxDurationSeconds = (int) ($trackingSessions->max('duration_seconds') ?? 0);
                                $maxScrollPercent = (int) ($trackingSessions->max('max_scroll_percent') ?? 0);

                                $durationMinute = intdiv($maxDurationSeconds, 60);
                                $durationSecond = $maxDurationSeconds % 60;

                                if ($maxDurationSeconds <= 0) {
                                    $durationText = '-';
                                } elseif ($durationMinute > 0) {
                                    $durationText = $durationMinute . 'm ' . $durationSecond . 'd';
                                } else {
                                    $durationText = $durationSecond . 'd';
                                }

                                $trackingModalId = 'tracking-modal-' . $guest->id;
                            @endphp

                            <td>
                                <div class="link-track-box">
                                    <div class="link-track-line">
                                        Dibuka: <strong>{{ $linkOpenCount }}x</strong>
                                    </div>

                                    <div class="link-track-line">
                                        Device: <strong>{{ $linkDeviceCount }}</strong>
                                    </div>

                                    <div class="link-track-line">
                                        Durasi: <strong>{{ $durationText }}</strong>
                                    </div>

                                    <div class="link-track-line">
                                        Scroll: <strong>{{ $maxScrollPercent }}%</strong>
                                    </div>

                                    @if ($isSuspectedShared)
                                        <span class="link-warning-pill link-warning-danger" title="{{ $suspicionReason }}">
                                            Warning
                                        </span>
                                    @else
                                        <span class="link-warning-pill link-warning-normal">
                                            Normal
                                        </span>
                                    @endif

                                    <button
                                        type="button"
                                        class="tracking-detail-btn"
                                        data-tracking-modal="{{ $trackingModalId }}"
                                    >
                                        Detail
                                    </button>
                                </div>

                                <div class="tracking-modal-overlay" id="{{ $trackingModalId }}">
                                    <div class="tracking-modal-card">
                                        <div class="tracking-modal-head">
                                            <div>
                                                <h3 class="tracking-modal-title">
                                                    Detail Tracking: {{ $guest->name }}
                                                </h3>

                                                <div class="tracking-modal-subtitle">
                                                    Total dibuka {{ $linkOpenCount }}x dari {{ $linkDeviceCount }} device.
                                                </div>

                                                <form
                                                    method="POST"
                                                    action="{{ route('guests.resetTracking', $guest) }}"
                                                    onsubmit="return confirm('Reset tracking link untuk tamu ini? Data open count, device, durasi, dan scroll akan dihapus.')"
                                                    style="margin-top: 10px;"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    @foreach (request()->query() as $key => $value)
                                                        @if (is_array($value))
                                                            @foreach ($value as $childValue)
                                                                <input type="hidden" name="{{ $key }}[]" value="{{ $childValue }}">
                                                            @endforeach
                                                        @else
                                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                                        @endif
                                                    @endforeach

                                                    <button type="submit" class="tracking-reset-btn">
                                                        Reset Tracking
                                                    </button>
                                                </form>
                                            </div>

                                            <button
                                                type="button"
                                                class="tracking-modal-close"
                                                data-tracking-close
                                                aria-label="Tutup"
                                            >
                                                ×
                                            </button>
                                        </div>

                                        <div class="tracking-modal-body">
                                            @if ($trackingSessions->count())
                                                <table class="tracking-session-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Device</th>
                                                            <th>IP</th>
                                                            <th>Dibuka</th>
                                                            <th>Durasi</th>
                                                            <th>Scroll</th>
                                                            <th>Pertama</th>
                                                            <th>Terakhir</th>
                                                            <th>User Agent</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($trackingSessions->sortByDesc('last_seen_at') as $session)
                                                            @php
                                                                $sessionDuration = (int) ($session->duration_seconds ?? 0);
                                                                $sessionMinute = intdiv($sessionDuration, 60);
                                                                $sessionSecond = $sessionDuration % 60;

                                                                if ($sessionDuration <= 0) {
                                                                    $sessionDurationText = '-';
                                                                } elseif ($sessionMinute > 0) {
                                                                    $sessionDurationText = $sessionMinute . 'm ' . $sessionSecond . 'd';
                                                                } else {
                                                                    $sessionDurationText = $sessionSecond . 'd';
                                                                }
                                                            @endphp

                                                            <tr>
                                                                <td>
                                                                    Device {{ $loop->iteration }}
                                                                </td>

                                                                <td>
                                                                    {{ $session->ip_address ?? '-' }}
                                                                </td>

                                                                <td>
                                                                    {{ $session->open_count ?? 0 }}x
                                                                </td>

                                                                <td>
                                                                    {{ $sessionDurationText }}
                                                                </td>

                                                                <td>
                                                                    {{ $session->max_scroll_percent ?? 0 }}%
                                                                </td>

                                                                <td>
                                                                    {{ $session->opened_at ? $session->opened_at->format('d M Y H:i') : '-' }}
                                                                </td>

                                                                <td>
                                                                    {{ $session->last_seen_at ? $session->last_seen_at->format('d M Y H:i') : '-' }}
                                                                </td>

                                                                <td>
                                                                    <div class="tracking-user-agent">
                                                                        {{ $session->user_agent ?? '-' }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="tracking-empty">
                                                    Link belum pernah dibuka oleh tamu.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="guest-action-group compact-actions">
                                    <a href="{{ route('guests.edit', $guest) }}" class="mini-action mini-edit">
                                        Edit
                                    </a>

                                    @if ($guestInviteUrl)
                                        <a href="{{ $guestInviteUrl }}" target="_blank" class="mini-action mini-edit">
                                            Link
                                        </a>
                                    @endif

                                    @if ($waLink)
                                        <a href="{{ $waLink }}" target="_blank" class="mini-action mini-wa">
                                            Kirim WA
                                        </a>

                                        @if (! $guest->invitation_sent_at)
                                            <form method="POST" action="{{ route('guests.markSent', $guest) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button class="mini-action mini-mark" type="submit">
                                                    Sudah Dikirim
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm('Hapus tamu ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="mini-action mini-delete" type="submit">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                Belum ada data tamu. Tambahkan tamu pertama dari form di sebelah kiri.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($guests->hasPages())
            <div class="guest-pagination-simple">
                <div class="pagination-info">
                    Menampilkan {{ $guests->firstItem() }} - {{ $guests->lastItem() }}
                    dari {{ $guests->total() }} tamu
                </div>

                <div class="pagination-actions">
                    @if ($guests->onFirstPage())
                        <span class="pagination-btn disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $guests->previousPageUrl() }}" class="pagination-btn">
                            Sebelumnya
                        </a>
                    @endif

                    <span class="pagination-current">
                        Halaman {{ $guests->currentPage() }} dari {{ $guests->lastPage() }}
                    </span>

                    @if ($guests->hasMorePages())
                        <a href="{{ $guests->nextPageUrl() }}" class="pagination-btn">
                            Berikutnya
                        </a>
                    @else
                        <span class="pagination-btn disabled">Berikutnya</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

<script>
    document.addEventListener('click', function (event) {
        const openButton = event.target.closest('[data-tracking-modal]');

        if (openButton) {
            const modalId = openButton.getAttribute('data-tracking-modal');
            const modal = document.getElementById(modalId);

            if (modal) {
                modal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            return;
        }

        const closeButton = event.target.closest('[data-tracking-close]');

        if (closeButton) {
            const modal = closeButton.closest('.tracking-modal-overlay');

            if (modal) {
                modal.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            return;
        }

        if (event.target.classList.contains('tracking-modal-overlay')) {
            event.target.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('.tracking-modal-overlay.is-open').forEach(function (modal) {
            modal.classList.remove('is-open');
        });

        document.body.style.overflow = '';
    });
</script>
@endsection