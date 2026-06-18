@extends('layouts.app')

@section('content')
@php
    $spreadsheetOk = (bool) ($status['spreadsheet_ok'] ?? false);
    $webhookTokenOk = (bool) ($status['webhook_token_ok'] ?? false);
    $legacyEnabled = (bool) ($status['legacy_enabled'] ?? false);
    $queueMode = (bool) ($status['web_export_queue'] ?? false);
    $safeDelete = (bool) ($status['safe_delete'] ?? false);
    $autoRefreshDropdowns = (bool) ($status['auto_refresh_dropdowns'] ?? false);

    $sheets = collect($status['sheets'] ?? []);
    $healthySheets = $sheets->filter(fn ($row) => ($row['exists'] ?? false) && ($row['headers_ok'] ?? false))->count();

    $latestSyncLogs = collect($status['latest_sync_logs'] ?? []);
    $latestChangeLogs = collect($status['latest_change_logs'] ?? []);

    $latestErrorCount = $latestSyncLogs->filter(function ($row) {
        $statusValue = strtolower((string) ($row['status'] ?? ''));
        return $statusValue !== '' && $statusValue !== 'success';
    })->count();

    $badge = function ($ok, $okText = 'OK', $badText = 'Check') {
        return $ok
            ? '<span class="sync-badge sync-badge-success">'.$okText.'</span>'
            : '<span class="sync-badge sync-badge-danger">'.$badText.'</span>';
    };
@endphp

