@extends('layouts.app', ['title' => 'Edit Budget'])

@push('styles')
@include('styles.form-edit')
<style>
/* BUDGET PAGE V2 */
/* Dipakai oleh halaman Budget dan halaman Acara */

.budget-page {
    display: flex;
    flex-direction: column;
    gap: 26px;
}

.budget-overview-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 22px;
}

.budget-hero-card,
.budget-side-card,
.budget-metric-card,
.budget-form-card,
.budget-table-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 24px;
    box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
}

.budget-hero-card {
    padding: 28px;
    background: linear-gradient(135deg, #fffdf8 0%, #fff6d7 100%);
}

.budget-kicker {
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

.budget-title {
    font-family: "Playfair Display", Georgia, serif;
    color: var(--navy);
    font-size: 42px;
    line-height: 1.1;
    margin: 0 0 12px;
}

.budget-subtitle {
    color: var(--muted);
    line-height: 1.8;
    margin: 0;
    max-width: 760px;
}

.budget-progress-box {
    margin-top: 24px;
    background: rgba(255, 255, 255, 0.82);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 18px;
}

.budget-progress-header {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
    margin-bottom: 14px;
}

.budget-progress-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.budget-progress-value {
    color: var(--navy);
    font-size: 28px;
    font-weight: 900;
}

.budget-status-chip {
    border-radius: 999px;
    padding: 9px 13px;
    font-size: 12px;
    font-weight: 900;
}

.budget-status-chip.safe {
    background: #dcfce7;
    color: #166534;
}

.budget-status-chip.danger {
    background: #fee2e2;
    color: #991b1b;
}

.budget-progress-track {
    width: 100%;
    height: 10px;
    background: #ebe7df;
    border-radius: 999px;
    overflow: hidden;
}

.budget-progress-fill {
    height: 100%;
    background: var(--gold);
    border-radius: 999px;
}

.budget-side-card {
    padding: 24px;
}

.budget-side-title {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin-bottom: 18px;
}

.budget-side-list {
    display: grid;
    gap: 12px;
}

.budget-side-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fffdf8;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 14px 16px;
}

.budget-side-item span {
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
}

.budget-side-item strong {
    color: var(--navy);
    font-size: 18px;
}

.budget-metric-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.budget-metric-card {
    padding: 22px;
}

.budget-metric-label {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.budget-metric-value {
    color: var(--navy);
    font-size: 30px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 8px;
}

.budget-metric-value.gold {
    color: var(--gold-dark);
}

.budget-metric-value.green {
    color: #059669;
}

.budget-metric-value.red {
    color: #dc2626;
}

.budget-metric-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.7;
}

.budget-workspace-grid {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 24px;
    align-items: start;
}

.budget-form-card {
    padding: 24px;
}

.budget-table-card {
    overflow: hidden;
}

.budget-card-header {
    margin-bottom: 22px;
}

.budget-card-header.table-header {
    padding: 24px 26px;
    margin-bottom: 0;
    border-bottom: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
}

.budget-card-header h2 {
    color: var(--navy);
    font-size: 22px;
    font-weight: 900;
    margin: 0 0 6px;
}

.budget-card-header p {
    color: var(--muted);
    margin: 0;
    line-height: 1.6;
    font-size: 14px;
}

.budget-count-badge {
    background: var(--gold);
    color: white;
    border-radius: 10px;
    padding: 8px 12px;
    font-weight: 900;
    font-size: 13px;
    white-space: nowrap;
}

.budget-submit-btn {
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

.budget-submit-btn:hover {
    background: var(--gold-dark);
}

.budget-table-wrapper {
    overflow-x: auto;
}

.budget-table {
    width: 100%;
    border-collapse: collapse;
}

.budget-table thead {
    background: #fafafa;
}

.budget-table th {
    text-align: left;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    padding: 16px 20px;
}

.budget-table td {
    padding: 16px 20px;
    border-top: 1px solid var(--soft-border);
    vertical-align: middle;
}

.budget-item-name {
    color: var(--navy);
    font-weight: 900;
    margin-bottom: 4px;
}

.budget-item-note {
    color: var(--muted-light);
    font-size: 12px;
    line-height: 1.5;
    max-width: 260px;
}

.budget-category-pill {
    display: inline-flex;
    align-items: center;
    background: #f3f4f6;
    color: var(--muted);
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 800;
}

.budget-amount {
    font-weight: 800;
    white-space: nowrap;
}

.budget-amount.plan {
    color: var(--gold-dark);
}

.budget-amount.actual {
    color: var(--navy);
}

.budget-status-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 900;
}

.budget-status-pill.paid {
    background: #dcfce7;
    color: #166534;
}

.budget-status-pill.partial {
    background: #fef3c7;
    color: #92400e;
}

.budget-status-pill.unpaid {
    background: #f3f4f6;
    color: #6b7280;
}

.budget-action-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.budget-action-group form {
    margin: 0;
}

.budget-edit-btn {
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    padding: 0 12px;
    background: #fffdf7;
    border: 1px solid var(--border);
    color: var(--gold-dark);
    font-size: 12px;
    font-weight: 900;
    text-decoration: none;
}

.budget-edit-btn:hover {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

.budget-delete-btn {
    height: 34px;
    border: 0;
    background: #fee2e2;
    color: #991b1b;
    border-radius: 10px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 900;
    cursor: pointer;
}

.budget-delete-btn:hover {
    background: #fecaca;
}

.budget-empty-state {
    text-align: center;
    padding: 36px 20px;
    color: var(--muted);
}

.budget-pagination {
    padding: 18px 22px 24px;
    border-top: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

/* FILTER CARD */
/* Dipakai di budget karena filter acara masih memakai class guest-filter-* */

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
    grid-template-columns: 1.7fr 1fr 1fr 1fr 0.7fr;
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

/* BADGE CPW / CPP */
/* Dipakai di halaman Acara */

.checklist-person-pill {
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

.checklist-muted {
    color: var(--muted-light);
}

@media (max-width: 1100px) {
    .budget-overview-grid,
    .budget-workspace-grid {
        grid-template-columns: 1fr;
    }

    .budget-metric-grid {
        grid-template-columns: 1fr;
    }

    .guest-filter-main {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    .budget-title {
        font-size: 34px;
    }

    .budget-progress-header,
    .budget-card-header.table-header,
    .budget-pagination {
        flex-direction: column;
        align-items: flex-start;
    }

    .budget-table {
        min-width: 820px;
    }

    .guest-filter-main {
        grid-template-columns: 1fr;
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
</style>
@endpush

@section('content')
@php
    $selisih = $budgetItem->estimated_amount - $budgetItem->actual_amount;

    $statusLabel = match ($budgetItem->payment_status) {
        'paid' => 'Lunas',
        'partial' => 'DP / Sebagian',
        default => 'Rencana',
    };
@endphp

<section class="budget-page">
    <div class="budget-overview-grid">
        <div class="budget-hero-card">
            <div class="budget-kicker">
                Edit Item Budget
            </div>

            <h1 class="budget-title">
                {{ $budgetItem->item_name }}
            </h1>

            <p class="budget-subtitle">
                Perbarui detail kebutuhan, estimasi biaya, biaya aktual, status pembayaran, dan catatan budget.
            </p>

            <div class="budget-progress-box">
                <div class="budget-progress-header">
                    <div>
                        <div class="budget-progress-label">Status Saat Ini</div>
                        <div class="budget-progress-value">
                            {{ $statusLabel }}
                        </div>
                    </div>

                    <div class="budget-status-chip {{ $selisih >= 0 ? 'safe' : 'danger' }}">
                        {{ $selisih >= 0 ? 'Masih Aman' : 'Melebihi Estimasi' }}
                    </div>
                </div>

                <div class="budget-progress-track">
                    <div
                        class="budget-progress-fill"
                        style="width: {{ $budgetItem->estimated_amount > 0 ? min(100, round(($budgetItem->actual_amount / $budgetItem->estimated_amount) * 100)) : 0 }}%;"
                    ></div>
                </div>
            </div>
        </div>

        <div class="budget-side-card">
            <div class="budget-side-title">
                Ringkasan Item
            </div>

            <div class="budget-side-list">
                <div class="budget-side-item">
                    <span>Kategori</span>
                    <strong>{{ $budgetItem->category }}</strong>
                </div>

                <div class="budget-side-item">
                    <span>Estimasi</span>
                    <strong>Rp{{ number_format($budgetItem->estimated_amount, 0, ',', '.') }}</strong>
                </div>

                <div class="budget-side-item">
                    <span>Aktual</span>
                    <strong>Rp{{ number_format($budgetItem->actual_amount, 0, ',', '.') }}</strong>
                </div>

                <div class="budget-side-item">
                    <span>Selisih</span>
                    <strong>Rp{{ number_format($selisih, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="budget-form-card">
        <div class="budget-card-header">
            <div>
                <h2>Form Edit Budget</h2>
                <p>Ubah data budget sesuai kondisi terbaru.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('budget-items.update', $budgetItem) }}">
            @csrf
            @method('PUT')

            <div class="form-grid-1">
                <div class="form-group">
                    <label class="form-label">Acara</label>

                    <select name="wedding_event_id" class="form-select">
                        <option value="">-- Pilih Acara --</option>

                        @foreach ($events as $event)
                            <option
                                value="{{ $event->id }}"
                                {{ (string) old('wedding_event_id', $budgetItem->wedding_event_id) === (string) $event->id ? 'selected' : '' }}
                            >
                                {{ $event->event_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Item</label>
                    <input
                        type="text"
                        name="item_name"
                        class="form-control"
                        value="{{ old('item_name', $budgetItem->item_name) }}"
                        placeholder="Contoh: Cincin kawin"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">-- Pilih Kategori --</option>

                        @foreach (['Catering', 'Dekorasi', 'Mahar', 'Venue', 'Dokumentasi', 'Undangan', 'Souvenir', 'Makeup', 'Busana', 'Lainnya'] as $category)
                            <option
                                value="{{ $category }}"
                                {{ old('category', $budgetItem->category) === $category ? 'selected' : '' }}
                            >
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid-2" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">Estimasi Biaya</label>
                    <input
                        type="number"
                        name="estimated_amount"
                        class="form-control"
                        min="0"
                        value="{{ old('estimated_amount', $budgetItem->estimated_amount) }}"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Biaya Aktual</label>
                    <input
                        type="number"
                        name="actual_amount"
                        class="form-control"
                        min="0"
                        value="{{ old('actual_amount', $budgetItem->actual_amount) }}"
                    >
                </div>
            </div>

            <div class="form-grid-1" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="payment_status" class="form-select">
                        <option value="unpaid" {{ old('payment_status', $budgetItem->payment_status) === 'unpaid' ? 'selected' : '' }}>
                            Rencana
                        </option>

                        <option value="partial" {{ old('payment_status', $budgetItem->payment_status) === 'partial' ? 'selected' : '' }}>
                            DP / Sebagian
                        </option>

                        <option value="paid" {{ old('payment_status', $budgetItem->payment_status) === 'paid' ? 'selected' : '' }}>
                            Lunas
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea
                        name="note"
                        class="form-control"
                        rows="5"
                        placeholder="Tambahkan catatan pembayaran, vendor, invoice, atau detail lainnya"
                    >{{ old('note', $budgetItem->note) }}</textarea>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('budget-items.index') }}" class="btn-soft-inline">
                    Kembali
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</section>
@endsection