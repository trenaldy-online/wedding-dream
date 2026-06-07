@php
    $rsvpSection = $rsvpSection ?? null;
    $rsvpContent = $rsvpSection ? ($rsvpSection->content ?? []) : [];

    $rsvpTitle = old('rsvp_title', $rsvpContent['title'] ?? 'RSVP');
    $rsvpDescription = old(
        'rsvp_description',
        $rsvpContent['description'] ?? 'Please confirm your attendance to help us prepare the best seat for you.'
    );

    $rsvpStatusQuestion = old('rsvp_status_question', $rsvpContent['status_question'] ?? 'Apakah kamu datang?');
    $rsvpAttendText = old('rsvp_attend_text', $rsvpContent['attend_text'] ?? 'Hadir');
    $rsvpNotAttendText = old('rsvp_not_attend_text', $rsvpContent['not_attend_text'] ?? 'Tidak Hadir');

    $rsvpSessionQuestion = old('rsvp_session_question', $rsvpContent['session_question'] ?? 'Acara mana yang akan Anda hadiri?');

    $rsvpEvents = $rsvpContent['events'] ?? [
        ['value' => 'akad', 'label' => 'Akad Nikah'],
        ['value' => 'resepsi', 'label' => 'Resepsi'],
    ];

    $rsvpEvent1Label = old('rsvp_event_1_label', $rsvpEvents[0]['label'] ?? 'Akad Nikah');
    $rsvpEvent2Label = old('rsvp_event_2_label', $rsvpEvents[1]['label'] ?? 'Resepsi');
    $rsvpAllEventsText = old('rsvp_all_events_text', $rsvpContent['all_events_text'] ?? 'Hadir Semua');

    $rsvpTotalQuestion = old('rsvp_total_question', $rsvpContent['total_question'] ?? 'Jumlah tamu yang datang termasuk kamu?');
    $rsvpMaxAttendance = old('rsvp_max_attendance', $rsvpContent['max_attendance'] ?? 10);

    $rsvpSubmitText = old('rsvp_submit_text', $rsvpContent['submit_text'] ?? 'Send RSVP');
    $rsvpChangeText = old('rsvp_change_text', $rsvpContent['change_text'] ?? 'Change');

    $rsvpSuccessAttendTitle = old('rsvp_success_attend_title', $rsvpContent['success_attend_title'] ?? 'Will Attend');
    $rsvpSuccessAttendCaption = old(
        'rsvp_success_attend_caption',
        $rsvpContent['success_attend_caption'] ?? 'Yeay, Thank you for the attendance. See you there ;)'
    );

    $rsvpSuccessNotAttendTitle = old('rsvp_success_not_attend_title', $rsvpContent['success_not_attend_title'] ?? 'Unable to Attend');
    $rsvpSuccessNotAttendCaption = old(
        'rsvp_success_not_attend_caption',
        $rsvpContent['success_not_attend_caption'] ?? 'Thank you for confirming. Your wishes mean a lot to us.'
    );
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten RSVP
            </h2>

            <p class="invitation-form-desc">
                Atur teks pada form RSVP tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.rsvp.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">RS</div>

                    <div>
                        <h3 class="form-section-title">
                            Judul dan Deskripsi RSVP
                        </h3>

                        <div class="form-section-subtitle">
                            Teks utama yang muncul di bagian atas form RSVP.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul RSVP
                        </label>

                        <input
                            type="text"
                            name="rsvp_title"
                            class="form-control"
                            value="{{ $rsvpTitle }}"
                            placeholder="Contoh: RSVP"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Maksimal Tamu
                        </label>

                        <input
                            type="number"
                            name="rsvp_max_attendance"
                            class="form-control"
                            value="{{ $rsvpMaxAttendance }}"
                            min="1"
                            max="20"
                        >

                        <div class="form-help">
                            Batas maksimal jumlah tamu yang bisa dipilih.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi RSVP
                        </label>

                        <textarea
                            name="rsvp_description"
                            class="form-control"
                            rows="4"
                            placeholder="Tulis deskripsi RSVP"
                        >{{ $rsvpDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">01</div>

                    <div>
                        <h3 class="form-section-title">
                            Status Kehadiran
                        </h3>

                        <div class="form-section-subtitle">
                            Atur pertanyaan dan pilihan hadir/tidak hadir.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Pertanyaan Status
                        </label>

                        <input
                            type="text"
                            name="rsvp_status_question"
                            class="form-control"
                            value="{{ $rsvpStatusQuestion }}"
                            placeholder="Contoh: Apakah kamu datang?"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Hadir
                        </label>

                        <input
                            type="text"
                            name="rsvp_attend_text"
                            class="form-control"
                            value="{{ $rsvpAttendText }}"
                            placeholder="Contoh: Hadir"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Tidak Hadir
                        </label>

                        <input
                            type="text"
                            name="rsvp_not_attend_text"
                            class="form-control"
                            value="{{ $rsvpNotAttendText }}"
                            placeholder="Contoh: Tidak Hadir"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">02</div>

                    <div>
                        <h3 class="form-section-title">
                            Pilihan Acara
                        </h3>

                        <div class="form-section-subtitle">
                            Teks ini muncul ketika tamu memilih hadir.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Pertanyaan Pilihan Acara
                        </label>

                        <input
                            type="text"
                            name="rsvp_session_question"
                            class="form-control"
                            value="{{ $rsvpSessionQuestion }}"
                            placeholder="Contoh: Acara mana yang akan Anda hadiri?"
                        >
                    </div>
                </div>

                <div class="form-grid-3" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Label Acara 1
                        </label>

                        <input
                            type="text"
                            name="rsvp_event_1_label"
                            class="form-control"
                            value="{{ $rsvpEvent1Label }}"
                            placeholder="Contoh: Akad Nikah"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Label Acara 2
                        </label>

                        <input
                            type="text"
                            name="rsvp_event_2_label"
                            class="form-control"
                            value="{{ $rsvpEvent2Label }}"
                            placeholder="Contoh: Resepsi"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Label Semua Acara
                        </label>

                        <input
                            type="text"
                            name="rsvp_all_events_text"
                            class="form-control"
                            value="{{ $rsvpAllEventsText }}"
                            placeholder="Contoh: Hadir Semua"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">03</div>

                    <div>
                        <h3 class="form-section-title">
                            Jumlah Tamu dan Tombol
                        </h3>

                        <div class="form-section-subtitle">
                            Atur teks jumlah tamu, tombol submit, dan tombol ubah RSVP.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Pertanyaan Jumlah Tamu
                        </label>

                        <input
                            type="text"
                            name="rsvp_total_question"
                            class="form-control"
                            value="{{ $rsvpTotalQuestion }}"
                            placeholder="Contoh: Jumlah tamu yang datang termasuk kamu?"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Submit
                        </label>

                        <input
                            type="text"
                            name="rsvp_submit_text"
                            class="form-control"
                            value="{{ $rsvpSubmitText }}"
                            placeholder="Contoh: Send RSVP"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Ubah RSVP
                        </label>

                        <input
                            type="text"
                            name="rsvp_change_text"
                            class="form-control"
                            value="{{ $rsvpChangeText }}"
                            placeholder="Contoh: Change"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">04</div>

                    <div>
                        <h3 class="form-section-title">
                            Pesan Setelah RSVP
                        </h3>

                        <div class="form-section-subtitle">
                            Teks ini muncul setelah tamu mengirim RSVP.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Pesan Hadir
                        </label>

                        <input
                            type="text"
                            name="rsvp_success_attend_title"
                            class="form-control"
                            value="{{ $rsvpSuccessAttendTitle }}"
                            placeholder="Contoh: Will Attend"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Judul Pesan Tidak Hadir
                        </label>

                        <input
                            type="text"
                            name="rsvp_success_not_attend_title"
                            class="form-control"
                            value="{{ $rsvpSuccessNotAttendTitle }}"
                            placeholder="Contoh: Unable to Attend"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Pesan Hadir
                        </label>

                        <textarea
                            name="rsvp_success_attend_caption"
                            class="form-control"
                            rows="4"
                        >{{ $rsvpSuccessAttendCaption }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Pesan Tidak Hadir
                        </label>

                        <textarea
                            name="rsvp_success_not_attend_caption"
                            class="form-control"
                            rows="4"
                        >{{ $rsvpSuccessNotAttendCaption }}</textarea>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan RSVP
                </button>
            </div>
        </form>
    </div>
</section>