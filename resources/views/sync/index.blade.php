@extends('layouts.app', ['title' => 'Sinkronisasi Google Sheet'])

@push('styles')
<style>
    .sync-layout {
        display: flex;
        flex-direction: column;
        gap: 26px;
    }

    .sync-note-box {
        background: var(--gold-soft);
        border: 1px solid var(--border);
        color: var(--navy);
        border-radius: 18px;
        padding: 16px 18px;
        line-height: 1.7;
        font-size: 14px;
    }

    .sync-toolbar-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sync-filter-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .sync-run-grid {
        display: grid;
        grid-template-columns: 280px 180px 1fr;
        gap: 14px;
        align-items: end;
    }

    .sync-form-label {
        display: block;
        margin-bottom: 7px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sync-mini-help {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .sync-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .sync-badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .sync-badge-resolved {
        background: #dcfce7;
        color: #166534;
    }

    .sync-badge-ignored {
        background: #f3f4f6;
        color: #6b7280;
    }

    .sync-badge-type {
        background: var(--gold-soft);
        color: var(--gold-dark);
    }

    .sync-payload {
        max-width: 380px;
    }

    .sync-details summary {
        cursor: pointer;
        color: var(--gold-dark);
        font-weight: 800;
    }

    .sync-pre {
        margin-top: 10px;
        background: var(--navy);
        color: #fff;
        padding: 14px;
        border-radius: 14px;
        overflow-x: auto;
        max-height: 280px;
        font-size: 12px;
        line-height: 1.5;
    }

    .sync-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 210px;
    }

    .sync-actions form {
        margin: 0;
    }

    .sync-action-btn {
        border: 0;
        background: transparent;
        cursor: pointer;
        font-weight: 800;
        padding: 0;
        font-size: 13px;
    }

    .sync-action-success {
        color: var(--green);
    }

    .sync-action-danger {
        color: var(--red);
    }

    .sync-run-list {
        display: grid;
        gap: 12px;
    }

    .sync-run-item {
        border: 1px solid var(--soft-border);
        border-radius: 16px;
        padding: 16px;
        background: #fffdf7;
    }

    .sync-run-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: var(--navy);
        font-weight: 900;
    }

    .sync-output-card {
        margin-bottom: 0;
    }

    .sync-table-min {
        min-width: 1040px;
    }

    @media (max-width: 1100px) {
        .sync-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .sync-run-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .sync-filter-grid {
            grid-template-columns: 1fr;
        }

        .sync-actions {
            flex-direction: column;
            align-items: flex-start;
        }
    }

        /* FIX: compact action buttons inside sync table */
    .clean-table td .sync-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 8px !important;
        min-width: 220px !important;
    }

    .clean-table td .sync-actions form {
        margin: 0 !important;
        display: inline-flex !important;
    }

    .clean-table td .sync-action-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: auto !important;
        min-width: auto !important;
        height: 30px !important;
        padding: 0 10px !important;
        border-radius: 999px !important;
        border: 1px solid transparent !important;
        font-size: 12px !important;
        line-height: 1 !important;
        font-weight: 900 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        box-shadow: none !important;
    }

    .clean-table td .sync-action-import {
        background: var(--gold-soft) !important;
        color: var(--gold-dark) !important;
        border-color: #f3df9f !important;
    }

    .clean-table td .sync-action-import:hover {
        background: var(--gold) !important;
        color: #ffffff !important;
    }

    .clean-table td .sync-action-resolve {
        background: #dcfce7 !important;
        color: #166534 !important;
        border-color: #bbf7d0 !important;
    }

    .clean-table td .sync-action-ignore {
        background: #fee2e2 !important;
        color: #991b1b !important;
        border-color: #fecaca !important;
    }

    /* Hero card for sync page */
    .sync-panel-hero {
        background: linear-gradient(135deg, #fffdf8 0%, #fff6d7 100%);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 26px 28px;
        box-shadow: var(--shadow);
    }

    .sync-panel-hero-title {
        font-family: "Playfair Display", Georgia, serif;
        color: var(--navy);
        font-size: 38px;
        line-height: 1.1;
        margin: 0 0 10px;
    }

    .sync-panel-hero-text {
        color: var(--muted);
        line-height: 1.7;
        margin: 0;
        max-width: 920px;
    }

    .sync-safe-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        color: var(--gold-dark);
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 14px;
    }


    /* UX simplification */
    .sync-guide-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .sync-guide-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
    }

    .sync-guide-step {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: var(--gold);
        color: white;
        font-weight: 900;
        margin-bottom: 12px;
    }

    .sync-guide-title {
        color: var(--navy);
        font-weight: 900;
        margin-bottom: 6px;
    }

    .sync-guide-text {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .sync-section-title {
        color: var(--navy);
        font-size: 18px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .sync-section-desc {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.7;
        margin-bottom: 14px;
    }

    .sync-quick-filter-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sync-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: #fffdf7;
        color: var(--gold-dark);
        text-decoration: none;
        font-size: 13px;
        font-weight: 900;
    }

    .sync-chip:hover,
    .sync-chip.active {
        background: var(--gold);
        color: white;
        border-color: var(--gold);
    }

    .sync-chip-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 7px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.7);
        color: inherit;
        font-size: 12px;
        font-weight: 900;
    }

    .sync-advanced-filter {
        margin-top: 16px;
        border-top: 1px solid var(--soft-border);
        padding-top: 16px;
    }

    .sync-advanced-filter summary {
        cursor: pointer;
        color: var(--gold-dark);
        font-weight: 900;
        margin-bottom: 14px;
    }

    .sync-warning-text {
        color: var(--red);
        font-size: 12px;
        font-weight: 800;
        margin-top: 8px;
    }

    @media (max-width: 900px) {
        .sync-guide-grid {
            grid-template-columns: 1fr;
        }
    }


    .clean-table td .sync-action-export {
        background: #e0f2fe !important;
        color: #075985 !important;
        border-color: #bae6fd !important;
    }

    .clean-table td .sync-action-export:hover {
        background: #0ea5e9 !important;
        color: #ffffff !important;
    }


    .sync-badge-failed {
        background: #fee2e2;
        color: #991b1b;
    }

