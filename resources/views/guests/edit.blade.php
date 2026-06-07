@extends('layouts.app', ['title' => 'Edit Tamu'])

@push('styles')
@include('styles.form-edit')
<style>
/* INVITATION EDIT PAGE */
/* Khusus tampilan halaman Undangan Digital */

.invitation-edit-grid {
    display: grid;
    grid-template-columns: 390px 1fr;
    gap: 34px;
    align-items: start;
}

.invitation-preview-card {
    background: linear-gradient(180deg, #fffdf7 0%, #fff6d7 100%);
    border: 1px solid var(--border);
    border-radius: 28px;
    padding: 28px;
    box-shadow: var(--shadow);
    position: sticky;
    top: 32px;
}

.preview-top-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: var(--gold-dark);
    border: 1px solid var(--border);
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 34px;
}

.preview-ornament {
    width: 82px;
    height: 82px;
    border-radius: 28px;
    background: white;
    border: 1px solid var(--border);
    display: grid;
    place-items: center;
    color: var(--gold);
    font-size: 38px;
    margin: 0 auto 26px;
    box-shadow: 0 12px 26px rgba(17, 24, 39, 0.06);
}

.preview-subtitle {
    text-align: center;
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.preview-couple-name {
    font-family: "Playfair Display", Georgia, serif;
    color: var(--navy);
    text-align: center;
    font-size: 42px;
    font-weight: 900;
    line-height: 1.05;
    margin-bottom: 24px;
}

.preview-divider {
    width: 72px;
    height: 3px;
    background: var(--gold);
    border-radius: 999px;
    margin: 0 auto 24px;
}

.preview-info-box {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 18px;
    margin-bottom: 14px;
}

.preview-info-label {
    color: var(--muted-light);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.preview-info-value {
    color: var(--navy);
    font-weight: 800;
    line-height: 1.5;
}

.preview-info-muted {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
    margin-top: 4px;
}

.preview-link {
    display: block;
    text-align: center;
    text-decoration: none;
    background: var(--gold);
    color: white;
    border-radius: 14px;
    padding: 14px 18px;
    font-weight: 800;
    margin-top: 22px;
    box-shadow: 0 12px 24px rgba(216, 181, 50, 0.28);
}

.preview-link:hover {
    background: var(--gold-dark);
    color: white;
}

.invitation-form-panel {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 28px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.invitation-form-header {
    padding: 28px 32px;
    border-bottom: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: center;
}

.invitation-form-title {
    margin: 0;
    color: var(--navy);
    font-size: 24px;
    font-weight: 900;
}

.invitation-form-desc {
    margin: 6px 0 0;
    color: var(--muted);
    line-height: 1.6;
}

.invitation-form-body {
    padding: 32px;
}

@media (max-width: 1050px) {
    .invitation-edit-grid {
        grid-template-columns: 1fr;
    }

    .invitation-preview-card {
        position: relative;
        top: 0;
    }
}

@media (max-width: 700px) {
    .invitation-form-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')
@php
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
            Edit Data Tamu
        </h1>

        <div class="page-date">
            <span>✦</span>
            Perbarui data tamu, nomor WhatsApp, jumlah undangan, dan status RSVP.
        </div>
    </div>

    <div class="budget-status">
        <div class="budget-status-label">
            Status Tamu
        </div>

        <div class="budget-status-row">
            @if ($guest->invitation_sent_at)
                <span class="sent-pill sent-yes">Sudah Dikirim</span>
            @else
                <span class="sent-pill sent-no">Belum Dikirim</span>
            @endif
        </div>
    </div>
</section>

<section class="invitation-edit-grid">
    <aside class="invitation-preview-card">
        <div class="preview-top-label">
            👤 Detail Tamu
        </div>

        <div class="preview-ornament">
            {{ strtoupper(substr($guest->name, 0, 1)) }}
        </div>

        <div class="preview-subtitle">
            Nama Tamu
        </div>

        <div class="preview-couple-name" style="font-size: 36px;">
            {{ $guest->name }}
        </div>

        <div class="preview-divider"></div>

        <div class="preview-info-box">
            <div class="preview-info-label">
                Nomor WhatsApp
            </div>

            <div class="preview-info-value">
                {{ $guest->phone ?: 'Belum diisi' }}
            </div>
        </div>

        <div class="preview-info-box">
            <div class="preview-info-label">
                Grup Tamu
            </div>

            <div class="preview-info-value">
                {{ $guest->group_name ?: 'Tanpa Grup' }}
            </div>

            <div class="preview-info-muted">
                Jumlah undangan: {{ $guest->total_invited }} orang
            </div>
        </div>

        <div class="preview-info-box">
            <div class="preview-info-label">
                RSVP
            </div>

            <div class="preview-info-value">
                @if ($guest->rsvp_status === 'attend')
                    Hadir
                @elseif ($guest->rsvp_status === 'not_attend')
                    Tidak Hadir
                @else
                    Belum Konfirmasi
                @endif
            </div>
        </div>

        @if ($publicUrl)
            <a href="{{ $publicUrl }}" target="_blank" class="preview-link">
                Lihat Undangan Publik
            </a>
        @endif
    </aside>

    <div class="invitation-form-panel">
        <div class="invitation-form-header">
            <div>
                <h2 class="invitation-form-title">
                    Form Edit Tamu
                </h2>

                <p class="invitation-form-desc">
                    Pastikan nomor WhatsApp benar agar link kirim undangan dapat dibuat dengan rapi.
                </p>
            </div>
        </div>

        <div class="invitation-form-body">
            <form method="POST" action="{{ route('guests.update', $guest) }}">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="form-section-heading">
                        <div class="form-section-icon">00</div>
                        <div>
                            <h3 class="form-section-title">Acara Undangan</h3>
                            <div class="form-section-subtitle">
                                Tentukan tamu ini diundang ke acara yang mana.
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-1">
                        <div class="form-group">
                            <label class="form-label">Acara</label>

                            <select name="wedding_event_id" class="form-select">
                                <option value="">-- Pilih Acara --</option>

                                @foreach ($events as $event)
                                    <option
                                        value="{{ $event->id }}"
                                        {{ (string) old('wedding_event_id', $guest->wedding_event_id) === (string) $event->id ? 'selected' : '' }}
                                    >
                                        {{ $event->event_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-heading">
                        <div class="form-section-icon">01</div>
                        <div>
                            <h3 class="form-section-title">Informasi Utama</h3>
                            <div class="form-section-subtitle">
                                Nama, nomor WhatsApp, dan grup tamu.
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Tamu</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $guest->name) }}"
                                placeholder="Contoh: Bapak Ahmad"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', $guest->phone) }}"
                                placeholder="Contoh: 08123456789"
                            >
                        </div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 18px;">
                        <div class="form-group">
                            <label class="form-label">Grup Tamu</label>

                            <input
                                type="text"
                                name="group_name"
                                class="form-control"
                                list="edit-guest-group-options"
                                placeholder="Contoh: Keluarga, Teman, Kantor, Saudara Ibu"
                                value="{{ old('group_name', $guest->group_name) }}"
                            >

                            <datalist id="edit-guest-group-options">
                                @foreach ($groupOptions as $group)
                                    <option value="{{ $group }}"></option>
                                @endforeach
                            </datalist>

                            <div class="form-help">
                                Bisa pilih dari saran atau mengetik grup baru secara manual.
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jumlah Orang</label>
                            <input
                                type="number"
                                name="total_invited"
                                class="form-control"
                                min="1"
                                value="{{ old('total_invited', $guest->total_invited) }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-heading">
                        <div class="form-section-icon">02</div>
                        <div>
                            <h3 class="form-section-title">Status dan Alamat</h3>
                            <div class="form-section-subtitle">
                                Atur status RSVP dan alamat tamu jika diperlukan.
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-1">
                        <div class="form-group">
                            <label class="form-label">Status RSVP</label>
                            <select name="rsvp_status" class="form-select">
                                <option value="pending" {{ old('rsvp_status', $guest->rsvp_status) === 'pending' ? 'selected' : '' }}>
                                    Belum Konfirmasi
                                </option>

                                <option value="attend" {{ old('rsvp_status', $guest->rsvp_status) === 'attend' ? 'selected' : '' }}>
                                    Hadir
                                </option>

                                <option value="not_attend" {{ old('rsvp_status', $guest->rsvp_status) === 'not_attend' ? 'selected' : '' }}>
                                    Tidak Hadir
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <textarea
                                name="address"
                                class="form-control"
                                rows="5"
                                placeholder="Opsional"
                            >{{ old('address', $guest->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="invitation-submit-row">
                    <a href="{{ route('guests.index') }}" class="btn-soft-inline">
                        Kembali
                    </a>

                    <button class="btn-gold-inline" type="submit">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection