@php
    $agendaSection = $agendaSection ?? null;
    $agendaContent = $agendaSection ? ($agendaSection->content ?? []) : [];

    $agendaTitle = old('agenda_title', $agendaContent['title'] ?? "It's Wedding Day");
    $agendaDay = old('agenda_day', $agendaContent['day'] ?? 'Saturday,');
    $agendaDate = old('agenda_date', $agendaContent['date'] ?? '31 January 2026');
    $agendaSameLocation = old('agenda_same_location', $agendaContent['same_location'] ?? true);
    $agendaMapsButtonText = old('agenda_maps_button_text', $agendaContent['maps_button_text'] ?? 'View Maps');

    $agendaActivities = old('activities', $agendaContent['activities'] ?? []);

    if (empty($agendaActivities)) {
        $agendaActivities = [
            [
                'title' => 'Akad Nikah',
                'time' => '09:00 - 10:00',
                'hall' => 'Mason Pine Hotel',
                'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                'city' => 'Kabupaten Bandung Barat',
                'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
            ],
            [
                'title' => 'Resepsi',
                'time' => '11:00 - 14:00',
                'hall' => 'Mason Pine Hotel',
                'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                'city' => 'Kabupaten Bandung Barat',
                'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
            ],
        ];
    }

    $agendaActivities = array_values($agendaActivities);
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Agenda
            </h2>

            <p class="invitation-form-desc">
                Atur teks pada section Agenda tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.agenda.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">AG</div>

                    <div>
                        <h3 class="form-section-title">
                            Judul dan Tanggal Agenda
                        </h3>

                        <div class="form-section-subtitle">
                            Teks ini akan mengganti tulisan pada section Agenda original.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Agenda
                        </label>

                        <input
                            type="text"
                            name="agenda_title"
                            class="form-control"
                            value="{{ $agendaTitle }}"
                            placeholder="Contoh: It's Wedding Day"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Maps
                        </label>

                        <input
                            type="text"
                            name="agenda_maps_button_text"
                            class="form-control"
                            value="{{ $agendaMapsButtonText }}"
                            placeholder="Contoh: View Maps"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Hari
                        </label>

                        <input
                            type="text"
                            name="agenda_day"
                            class="form-control"
                            value="{{ $agendaDay }}"
                            placeholder="Contoh: Saturday,"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Tanggal
                        </label>

                        <input
                            type="text"
                            name="agenda_date"
                            class="form-control"
                            value="{{ $agendaDate }}"
                            placeholder="Contoh: 31 January 2026"
                        >
                    </div>
                </div>

                <label class="section-toggle" style="margin-top: 18px;">
                    <input
                        type="checkbox"
                        name="agenda_same_location"
                        value="1"
                        {{ $agendaSameLocation ? 'checked' : '' }}
                    >

                    <span class="section-toggle-slider"></span>

                    <span class="section-toggle-text">
                        Kedua acara berada di lokasi yang sama
                    </span>
                </label>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">01</div>

                    <div>
                        <h3 class="form-section-title">
                            Detail Acara
                        </h3>

                        <div class="form-section-subtitle">
                            Saat ini dibuat 2 acara utama agar tetap mengikuti tampilan original: Akad dan Resepsi.
                        </div>
                    </div>
                </div>

                @foreach ($agendaActivities as $index => $activity)
                    <div class="section-manager-item" style="align-items: flex-start; margin-bottom: 16px;">
                        <div class="section-manager-main">
                            <div class="section-manager-icon">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div>
                                <div class="section-manager-title">
                                    Acara {{ $index + 1 }}
                                </div>

                                <div class="section-manager-key">
                                    agenda_activity_{{ $index + 1 }}
                                </div>
                            </div>
                        </div>

                        <div style="flex: 1; width: 100%;">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Acara
                                    </label>

                                    <input
                                        type="text"
                                        name="activities[{{ $index }}][title]"
                                        class="form-control"
                                        value="{{ $activity['title'] ?? '' }}"
                                        placeholder="Contoh: Akad Nikah"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Waktu Acara
                                    </label>

                                    <input
                                        type="text"
                                        name="activities[{{ $index }}][time]"
                                        class="form-control"
                                        value="{{ $activity['time'] ?? '' }}"
                                        placeholder="Contoh: 09:00 - 10:00"
                                    >
                                </div>
                            </div>

                            <div class="form-grid-2" style="margin-top: 18px;">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Gedung / Hall
                                    </label>

                                    <input
                                        type="text"
                                        name="activities[{{ $index }}][hall]"
                                        class="form-control"
                                        value="{{ $activity['hall'] ?? '' }}"
                                        placeholder="Contoh: Mason Pine Hotel"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Kota
                                    </label>

                                    <input
                                        type="text"
                                        name="activities[{{ $index }}][city]"
                                        class="form-control"
                                        value="{{ $activity['city'] ?? '' }}"
                                        placeholder="Contoh: Kabupaten Bandung Barat"
                                    >
                                </div>
                            </div>

                            <div class="form-grid-1" style="margin-top: 18px;">
                                <div class="form-group">
                                    <label class="form-label">
                                        Alamat
                                    </label>

                                    <textarea
                                        name="activities[{{ $index }}][address]"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Masukkan alamat acara"
                                    >{{ $activity['address'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <div class="form-grid-1" style="margin-top: 18px;">
                                <div class="form-group">
                                    <label class="form-label">
                                        Link Google Maps
                                    </label>

                                    <input
                                        type="url"
                                        name="activities[{{ $index }}][maps_url]"
                                        class="form-control"
                                        value="{{ $activity['maps_url'] ?? '' }}"
                                        placeholder="https://maps.google.com/..."
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Agenda
                </button>
            </div>
        </form>
    </div>
</section>