</style>



@endpush

@section('content')
<div class="sync-layout">
    <div class="page-heading">
        <div>
            <h1 class="page-title">Sinkronisasi Google Sheet</h1>
            <div class="page-date">
                <span>↔</span>
                Web dan Google Sheet
            </div>
        </div>

        <div class="budget-status">
            <div class="budget-status-label">Mode Saat Ini</div>
            <div class="budget-status-row">
                <div class="budget-status-track">
                    <div class="budget-status-fill" style="width: 100%;"></div>
                </div>
                <div class="budget-status-number">Review Only</div>
            </div>
        </div>
    </div>

    <div class="sync-panel-hero">
        <div class="sync-safe-pill">↔ Review Only Mode</div>
        <h2 class="sync-panel-hero-title">Cek Perbedaan Data Sebelum Sinkronisasi</h2>
        <p class="sync-panel-hero-text">
            Halaman ini membandingkan data Google Sheet dan database web. Sistem hanya membuat laporan perbedaan,
            sehingga data asli belum ditimpa otomatis. Gunakan tombol <strong>Import ke Web</strong> hanya untuk data
            yang memang ada di Google Sheet tetapi belum masuk ke web.
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('sync_output'))
        <div class="panel sync-output-card">
            <div class="panel-header">
                <h2 class="panel-title">Output Reconcile</h2>
            </div>
            <div class="panel-body">
                <pre class="sync-pre">{{ session('sync_output') }}</pre>
            </div>
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value gold">
                {{ $summaryByStatus->firstWhere('status', 'pending')->total ?? 0 }}
            </div>
            <div class="stat-note">Perbedaan yang perlu dicek admin.</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Resolved</div>
            <div class="stat-value green">
                {{ $summaryByStatus->firstWhere('status', 'resolved')->total ?? 0 }}
            </div>
            <div class="stat-note">Sudah ditandai selesai.</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Ignored</div>
            <div class="stat-value">
                {{ $summaryByStatus->firstWhere('status', 'ignored')->total ?? 0 }}
            </div>
            <div class="stat-note">Diabaikan oleh admin.</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Difference</div>
            <div class="stat-value">
                {{ $summaryByStatus->sum('total') }}
            </div>
            <div class="stat-note">Total semua laporan sync.</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Sheet Only</div>
            <div class="stat-value gold">
                {{ $summaryByType->firstWhere('difference_type', 'sheet_only')->total ?? 0 }}
            </div>
            <div class="stat-note">Ada di Sheet, belum ada di Web.</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Web Only</div>
            <div class="stat-value gold">
                {{ $summaryByType->firstWhere('difference_type', 'web_only')->total ?? 0 }}
            </div>
            <div class="stat-note">Ada di Web, belum ada di Sheet.</div>
        </div>
    </div>

    <div class="sync-guide-grid">
        <div class="sync-guide-card">
            <div class="sync-guide-step">1</div>
            <div class="sync-guide-title">Cek Ulang Data</div>
            <div class="sync-guide-text">
                Membaca Google Sheet dan database web, lalu membuat laporan perbedaan.
                Data asli belum diubah.
            </div>
        </div>

        <div class="sync-guide-card">
            <div class="sync-guide-step">2</div>
            <div class="sync-guide-title">Tinjau Perbedaan</div>
            <div class="sync-guide-text">
                Gunakan Filter Cepat untuk melihat data yang perlu di-import,
                di-export, atau diperiksa karena berbeda.
            </div>
        </div>

        <div class="sync-guide-card">
            <div class="sync-guide-step">3</div>
            <div class="sync-guide-title">Jalankan Aksi</div>
            <div class="sync-guide-text">
                Import hanya untuk data <strong>sheet_only</strong>.
                Data <strong>web_only</strong> nanti akan memakai fitur Export ke Staging.
            </div>
        </div>
    </div>

    <div class="guest-filter-card">
        <div class="sync-section-title">Filter Cepat</div>
        <div class="sync-section-desc">
            Pilih jenis masalah yang ingin dilihat. Filter ini hanya mengubah tampilan daftar, bukan mengubah data.
        </div>

        <div class="sync-quick-filter-row">
            <a class="sync-chip {{ $differenceType === 'all' ? 'active' : '' }}"
               href="{{ route('sync.index', array_merge(request()->except('page'), ['status' => 'pending', 'difference_type' => 'all'])) }}">
                Semua Pending
                <span class="sync-chip-count">{{ $summaryByStatus->firstWhere('status', 'pending')->total ?? 0 }}</span>
            </a>

            <a class="sync-chip {{ $differenceType === 'sheet_only' ? 'active' : '' }}"
               href="{{ route('sync.index', array_merge(request()->except('page'), ['status' => 'pending', 'difference_type' => 'sheet_only'])) }}">
                Perlu Import ke Web
                <span class="sync-chip-count">{{ $summaryByType->firstWhere('difference_type', 'sheet_only')->total ?? 0 }}</span>
            </a>

            <a class="sync-chip {{ $differenceType === 'web_only' ? 'active' : '' }}"
               href="{{ route('sync.index', array_merge(request()->except('page'), ['status' => 'pending', 'difference_type' => 'web_only'])) }}">
                Perlu Export ke Staging
                <span class="sync-chip-count">{{ $summaryByType->firstWhere('difference_type', 'web_only')->total ?? 0 }}</span>
            </a>

            <a class="sync-chip {{ $differenceType === 'different' ? 'active' : '' }}"
               href="{{ route('sync.index', array_merge(request()->except('page'), ['status' => 'pending', 'difference_type' => 'different'])) }}">
                Data Berbeda
                <span class="sync-chip-count">{{ $summaryByType->firstWhere('difference_type', 'different')->total ?? 0 }}</span>
            </a>
        </div>

        <details class="sync-advanced-filter">
            <summary>Filter lanjutan</summary>

            <form class="sync-toolbar-form" method="GET" action="{{ route('sync.index') }}">
                <div class="sync-filter-grid">
                    <div>
                        <label class="sync-form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="resolved" @selected($status === 'resolved')>Resolved</option>
                            <option value="ignored" @selected($status === 'ignored')>Ignored</option>
                            <option value="failed" @selected($status === 'failed')>Failed</option>
                            <option value="all" @selected($status === 'all')>Semua</option>
                        </select>
                    </div>

                    <div>
                        <label class="sync-form-label">Module</label>
                        <select class="form-select" name="module">
                            <option value="all">Semua</option>
                            @foreach ($modules as $item)
                                <option value="{{ $item }}" @selected($module === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sync-form-label">Tipe Perbedaan</label>
                        <select class="form-select" name="difference_type">
                            <option value="all" @selected($differenceType === 'all')>Semua</option>
                            @foreach ($differenceTypes as $item)
                                <option value="{{ $item }}" @selected($differenceType === $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sync-form-label">Sheet</label>
                        <select class="form-select" name="sheet_name">
                            <option value="all">Semua</option>
                            @foreach ($sheetNames as $item)
                                <option value="{{ $item }}" @selected($sheetName === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="sync-form-label">&nbsp;</label>
                        <button class="guest-filter-btn" type="submit">Filter</button>
                    </div>
                </div>
            </form>
        </details>
    </div>

    <div class="guest-filter-card">
        <form class="sync-run-grid" method="POST" action="{{ route('sync.run') }}">
            @csrf

            <div>
                <label class="sync-form-label">Langkah 1 — Cek ulang data</label>
                <select class="form-select" name="module">
                    <option value="all">Semua Data</option>
                    <option value="events">Events</option>
                    <option value="guests">Guests</option>
                    <option value="budget_items">Budget</option>
                    <option value="checklist_items">Persiapan</option>
                    <option value="documents">Dokumen Nikah</option>
                </select>
            </div>

            <div>
                <label class="sync-form-label">&nbsp;</label>
                <button class="guest-filter-btn" type="submit">Cek Ulang</button>
            </div>

            <div class="sync-mini-help">
                Gunakan tombol ini terlebih dahulu. Sistem hanya mengecek ulang data Sheet dan Web,
                lalu memperbarui daftar perbedaan. Tidak ada data asli yang ditimpa.
            </div>
        </form>
    </div>

    <div class="guest-filter-card">
        <div class="item-name" style="margin-bottom: 12px;">
            Langkah 3 — Import Sheet Only ke Web
        </div>

        <form class="sync-run-grid"
              method="POST"
              action="{{ route('sync.applyAllSheetToWeb') }}"
              onsubmit="return confirm('Import semua data sheet_only pada module yang dipilih ke Web? Pastikan kamu sudah menjalankan Cek Sekarang terlebih dahulu.');">
            @csrf

            <div>
                <label class="sync-form-label">Pilih Modul</label>
                <select class="form-select" name="module">
                    <option value="all">Semua Data yang Perlu Import</option>
                    <option value="events">Events</option>
                    <option value="guests">Guests</option>
                    <option value="budget_items">Budget</option>
                    <option value="checklist_items">Persiapan</option>
                    <option value="documents">Dokumen Nikah</option>
                </select>
            </div>

            <div>
                <label class="sync-form-label">&nbsp;</label>
                <button class="guest-filter-btn" type="submit">
                    Import Semua
                </button>
            </div>

            <div class="sync-mini-help">
                Gunakan ini hanya setelah <strong>Cek Ulang</strong>. Tombol ini hanya memproses data
                <strong>sheet_only</strong>, yaitu data yang ada di Google Sheet tetapi belum ada di Web.
                Data yang sudah ada di Web tidak akan ditimpa.
            </div>
        </form>
    </div>

    <div class="guest-filter-card">
        <div class="item-name" style="margin-bottom: 12px;">
            Export Web Only ke WEB_EXPORT Staging
        </div>

        <form class="sync-run-grid"
              method="POST"
              action="{{ route('sync.exportAllWebToStaging') }}"
              onsubmit="return confirm('Kirim semua data web_only pada module yang dipilih ke WEB_EXPORT staging sheet?');">
            @csrf

            <div>
                <label class="sync-form-label">Pilih Modul</label>
                <select class="form-select" name="module">
                    <option value="all">Semua Data yang Perlu Staging</option>
                    <option value="events">Events</option>
                    <option value="guests">Guests</option>
                    <option value="budget_items">Budget</option>
                    <option value="checklist_items">Persiapan</option>
                    <option value="documents">Dokumen Nikah</option>
                </select>
            </div>

            <div>
                <label class="sync-form-label">&nbsp;</label>
                <input type="hidden" name="limit" value="5">

                <button class="guest-filter-btn" type="submit">
                    Jalankan Otomatis
                </button>
            </div>

            <div class="sync-mini-help">
                Tombol ini memulai proses backend otomatis. Sistem akan memproses data
                <strong>web_only</strong> sebanyak maksimal <strong>5 data per batch</strong>,
                lalu melanjutkan batch berikutnya sampai data habis. Pastikan terminal
                <strong>queue worker</strong> aktif.
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Ringkasan Pending</h2>
            <span class="item-badge">{{ $summaryBySheet->sum('total') }} item</span>
        </div>

        <div class="panel-body">
            @if ($summaryBySheet->isEmpty())
                <div class="empty-state">Tidak ada pending difference.</div>
            @else
                <div class="table-wrapper">
                    <table class="clean-table">
                        <thead>
                        <tr>
                            <th>Sheet</th>
                            <th>Tipe</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($summaryBySheet as $item)
                            <tr>
                                <td>
                                    <div class="item-name">{{ $item->sheet_name ?: '-' }}</div>
                                </td>
                                <td>
                                    <span class="sync-badge sync-badge-type">{{ $item->difference_type }}</span>
                                </td>
                                <td>
                                    <strong>{{ $item->total }}</strong>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Daftar Perbedaan</h2>
            <span class="item-badge">{{ $differences->total() }} data</span>
        </div>

        <div class="table-wrapper">
            <table class="clean-table sync-table-min">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Module</th>
                    <th>Sheet</th>
                    <th>Key / Row</th>
                    <th>Tipe</th>
                    <th>Field Berbeda</th>
                    <th>Payload</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($differences as $difference)
                    <tr>
                        <td>
                            <span class="sync-badge sync-badge-{{ $difference->status }}">
                                {{ $difference->status }}
                            </span>
                        </td>

                        <td>
                            <div class="item-name">{{ $difference->module }}</div>
                        </td>

                        <td>
                            <div class="item-category">{{ $difference->sheet_name ?: '-' }}</div>
                        </td>

                        <td>
                            <div class="item-name">{{ $difference->record_key ?: '-' }}</div>
                            <div class="item-category">Row: {{ $difference->sheet_row ?: '-' }}</div>
                            <div class="item-category">Web ID: {{ $difference->web_id ?: '-' }}</div>
                        </td>

                        <td>
                            <span class="sync-badge sync-badge-type">{{ $difference->difference_type }}</span>
                        </td>

                        <td class="sync-payload">
                            @if ($difference->field_differences)
                                <details class="sync-details">
                                    <summary>Lihat field</summary>
                                    <pre class="sync-pre">{{ json_encode($difference->field_differences, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @else
                                <span class="item-category">Tidak ada field diff detail.</span>
                            @endif
                        </td>

                        <td class="sync-payload">
                            <details class="sync-details">
                                <summary>Sheet Payload</summary>
                                <pre class="sync-pre">{{ json_encode($difference->sheet_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>

                            <details class="sync-details" style="margin-top:8px;">
                                <summary>Web Payload</summary>
                                <pre class="sync-pre">{{ json_encode($difference->web_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                        </td>

                        <td>
                            @if ($difference->status === 'pending')
                                <div class="sync-actions">
                                    @if (in_array($difference->difference_type, ['sheet_only', 'different'], true))
                                        <form method="POST" action="{{ route('sync.applySheetToWeb', $difference) }}"
                                              onsubmit="return confirm('{{ $difference->difference_type === 'different' ? 'Pakai data Sheet untuk menimpa data Web?' : 'Import data ini dari Google Sheet ke Web?' }}');">
                                            @csrf
                                            @method('PATCH')
                                            <button class="sync-action-pill sync-action-import" type="submit">
                                                {{ $difference->difference_type === 'different' ? 'Pakai Data Sheet' : 'Import ke Web' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if (in_array($difference->difference_type, ['web_only', 'different'], true))
                                        <form method="POST" action="{{ route('sync.exportWebToStaging', $difference) }}"
                                              onsubmit="return confirm('{{ $difference->difference_type === 'different' ? 'Pakai data Web untuk dikirim ke staging sheet?' : 'Kirim data ini ke WEB_EXPORT staging sheet?' }}');">
                                            @csrf
                                            @method('PATCH')
                                            <button class="sync-action-pill sync-action-export" type="submit">
                                                {{ $difference->difference_type === 'different' ? 'Pakai Data Web' : 'Export ke Staging' }}
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('sync.resolve', $difference) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="sync-action-pill sync-action-resolve" type="submit">
                                            Selesai
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('sync.ignore', $difference) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="sync-action-pill sync-action-ignore" type="submit">
                                            Abaikan
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="item-category">
                                    {{ $difference->resolved_at ? $difference->resolved_at->format('d M Y H:i') : 'Sudah diproses' }}
                                </span>
                            @endif

                            @if ($difference->note)
                                <div class="item-category" style="margin-top:8px;">
                                    {{ $difference->note }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">Tidak ada data perbedaan untuk filter ini.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel-body">
            <div class="sync-pagination">
                <div class="pagination-info">
                    Showing {{ $differences->firstItem() ?? 0 }}
                    to {{ $differences->lastItem() ?? 0 }}
                    of {{ $differences->total() }} results
                </div>

                <div class="pagination-actions">
                    @if ($differences->onFirstPage())
                        <span class="pagination-btn disabled">‹ Previous</span>
                    @else
                        <a class="pagination-btn" href="{{ $differences->previousPageUrl() }}">‹ Previous</a>
                    @endif

                    <span class="pagination-current">
                        Page {{ $differences->currentPage() }} of {{ $differences->lastPage() }}
                    </span>

                    @if ($differences->hasMorePages())
                        <a class="pagination-btn" href="{{ $differences->nextPageUrl() }}">Next ›</a>
                    @else
                        <span class="pagination-btn disabled">Next ›</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Riwayat Reconcile Terakhir</h2>
            <span class="item-badge">{{ $runs->count() }} log</span>
        </div>

        <div class="panel-body">
            @if ($runs->isEmpty())
                <div class="empty-state">Belum ada riwayat reconcile.</div>
            @else
                <div class="sync-run-list">
                    @foreach ($runs as $run)
                        <div class="sync-run-item">
                            <div class="sync-run-title">
                                {{ $run->run_type }}
                                <span class="sync-badge sync-badge-type">{{ $run->status }}</span>
                            </div>

                            <div class="item-category">
                                Mulai: {{ $run->started_at?->format('d M Y H:i:s') ?: '-' }}
                                |
                                Selesai: {{ $run->finished_at?->format('d M Y H:i:s') ?: '-' }}
                            </div>

                            <div class="item-category" style="margin-top:8px;">
                                Sheet: {{ $run->total_sheet_rows }},
                                Web: {{ $run->total_web_rows }},
                                Same: {{ $run->total_same }},
                                Sheet Only: {{ $run->total_sheet_only }},
                                Web Only: {{ $run->total_web_only }},
                                Different: {{ $run->total_different }},
                                Dummy: {{ $run->total_dummy }},
                                Error: {{ $run->total_errors }}
                            </div>

                            @if ($run->error_message)
                                <pre class="sync-pre">{{ $run->error_message }}</pre>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
