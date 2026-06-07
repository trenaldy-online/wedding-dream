@php
    $saveDateSection = $saveDateSection ?? null;
    $saveDateContent = $saveDateSection ? ($saveDateSection->content ?? []) : [];

    $saveDateTitle = old('save_date_title', $saveDateContent['title'] ?? 'Save the date');
    $saveDateButtonText = old('save_date_button_text', $saveDateContent['button_text'] ?? 'Add to Calendar');
    $calendarTitle = old(
        'calendar_title',
        $saveDateContent['calendar_title'] ?? trim($profile->groom_name . ' & ' . $profile->bride_name . ' Wedding')
    );
    $calendarDetails = old(
        'calendar_details',
        $saveDateContent['calendar_details'] ?? 'You are invited to our wedding ceremony.'
    );
    $calendarDurationMinutes = old(
        'calendar_duration_minutes',
        $saveDateContent['calendar_duration_minutes'] ?? 180
    );
    $saveDateEventDate = old('event_date');

    if ($saveDateEventDate === null) {
        $saveDateEventDate = $profile->event_date
            ? \Carbon\Carbon::parse($profile->event_date)->format('Y-m-d\TH:i')
            : '';
    }
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Save The Date
            </h2>

            <p class="invitation-form-desc">
                Atur judul countdown dan tombol Add to Calendar pada template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.save-date.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">SD</div>

                    <div>
                        <h3 class="form-section-title">
                            Pengaturan Save The Date
                        </h3>

                        <div class="form-section-subtitle">
                            Countdown memakai tanggal utama dari Detail Undangan.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Tanggal & Jam Countdown
                        </label>

                        <input
                            type="datetime-local"
                            name="event_date"
                            class="form-control"
                            value="{{ $saveDateEventDate }}"
                        >

                        <div class="form-help">
                            Tanggal ini akan dipakai untuk countdown dan Add to Calendar.
                        </div>
                    </div>
                </div>
                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Section
                        </label>

                        <input
                            type="text"
                            name="save_date_title"
                            class="form-control"
                            value="{{ $saveDateTitle }}"
                            placeholder="Contoh: Save the date"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Calendar
                        </label>

                        <input
                            type="text"
                            name="save_date_button_text"
                            class="form-control"
                            value="{{ $saveDateButtonText }}"
                            placeholder="Contoh: Add to Calendar"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Google Calendar
                        </label>

                        <input
                            type="text"
                            name="calendar_title"
                            class="form-control"
                            value="{{ $calendarTitle }}"
                            placeholder="Contoh: Trenaldy & Dinda Wedding"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Durasi Acara
                        </label>

                        <input
                            type="number"
                            name="calendar_duration_minutes"
                            class="form-control"
                            value="{{ $calendarDurationMinutes }}"
                            min="30"
                            max="720"
                            step="30"
                        >

                        <div class="form-help">
                            Dalam menit. Contoh: 180 berarti 3 jam.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Google Calendar
                        </label>

                        <textarea
                            name="calendar_details"
                            class="form-control"
                            rows="4"
                            placeholder="Tulis deskripsi acara untuk Google Calendar"
                        >{{ $calendarDetails }}</textarea>
                    </div>
                </div>

                <div class="form-help" style="margin-top: 16px;">
                    Tanggal countdown saat ini:
                    <strong>
                        @if ($profile->event_date)
                            {{ $profile->event_date->translatedFormat('d F Y, H:i') }} WIB
                        @else
                            Belum diatur. Isi dulu tanggal pada Detail Undangan.
                        @endif
                    </strong>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Save The Date
                </button>
            </div>
        </form>
    </div>
</section>