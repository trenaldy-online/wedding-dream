@extends('layouts.app', ['title' => 'Dashboard Wedding'])

@push('styles')
<style>
/* DASHBOARD V2 */

.dashboard-v2 {
    display: flex;
    flex-direction: column;
    gap: 28px;
}

.dashboard-v2-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    align-items: stretch;
}

.dashboard-hero-card,
.dashboard-side-card,
.dashboard-section-card,
.dashboard-metric-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 24px;
    box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
}

.dashboard-hero-card {
    padding: 28px;
    background: linear-gradient(135deg, #fffdf8 0%, #fff6d7 100%);
}

.dashboard-hero-top {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: flex-start;
    margin-bottom: 22px;
}

.dashboard-hero-kicker {
    display: inline-block;
    background: rgba(255, 255, 255, 0.85);
    color: var(--gold-dark);
    font-size: 12px;
    font-weight: 800;
    padding: 8px 12px;
    border-radius: 999px;
    margin-bottom: 14px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.dashboard-hero-title {
    font-family: "Playfair Display", Georgia, serif;
    font-size: 42px;
    line-height: 1.1;
    color: var(--navy);
    margin: 0 0 12px;
}

.dashboard-hero-subtitle {
    color: var(--muted);
    line-height: 1.8;
    font-size: 15px;
    max-width: 720px;
    margin: 0;
}

.dashboard-date-badge {
    min-width: 180px;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 16px;
}

.dashboard-date-badge-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.dashboard-date-badge-value {
    color: var(--navy);
    font-size: 18px;
    font-weight: 800;
    line-height: 1.4;
}

.dashboard-highlight-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 22px;
}

.dashboard-highlight-item {
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 18px;
}

.dashboard-highlight-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.dashboard-highlight-value {
    color: var(--navy);
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 6px;
}

.dashboard-highlight-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

.dashboard-hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.dashboard-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 18px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 800;
    font-size: 14px;
    transition: 0.2s ease;
}

.dashboard-btn-primary {
    background: var(--gold);
    color: white;
    box-shadow: 0 12px 24px rgba(216, 181, 50, 0.28);
}

.dashboard-btn-primary:hover {
    background: var(--gold-dark);
    color: white;
}

.dashboard-btn-soft {
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid var(--border);
    color: var(--navy);
}

.dashboard-btn-soft:hover {
    background: var(--gold-soft);
    color: var(--navy);
}

.dashboard-side-card {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.dashboard-side-title {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
}

.dashboard-side-list {
    display: grid;
    gap: 12px;
}

.dashboard-side-item {
    border: 1px solid var(--border);
    background: #fffdf8;
    border-radius: 16px;
    padding: 14px 16px;
}

.dashboard-side-item-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.dashboard-side-item-value {
    color: var(--navy);
    font-size: 18px;
    font-weight: 800;
    line-height: 1.5;
}

.dashboard-public-link-box {
    margin-top: auto;
    background: var(--gold-soft);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 16px;
}

.dashboard-public-link-label {
    color: var(--gold-dark);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.dashboard-public-link {
    color: var(--navy);
    font-weight: 700;
    text-decoration: none;
    word-break: break-word;
}

.dashboard-metric-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.dashboard-metric-card {
    padding: 24px;
}

.dashboard-metric-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.dashboard-metric-value {
    color: var(--navy);
    font-size: 32px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 8px;
}

.dashboard-metric-value.gold {
    color: var(--gold-dark);
}

.dashboard-metric-value.green {
    color: #059669;
}

.dashboard-metric-value.red {
    color: #dc2626;
}

.dashboard-metric-desc {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
}

.dashboard-section-card {
    padding: 24px;
}

.dashboard-section-header {
    margin-bottom: 20px;
}

.dashboard-section-title {
    color: var(--navy);
    font-size: 24px;
    font-weight: 900;
    margin: 0 0 6px;
}

.dashboard-section-subtitle {
    color: var(--muted);
    margin: 0;
    line-height: 1.7;
}

.dashboard-action-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.dashboard-action-card {
    display: block;
    text-decoration: none;
    background: #fffdf8;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 22px;
    color: var(--navy);
    transition: 0.2s ease;
}

.dashboard-action-card:hover {
    transform: translateY(-2px);
    background: var(--gold-soft);
    color: var(--navy);
}

.dashboard-action-icon {
    font-size: 26px;
    margin-bottom: 14px;
}

.dashboard-action-title {
    font-size: 18px;
    font-weight: 900;
    margin-bottom: 8px;
}

.dashboard-action-desc {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
}

.text-success-custom {
    color: #059669;
}

.text-danger-custom {
    color: #dc2626;
}

@media (max-width: 1100px) {
    .dashboard-v2-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-highlight-grid,
    .dashboard-metric-grid,
    .dashboard-action-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-hero-top {
        flex-direction: column;
    }

    .dashboard-date-badge {
        width: 100%;
    }
}

@media (max-width: 700px) {
    .dashboard-hero-title {
        font-size: 32px;
    }

    .dashboard-hero-card,
    .dashboard-side-card,
    .dashboard-section-card,
    .dashboard-metric-card {
        padding: 18px;
    }

    .dashboard-highlight-value,
    .dashboard-metric-value {
        font-size: 26px;
    }
}

/* DASHBOARD EVENT SUMMARY */

.dashboard-event-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.dashboard-event-card {
    background: #fffdf8;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 22px;
}

.dashboard-event-top {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}

.dashboard-event-side {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--gold-soft);
    color: var(--gold-dark);
    border-radius: 999px;
    padding: 7px 11px;
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 10px;
}

.dashboard-event-title {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin: 0 0 6px;
}

.dashboard-event-date {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
}

.dashboard-event-progress {
    margin-bottom: 18px;
}

.dashboard-event-progress-head {
    display: flex;
    justify-content: space-between;
    color: var(--muted);
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 8px;
}

.dashboard-event-progress-head strong {
    color: var(--gold-dark);
}

.dashboard-event-progress-track {
    width: 100%;
    height: 9px;
    background: #ebe7df;
    border-radius: 999px;
    overflow: hidden;
}

.dashboard-event-progress-fill {
    height: 100%;
    background: var(--gold);
    border-radius: 999px;
}

.dashboard-event-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.dashboard-event-stats div {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
}

.dashboard-event-stats span {
    display: block;
    color: var(--muted-light);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.dashboard-event-stats strong {
    color: var(--navy);
    font-size: 15px;
    font-weight: 900;
}

.dashboard-event-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.dashboard-event-actions a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    padding: 0 12px;
    background: white;
    border: 1px solid var(--border);
    color: var(--gold-dark);
    border-radius: 10px;
    font-size: 12px;
    font-weight: 900;
    text-decoration: none;
}

.dashboard-event-actions a:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.dashboard-empty-event {
    background: var(--gold-soft);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 22px;
    color: var(--navy);
    line-height: 1.7;
}

.dashboard-empty-event a {
    color: var(--gold-dark);
    font-weight: 900;
}

@media (max-width: 900px) {
    .dashboard-event-grid {
        grid-template-columns: 1fr;
    }
}

/* DASHBOARD SIDE SUMMARY */

.dashboard-global-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.7;
    margin: -8px 0 4px;
}

.dashboard-side-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.dashboard-side-summary-card {
    position: relative;
    background: #fffdf8;
    border: 1px solid var(--border);
    border-radius: 22px;
    padding: 22px;
    overflow: hidden;
}

.dashboard-side-summary-card::before {
    content: "";
    position: absolute;
    width: 130px;
    height: 130px;
    border-radius: 999px;
    top: -48px;
    right: -42px;
    opacity: 0.22;
}

.dashboard-side-summary-card.cpw::before {
    background: #f9a8d4;
}

.dashboard-side-summary-card.cpp::before {
    background: #93c5fd;
}

.dashboard-side-summary-card.both::before {
    background: #d8b532;
}

.dashboard-side-summary-top {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
}

.dashboard-side-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 7px 11px;
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
}

.dashboard-side-chip.cpw {
    background: #fce7f3;
    color: #9d174d;
}

.dashboard-side-chip.cpp {
    background: #dbeafe;
    color: #1e40af;
}

.dashboard-side-chip.both {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.dashboard-side-summary-title {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin: 0 0 8px;
}

.dashboard-side-summary-desc {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
    margin: 0;
}

.dashboard-side-budget-progress {
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
}

.dashboard-side-progress-head {
    display: flex;
    justify-content: space-between;
    color: var(--muted);
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 8px;
}

.dashboard-side-progress-head strong {
    color: var(--gold-dark);
}

.dashboard-side-progress-track {
    width: 100%;
    height: 9px;
    background: #ebe7df;
    border-radius: 999px;
    overflow: hidden;
}

.dashboard-side-progress-fill {
    height: 100%;
    background: var(--gold);
    border-radius: 999px;
}

.dashboard-side-summary-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 11px;
    position: relative;
    z-index: 1;
}

.dashboard-side-summary-stats div {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 12px;
}

.dashboard-side-summary-stats span {
    display: block;
    color: var(--muted-light);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.dashboard-side-summary-stats strong {
    display: block;
    color: var(--navy);
    font-size: 15px;
    font-weight: 900;
    line-height: 1.4;
}

@media (max-width: 1050px) {
    .dashboard-side-summary-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .dashboard-side-summary-stats {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $budgetPercent = $totalEstimated > 0
        ? min(100, round(($totalActual / $totalEstimated) * 100))
        : 0;

    $daysLeft = null;

    if ($nextEvent && $nextEvent->event_date) {
        $today = Carbon::now()->startOfDay();
        $daysLeft = $today->diffInDays($nextEvent->event_date->copy()->startOfDay(), false);
    }

    $publicUrl = $profile ? route('invitation.show', $profile->slug) : null;
@endphp

<section class="dashboard-v2">
    <div class="dashboard-v2-grid">
        <div class="dashboard-hero-card">
            <div class="dashboard-hero-top">
                <div>
                    <div class="dashboard-hero-kicker">
                        Wedding Overview
                    </div>

                    <h1 class="dashboard-hero-title">
                        {{ $profile ? $profile->groom_name . ' & ' . $profile->bride_name : 'Acara Pernikahan' }}
                    </h1>

                    <p class="dashboard-hero-subtitle">
                        Kelola beberapa rangkaian acara wedding dari satu dashboard, termasuk acara pihak CPW, acara pihak CPP, budget, tamu, RSVP, dan undangan digital.
                    </p>
                </div>

                <div class="dashboard-date-badge">
                    <div class="dashboard-date-badge-label">
                        Acara Terdekat
                    </div>

                    <div class="dashboard-date-badge-value">
                        @if ($nextEvent)
                            {{ $nextEvent->event_name }}
                            <br>
                            <small>
                                {{ $nextEvent->event_date ? $nextEvent->event_date->translatedFormat('d M Y') : 'Tanggal belum diatur' }}
                            </small>
                        @else
                            Belum ada acara
                        @endif
                    </div>
                </div>
            </div>

            <div class="dashboard-highlight-grid">
                <div class="dashboard-highlight-item">
                    <div class="dashboard-highlight-label">Status Anggaran</div>
                    <div class="dashboard-highlight-value">{{ $budgetPercent }}%</div>
                    <div class="dashboard-highlight-desc">
                        Realisasi dari total estimasi semua acara.
                    </div>
                </div>

                <div class="dashboard-highlight-item">
                    <div class="dashboard-highlight-label">Menuju Acara</div>
                    <div class="dashboard-highlight-value">
                        @if (! is_null($daysLeft))
                            {{ $daysLeft >= 0 ? $daysLeft . ' hari' : 'Lewat' }}
                        @else
                            -
                        @endif
                    </div>
                    <div class="dashboard-highlight-desc">
                        Berdasarkan acara terdekat yang sudah diatur.
                    </div>
                </div>

                <div class="dashboard-highlight-item">
                    <div class="dashboard-highlight-label">Konfirmasi Hadir</div>
                    <div class="dashboard-highlight-value">{{ $totalInvitedPeople }}</div>
                    <div class="dashboard-highlight-desc">
                        Total orang hadir dari hasil RSVP.
                    </div>
                </div>
            </div>

            <div class="dashboard-hero-actions">
                <a href="{{ route('wedding-events.index') }}" class="dashboard-btn dashboard-btn-primary">
                    Kelola Acara
                </a>

                <a href="{{ route('budget-items.index') }}" class="dashboard-btn dashboard-btn-soft">
                    Kelola Budget
                </a>

                <a href="{{ route('guests.index') }}" class="dashboard-btn dashboard-btn-soft">
                    Kelola Tamu
                </a>

                @if ($publicUrl)
                    <a href="{{ $publicUrl }}" target="_blank" class="dashboard-btn dashboard-btn-soft">
                        Lihat Undangan
                    </a>
                @endif
            </div>
        </div>

        <div class="dashboard-side-card">
            <div class="dashboard-side-title">
                Ringkasan Global Semua Acara
            </div>

            <p class="dashboard-global-desc">
                Gabungan data dari acara CPW, CPP, dan acara bersama.
            </p>

            <div class="dashboard-side-list">
                <div class="dashboard-side-item">
                    <div class="dashboard-side-item-label">Total Acara</div>
                    <div class="dashboard-side-item-value">{{ $events->count() }}</div>
                </div>

                <div class="dashboard-side-item">
                    <div class="dashboard-side-item-label">Total Nama Tamu</div>
                    <div class="dashboard-side-item-value">{{ $totalGuests }}</div>
                </div>

                <div class="dashboard-side-item">
                    <div class="dashboard-side-item-label">Undangan Terkirim</div>
                    <div class="dashboard-side-item-value">{{ $totalSent }}</div>
                </div>

                <div class="dashboard-side-item">
                    <div class="dashboard-side-item-label">Sisa Budget</div>
                    <div class="dashboard-side-item-value {{ $sisaBudget >= 0 ? 'text-success-custom' : 'text-danger-custom' }}">
                        Rp{{ number_format($sisaBudget, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if ($publicUrl)
                <div class="dashboard-public-link-box">
                    <div class="dashboard-public-link-label">
                        Link Undangan Umum
                    </div>

                    <a href="{{ $publicUrl }}" target="_blank" class="dashboard-public-link">
                        {{ $publicUrl }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-section-card">
        <div class="dashboard-section-header">
            <div>
                <h2 class="dashboard-section-title">
                    Ringkasan Per Pihak
                </h2>

                <p class="dashboard-section-subtitle">
                    Data dipisahkan berdasarkan acara pihak CPW, pihak CPP, dan acara bersama agar tidak tercampur dengan ringkasan global.
                </p>
            </div>
        </div>

        <div class="dashboard-side-summary-grid">
            @foreach ($sideSummaries as $summary)
                @if ($summary['event_count'] > 0 || $summary['total_estimated'] > 0 || $summary['total_guests'] > 0)
                    <div class="dashboard-side-summary-card {{ $summary['class'] }}">
                        <div class="dashboard-side-summary-top">
                            <div>
                                <div class="dashboard-side-chip {{ $summary['class'] }}">
                                    {{ $summary['label'] }}
                                </div>

                                <h3 class="dashboard-side-summary-title">
                                    {{ $summary['label'] }}
                                </h3>

                                <p class="dashboard-side-summary-desc">
                                    {{ $summary['description'] }}
                                </p>
                            </div>
                        </div>

                        <div class="dashboard-side-budget-progress">
                            <div class="dashboard-side-progress-head">
                                <span>Realisasi Budget</span>
                                <strong>{{ $summary['budget_percent'] }}%</strong>
                            </div>

                            <div class="dashboard-side-progress-track">
                                <div
                                    class="dashboard-side-progress-fill"
                                    style="width: {{ $summary['budget_percent'] }}%;"
                                ></div>
                            </div>
                        </div>

                        <div class="dashboard-side-summary-stats">
                            <div>
                                <span>Total Acara</span>
                                <strong>{{ $summary['event_count'] }}</strong>
                            </div>

                            <div>
                                <span>Total Tamu</span>
                                <strong>{{ $summary['total_guests'] }}</strong>
                            </div>

                            <div>
                                <span>Konfirmasi Hadir</span>
                                <strong>{{ $summary['total_attending_people'] }}</strong>
                            </div>

                            <div>
                                <span>Undangan Terkirim</span>
                                <strong>{{ $summary['total_sent'] }}</strong>
                            </div>

                            <div>
                                <span>Estimasi</span>
                                <strong>Rp{{ number_format($summary['total_estimated'], 0, ',', '.') }}</strong>
                            </div>

                            <div>
                                <span>Realisasi</span>
                                <strong>Rp{{ number_format($summary['total_actual'], 0, ',', '.') }}</strong>
                            </div>

                            <div>
                                <span>Sisa Budget</span>
                                <strong class="{{ $summary['remaining'] >= 0 ? 'text-success-custom' : 'text-danger-custom' }}">
                                    Rp{{ number_format($summary['remaining'], 0, ',', '.') }}
                                </strong>
                            </div>

                            <div>
                                <span>Pending RSVP</span>
                                <strong>{{ $summary['total_pending_rsvp'] }}</strong>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="dashboard-section-card">
        <div class="dashboard-section-header">
            <div>
                <h2 class="dashboard-section-title">
                    Ringkasan Per Acara
                </h2>

                <p class="dashboard-section-subtitle">
                    Pantau budget, tamu, RSVP, dan pengiriman undangan untuk masing-masing acara.
                </p>
            </div>
        </div>

        @if ($events->count() > 0)
            <div class="dashboard-event-grid">
                @foreach ($events as $event)
                    @php
                        $eventBudgetPercent = $event->total_estimated > 0
                            ? min(100, round(($event->total_actual / $event->total_estimated) * 100))
                            : 0;
                    @endphp

                    <div class="dashboard-event-card">
                        <div class="dashboard-event-top">
                            <div>
                                <div class="dashboard-event-side">
                                    @if ($event->event_side === 'cpw')
                                        Pihak CPW
                                    @elseif ($event->event_side === 'cpp')
                                        Pihak CPP
                                    @else
                                        Bersama
                                    @endif
                                </div>

                                <h3 class="dashboard-event-title">
                                    {{ $event->event_name }}
                                </h3>

                                <div class="dashboard-event-date">
                                    @if ($event->event_date)
                                        {{ $event->event_date->translatedFormat('d M Y H:i') }}
                                    @else
                                        Tanggal belum diatur
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-event-progress">
                            <div class="dashboard-event-progress-head">
                                <span>Realisasi Budget</span>
                                <strong>{{ $eventBudgetPercent }}%</strong>
                            </div>

                            <div class="dashboard-event-progress-track">
                                <div class="dashboard-event-progress-fill" style="width: {{ $eventBudgetPercent }}%;"></div>
                            </div>
                        </div>

                        <div class="dashboard-event-stats">
                            <div>
                                <span>Estimasi</span>
                                <strong>Rp{{ number_format($event->total_estimated, 0, ',', '.') }}</strong>
                            </div>

                            <div>
                                <span>Aktual</span>
                                <strong>Rp{{ number_format($event->total_actual, 0, ',', '.') }}</strong>
                            </div>

                            <div>
                                <span>Tamu</span>
                                <strong>{{ $event->total_guests }}</strong>
                            </div>

                            <div>
                                <span>Hadir</span>
                                <strong>{{ $event->total_attending_people }}</strong>
                            </div>

                            <div>
                                <span>Terkirim</span>
                                <strong>{{ $event->total_sent }}</strong>
                            </div>

                            <div>
                                <span>Pending RSVP</span>
                                <strong>{{ $event->total_pending_rsvp }}</strong>
                            </div>
                        </div>

                        <div class="dashboard-event-actions">
                            <a href="{{ route('budget-items.index', ['event_id' => $event->id]) }}">
                                Budget
                            </a>

                            <a href="{{ route('guests.index', ['event_id' => $event->id]) }}">
                                Tamu
                            </a>

                            <a href="{{ route('wedding-events.edit', $event) }}">
                                Edit Acara
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="dashboard-empty-event">
                Belum ada acara. Silakan buat acara CPW, CPP, atau acara bersama terlebih dahulu.
                <br>
                <a href="{{ route('wedding-events.index') }}">Buat acara sekarang</a>
            </div>
        @endif
    </div>

    <div class="dashboard-section-card">
        <div class="dashboard-section-header">
            <div>
                <h2 class="dashboard-section-title">Aksi Cepat</h2>
                <p class="dashboard-section-subtitle">
                    Langsung menuju fitur utama wedding planner.
                </p>
            </div>
        </div>

        <div class="dashboard-action-grid">
            <a href="{{ route('wedding-events.index') }}" class="dashboard-action-card">
                <div class="dashboard-action-icon">📅</div>
                <div class="dashboard-action-title">Kelola Acara</div>
                <div class="dashboard-action-desc">
                    Buat acara pihak CPW, pihak CPP, akad, resepsi, atau acara bersama.
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="dashboard-action-card">
                <div class="dashboard-action-icon">💌</div>
                <div class="dashboard-action-title">Undangan Digital</div>
                <div class="dashboard-action-desc">
                    Atur nama mempelai, slug, teks pembuka, dan cerita undangan.
                </div>
            </a>

            <a href="{{ route('checklists.index') }}" class="dashboard-action-card">
                <div class="dashboard-action-icon">✅</div>
                <div class="dashboard-action-title">Checklist</div>
                <div class="dashboard-action-desc">
                    Pantau kebutuhan CPW, CPP, dokumen, vendor, dan persiapan acara.
                </div>
            </a>
        </div>
    </div>
</section>
@endsection