<style>
    .sync-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 48px 24px 72px;
        color: #1f2937;
    }

    .sync-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        margin-bottom: 28px;
    }

    .sync-title {
        margin: 0;
        font-size: 34px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #111827;
    }

    .sync-subtitle {
        margin: 10px 0 0;
        max-width: 720px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
    }

    .sync-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sync-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        padding: 10px 16px;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        border: 1px solid transparent;
        transition: 0.18s ease;
        white-space: nowrap;
    }

    .sync-btn-primary {
        background: #c99a3b;
        color: white;
        box-shadow: 0 10px 22px rgba(201, 154, 59, 0.22);
    }

    .sync-btn-primary:hover {
        background: #b3832f;
        color: white;
    }

    .sync-btn-secondary {
        background: white;
        color: #334155;
        border-color: #e2e8f0;
    }

    .sync-btn-secondary:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .sync-grid {
        display: grid;
        gap: 18px;
    }

    .sync-grid-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .sync-grid-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .sync-grid-2 {
        grid-template-columns: minmax(0, 1.65fr) minmax(320px, 0.85fr);
    }

    .sync-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .sync-card-body {
        padding: 22px;
    }

    .sync-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
    }

    .sync-card-title {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: #111827;
    }

    .sync-card-desc {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .sync-metric-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .sync-metric-value {
        color: #111827;
        font-size: 24px;
        line-height: 1.15;
        font-weight: 850;
        word-break: break-word;
    }

    .sync-metric-sub {
        margin-top: 7px;
        color: #64748b;
        font-size: 12.5px;
        line-height: 1.55;
    }

    .sync-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 11px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: .02em;
        white-space: nowrap;
    }

    .sync-badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .sync-badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .sync-badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .sync-badge-neutral {
        background: #e2e8f0;
        color: #334155;
    }

    .sync-status-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .sync-table-wrap {
        overflow-x: auto;
    }

    .sync-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .sync-table th {
        background: #f8fafc;
        color: #475569;
        text-align: left;
        font-weight: 800;
        padding: 13px 16px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .sync-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #eef2f7;
        color: #334155;
        vertical-align: top;
    }

    .sync-table tr:hover td {
        background: #fbfdff;
    }

    .sync-module-name {
        font-weight: 800;
        color: #111827;
    }

    .sync-log-list {
        display: grid;
        gap: 12px;
        padding: 18px;
    }

    .sync-log-item {
        border: 1px solid #edf2f7;
        border-radius: 16px;
        padding: 14px 15px;
        background: #fff;
    }

    .sync-log-head {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 8px;
    }

    .sync-log-title {
        font-weight: 800;
        color: #111827;
        font-size: 13px;
    }

    .sync-log-time {
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
    }

    .sync-log-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .sync-log-message {
        color: #475569;
        font-size: 12.5px;
        line-height: 1.6;
        word-break: break-word;
    }

    .sync-alert {
        border-radius: 18px;
        padding: 16px 18px;
        margin-bottom: 22px;
        border: 1px solid;
        font-size: 14px;
        line-height: 1.6;
    }

    .sync-alert-danger {
        border-color: #fecaca;
        color: #991b1b;
        background: #fff1f2;
    }

    .sync-tip-list {
        display: grid;
        gap: 12px;
        padding: 18px;
    }

    .sync-tip {
        border-radius: 16px;
        padding: 15px;
        background: #f8fafc;
        border: 1px solid #edf2f7;
    }

    .sync-tip strong {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 13px;
    }

    .sync-tip p {
        margin: 0;
        color: #64748b;
        font-size: 12.5px;
        line-height: 1.65;
    }

    .sync-tip-warning {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .sync-empty {
        padding: 24px;
        color: #64748b;
        font-size: 14px;
    }

    .sync-divider-space {
        height: 22px;
    }

    @media (max-width: 980px) {
        .sync-grid-4,
        .sync-grid-3,
        .sync-grid-2 {
            grid-template-columns: 1fr;
        }

        .sync-hero {
            flex-direction: column;
        }

        .sync-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 560px) {
        .sync-page {
            padding: 32px 16px 56px;
        }

        .sync-title {
            font-size: 28px;
        }

        .sync-card-body,
        .sync-card-header {
            padding: 18px;
        }
    }
</style>

<div class="sync-page">
    <div class="sync-hero">
        <div>
            <h1 class="sync-title">Wedding Sync V2 Status</h1>
            <p class="sync-subtitle">
                Monitoring koneksi Google Sheets, webhook, status header, mode sinkronisasi, dan log terbaru.
                Halaman ini dipakai untuk memastikan sinkronisasi Web ↔ Sheet berjalan normal.
            </p>
        </div>

        <div class="sync-actions">
            <a href="{{ url('/sync-v2-status') }}" class="sync-btn sync-btn-primary">Refresh Status</a>
            <a href="{{ url('/') }}" class="sync-btn sync-btn-secondary">Kembali</a>
        </div>
    </div>

    @if(!empty($status['spreadsheet_error']))
        <div class="sync-alert sync-alert-danger">
            <strong>Spreadsheet Error:</strong>
            {{ $status['spreadsheet_error'] }}
        </div>
    @endif

    <div class="sync-grid sync-grid-4">
        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-status-top">
                    <div>
                        <div class="sync-metric-label">Google Sheet</div>
                        <div class="sync-metric-value">{{ $spreadsheetOk ? 'Aktif' : 'Bermasalah' }}</div>
                        <div class="sync-metric-sub">{{ $status['spreadsheet_title'] ?? 'Tidak terbaca' }}</div>
                    </div>
                    {!! $badge($spreadsheetOk, 'OK', 'ERROR') !!}
                </div>
            </div>
        </div>

        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-status-top">
                    <div>
                        <div class="sync-metric-label">Webhook Token</div>
                        <div class="sync-metric-value">{{ $webhookTokenOk ? 'Terisi' : 'Kosong' }}</div>
                        <div class="sync-metric-sub">Dipakai Apps Script Sheet → Web.</div>
                    </div>
                    {!! $badge($webhookTokenOk, 'OK', 'CHECK') !!}
                </div>
            </div>
        </div>

        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-status-top">
                    <div>
                        <div class="sync-metric-label">Legacy Sync</div>
                        <div class="sync-metric-value">{{ $legacyEnabled ? 'Aktif' : 'Disabled' }}</div>
                        <div class="sync-metric-sub">Sync lama sebaiknya tetap disabled.</div>
                    </div>
                    {!! $badge(!$legacyEnabled, 'OK', 'ON') !!}
                </div>
            </div>
        </div>

        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-status-top">
                    <div>
                        <div class="sync-metric-label">Web → Sheet Mode</div>
                        <div class="sync-metric-value">{{ $queueMode ? 'Queue' : 'Direct' }}</div>
                        <div class="sync-metric-sub">{{ $queueMode ? 'Siap untuk production.' : 'Local/dev mode. Production disarankan Queue.' }}</div>
                    </div>
                    <span class="sync-badge {{ $queueMode ? 'sync-badge-success' : 'sync-badge-warning' }}">
                        {{ $queueMode ? 'PROD' : 'DEV' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="sync-divider-space"></div>

    <div class="sync-grid sync-grid-3">
        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-metric-label">Healthy Modules</div>
                <div class="sync-metric-value">{{ $healthySheets }} / {{ $sheets->count() }}</div>
                <div class="sync-metric-sub">Sheet tersedia dan header valid.</div>
            </div>
        </div>

        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-metric-label">Safe Delete</div>
                <div class="sync-metric-value">{{ $safeDelete ? 'Enabled' : 'Disabled' }}</div>
                <div class="sync-metric-sub">Delete aman via clear row / sync action.</div>
            </div>
        </div>

        <div class="sync-card">
            <div class="sync-card-body">
                <div class="sync-metric-label">Dropdown Auto Refresh</div>
                <div class="sync-metric-value">{{ $autoRefreshDropdowns ? 'Enabled' : 'Disabled' }}</div>
                <div class="sync-metric-sub">Dropdown Sheet mengikuti data terbaru.</div>
            </div>
        </div>
    </div>

    <div class="sync-divider-space"></div>

    <div class="sync-card">
        <div class="sync-card-header">
            <h2 class="sync-card-title">Sheet & Header Check</h2>
            <p class="sync-card-desc">
                Validasi modul utama Sync V2. Semua sheet harus tersedia dan header harus valid.
            </p>
        </div>

        <div class="sync-table-wrap">
            <table class="sync-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Sheet</th>
                        <th>Sheet Exists</th>
                        <th>Headers</th>
                        <th>Missing Headers</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sheets as $row)
                        <tr>
                            <td class="sync-module-name">{{ $row['module'] ?? '-' }}</td>
                            <td>{{ $row['sheet'] ?? '-' }}</td>
                            <td>
                                {!! $badge((bool) ($row['exists'] ?? false), 'OK', 'MISSING') !!}
                            </td>
                            <td>
                                {!! $badge((bool) ($row['headers_ok'] ?? false), 'OK', 'CHECK') !!}
                            </td>
                            <td>
                                @php
                                    $missing = $row['missing_headers'] ?? [];
                                @endphp

                                @if(empty($missing))
                                    <span style="color:#94a3b8">-</span>
                                @else
                                    {{ implode(', ', $missing) }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="sync-empty">Belum ada data sheet check.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="sync-divider-space"></div>

    <div class="sync-grid sync-grid-2">
        <div class="sync-card">
            <div class="sync-card-header">
                <h2 class="sync-card-title">Latest SYNC_LOG</h2>
                <p class="sync-card-desc">
                    Log terbaru dari tab SYNC_LOG Google Sheet.
                </p>
            </div>

            @if($latestSyncLogs->isEmpty())
                <div class="sync-empty">Belum ada sync log.</div>
            @else
                <div class="sync-log-list">
                    @foreach($latestSyncLogs->take(10) as $log)
                        @php
                            $logStatus = strtolower((string) ($log['status'] ?? ''));
                            $isSuccess = $logStatus === 'success';
                            $time = $log['waktu'] ?? $log['time'] ?? $log['created_at'] ?? '-';
                            $module = $log['module'] ?? '-';
                            $direction = $log['direction'] ?? '-';
                            $action = $log['action'] ?? '-';
                            $message = $log['message'] ?? '-';
                            $item = $log['item'] ?? null;
                        @endphp

                        <div class="sync-log-item">
                            <div class="sync-log-head">
                                <div class="sync-log-title">{{ $module }}</div>
                                <div class="sync-log-time">{{ $time }}</div>
                            </div>

                            <div class="sync-log-meta">
                                <span class="sync-badge sync-badge-neutral">{{ $direction }}</span>
                                <span class="sync-badge sync-badge-neutral">{{ $action }}</span>
                                <span class="sync-badge {{ $isSuccess ? 'sync-badge-success' : 'sync-badge-danger' }}">
                                    {{ $logStatus ? strtoupper($logStatus) : '-' }}
                                </span>
                            </div>

                            <div class="sync-log-message">
                                {{ $message }}

                                @if($item)
                                    <br>
                                    <span style="color:#64748b">Item: {{ $item }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="sync-card">
            <div class="sync-card-header">
                <h2 class="sync-card-title">Latest Change Logs</h2>
                <p class="sync-card-desc">
                    Riwayat perubahan dari database untuk audit dan rollback.
                </p>
            </div>

            @if($latestChangeLogs->isEmpty())
                <div class="sync-empty">Belum ada change log.</div>
            @else
                <div class="sync-log-list">
                    @foreach($latestChangeLogs->take(8) as $log)
                        <div class="sync-log-item">
                            <div class="sync-log-head">
                                <div class="sync-log-title">{{ $log->module ?? '-' }}</div>
                                <div class="sync-log-time">{{ $log->changed_at ?? $log->created_at ?? '-' }}</div>
                            </div>

                            <div class="sync-log-meta">
                                <span class="sync-badge sync-badge-neutral">ID {{ $log->id ?? '-' }}</span>
                                <span class="sync-badge sync-badge-neutral">{{ $log->action ?? '-' }}</span>
                                <span class="sync-badge sync-badge-neutral">Model {{ $log->model_id ?? '-' }}</span>
                            </div>

                            <div class="sync-log-message">
                                sync_key: {{ $log->sync_key ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="sync-tip-list">
                <div class="sync-tip">
                    <strong>Input dari Sheet</strong>
                    <p>Isi data sampai lengkap, lalu pilih <b>sync_action = SYNC</b>. Tunggu <b>sync_status</b> berubah menjadi selesai.</p>
                </div>

                <div class="sync-tip">
                    <strong>Delete Aman</strong>
                    <p>Kosongkan isi row bisnis atau gunakan action delete sesuai panduan. Jangan edit kolom sistem hidden.</p>
                </div>

                <div class="sync-tip sync-tip-warning">
                    <strong>Production Reminder</strong>
                    <p>Ganti URL ngrok di Apps Script menjadi domain production dan aktifkan queue worker jika sudah deploy.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
