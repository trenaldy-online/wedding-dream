@php
    $weddingWishSection = $weddingWishSection ?? null;
    $weddingWishContent = $weddingWishSection ? ($weddingWishSection->content ?? []) : [];

    $weddingWishTitle = old('title', $weddingWishContent['title'] ?? 'Wedding Wish');
    $weddingWishGuestName = old('guest_name', $weddingWishContent['guest_name'] ?? 'Katsudoto');
    $weddingWishPlaceholder = old('placeholder', $weddingWishContent['placeholder'] ?? 'Give your wish');
    $weddingWishButtonText = old('button_text', $weddingWishContent['button_text'] ?? 'Send');

    $wishes = old('wishes', $weddingWishContent['wishes'] ?? []);

    if (empty($wishes)) {
        $wishes = [
            [
                'name' => 'Katsudoto',
                'date' => '17 Jan 2026, 11:00',
                'message' => 'May your marriage be filled with love, joy, and endless blessings.',
                'verified' => true,
            ],
            [
                'name' => 'Family',
                'date' => '17 Jan 2026, 12:00',
                'message' => 'Selamat menempuh hidup baru. Semoga menjadi keluarga yang bahagia dan penuh berkah.',
                'verified' => true,
            ],
            [
                'name' => 'Best Friend',
                'date' => '17 Jan 2026, 13:00',
                'message' => 'So happy for both of you. Wishing you a lifetime of happiness together.',
                'verified' => false,
            ],
        ];
    }

    $wishes = array_values($wishes);
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Wedding Wish
            </h2>

            <p class="invitation-form-desc">
                Atur teks form ucapan dan contoh komentar yang tampil pada section Wedding Wish template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.wedding-wish.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">WW</div>

                    <div>
                        <h3 class="form-section-title">
                            Pengaturan Form Wish
                        </h3>

                        <div class="form-section-subtitle">
                            Data ini akan tampil pada bagian form ucapan Anselma.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Section
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ $weddingWishTitle }}"
                            placeholder="Contoh: Wedding Wish"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Nama Tamu Default
                        </label>

                        <input
                            type="text"
                            name="guest_name"
                            class="form-control"
                            value="{{ $weddingWishGuestName }}"
                            placeholder="Contoh: Katsudoto"
                        >

                        <div class="form-help">
                            Ini dipakai untuk preview. Nanti pada link tamu personal, nama tamu bisa mengikuti data guest.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Placeholder Komentar
                        </label>

                        <input
                            type="text"
                            name="placeholder"
                            class="form-control"
                            value="{{ $weddingWishPlaceholder }}"
                            placeholder="Contoh: Give your wish"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol
                        </label>

                        <input
                            type="text"
                            name="button_text"
                            class="form-control"
                            value="{{ $weddingWishButtonText }}"
                            placeholder="Contoh: Send"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">01</div>

                    <div>
                        <h3 class="form-section-title">
                            Contoh Ucapan
                        </h3>

                        <div class="form-section-subtitle">
                            Untuk tahap ini jumlah ucapan mengikuti data yang tersedia. Nanti bisa kita buat fitur tambah/hapus komentar dinamis.
                        </div>
                    </div>
                </div>

                @foreach ($wishes as $index => $wish)
                    <div class="section-manager-item" style="align-items: flex-start; margin-bottom: 16px;">
                        <div class="section-manager-main">
                            <div class="section-manager-icon">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div>
                                <div class="section-manager-title">
                                    Ucapan {{ $index + 1 }}
                                </div>

                                <div class="section-manager-key">
                                    wish_comment_{{ $index + 1 }}
                                </div>
                            </div>
                        </div>

                        <div style="flex: 1; width: 100%;">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Pengirim
                                    </label>

                                    <input
                                        type="text"
                                        name="wishes[{{ $index }}][name]"
                                        class="form-control"
                                        value="{{ $wish['name'] ?? '' }}"
                                        placeholder="Contoh: Katsudoto"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Tanggal
                                    </label>

                                    <input
                                        type="text"
                                        name="wishes[{{ $index }}][date]"
                                        class="form-control"
                                        value="{{ $wish['date'] ?? '' }}"
                                        placeholder="Contoh: 17 Jan 2026, 11:00"
                                    >
                                </div>
                            </div>

                            <div class="form-grid-1" style="margin-top: 18px;">
                                <div class="form-group">
                                    <label class="form-label">
                                        Isi Ucapan
                                    </label>

                                    <textarea
                                        name="wishes[{{ $index }}][message]"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Tulis isi ucapan"
                                    >{{ $wish['message'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <label class="section-toggle" style="margin-top: 4px;">
                                <input
                                    type="checkbox"
                                    name="wishes[{{ $index }}][verified]"
                                    value="1"
                                    {{ !empty($wish['verified']) ? 'checked' : '' }}
                                >

                                <span class="section-toggle-slider"></span>

                                <span class="section-toggle-text">
                                    Verified
                                </span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Wedding Wish
                </button>
            </div>
        </form>
    </div>
</section>