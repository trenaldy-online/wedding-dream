@php
    $coupleSection = $coupleSection ?? null;
    $coupleContent = $coupleSection ? ($coupleSection->content ?? []) : [];

    $coupleTitle = old('couple_title', $coupleContent['title'] ?? 'Two souls intertwined, a love that will bind');
    $coupleDescription = old(
        'couple_description',
        $coupleContent['description'] ?? 'They say that some souls are simply meant to find each other. Ours did, and with each shared moment, our connection has grown into a love that will forever bind us. We are so excited to celebrate this beautiful journey with you as we exchange our vows.'
    );

    $coupleOrder = $coupleContent['order'] ?? 'groom_first';
    $coupleBrideFirst = old('couple_bride_first', $coupleOrder === 'bride_first');

    $groomContent = $coupleContent['groom'] ?? [];
    $brideContent = $coupleContent['bride'] ?? [];

    $groomName = old('groom_name', $groomContent['name'] ?? 'Varo Brian');
    $groomParents = old('groom_parents', $groomContent['parents'] ?? "The Son of <br> Mr. Lerry Brian <br> & Mrs. Lenny Diah");
    $groomInstagram = old('groom_instagram', $groomContent['instagram'] ?? '@katsudoto');
    $groomInstagramUrl = old('groom_instagram_url', $groomContent['instagram_url'] ?? 'https://www.instagram.com/katsudoto');
    $groomPhoto = $groomContent['photo'] ?? null;

    $brideName = old('bride_name', $brideContent['name'] ?? 'Ansel Ginny');
    $brideParents = old('bride_parents', $brideContent['parents'] ?? "The Daughter of <br> Mr. Darwin Davidson <br> & Mrs. Jenny Smith");
    $brideInstagram = old('bride_instagram', $brideContent['instagram'] ?? '');
    $brideInstagramUrl = old('bride_instagram_url', $brideContent['instagram_url'] ?? '');
    $bridePhoto = $brideContent['photo'] ?? null;

    $coupleStorageImage = function ($path) {
        if (empty($path)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (strpos($path, 'couple/') === 0) {
            return asset('storage/' . $path);
        }

        return asset('templates/anselma/files/' . $path);
    };
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Couple
            </h2>

            <p class="invitation-form-desc">
                Atur data mempelai tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.couple.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">CP</div>

                    <div>
                        <h3 class="form-section-title">
                            Judul dan Deskripsi Couple
                        </h3>

                        <div class="form-section-subtitle">
                            Teks pembuka pada section pasangan.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Couple
                        </label>

                        <input
                            type="text"
                            name="couple_title"
                            class="form-control"
                            value="{{ $coupleTitle }}"
                            placeholder="Contoh: Two souls intertwined, a love that will bind"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Couple
                        </label>

                        <textarea
                            name="couple_description"
                            class="form-control"
                            rows="5"
                            placeholder="Tulis deskripsi pasangan"
                        >{{ $coupleDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">OR</div>

                    <div>
                        <h3 class="form-section-title">
                            Urutan Pasangan
                        </h3>

                        <div class="form-section-subtitle">
                            Aktifkan jika acara ingin menampilkan mempelai wanita terlebih dahulu.
                        </div>
                    </div>
                </div>

                <input type="hidden" name="couple_bride_first" value="0">

                <label class="section-toggle">
                    <input
                        type="checkbox"
                        name="couple_bride_first"
                        value="1"
                        {{ $coupleBrideFirst ? 'checked' : '' }}
                    >

                    <span class="section-toggle-slider"></span>

                    <span class="section-toggle-text">
                        Tampilkan mempelai wanita di atas
                    </span>
                </label>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">PR</div>

                    <div>
                        <h3 class="form-section-title">
                            Data Mempelai Pria
                        </h3>

                        <div class="form-section-subtitle">
                            Nama, orang tua, Instagram, dan foto mempelai pria.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Nama Mempelai Pria
                        </label>

                        <input
                            type="text"
                            name="groom_name"
                            class="form-control"
                            value="{{ $groomName }}"
                            placeholder="Contoh: Teguh Trenasdy"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Instagram Pria
                        </label>

                        <input
                            type="text"
                            name="groom_instagram"
                            class="form-control"
                            value="{{ $groomInstagram }}"
                            placeholder="Contoh: @teguh"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Link Instagram Pria
                        </label>

                        <input
                            type="url"
                            name="groom_instagram_url"
                            class="form-control"
                            value="{{ $groomInstagramUrl }}"
                            placeholder="https://www.instagram.com/username"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Orang Tua Mempelai Pria
                        </label>

                        <textarea
                            name="groom_parents"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Putra dari <br> Bapak ... <br> & Ibu ..."
                        >{{ $groomParents }}</textarea>

                        <div class="form-help">
                            Boleh gunakan &lt;br&gt; untuk pindah baris.
                        </div>
                    </div>
                </div>

                @if ($coupleStorageImage($groomPhoto))
                    <div style="margin: 18px 0;">
                        <img
                            src="{{ $coupleStorageImage($groomPhoto) }}"
                            alt="Foto Groom"
                            style="width: 140px; height: 180px; object-fit: cover; border-radius: 28px 28px 0 0; border: 1px solid #e5d8bd;"
                        >
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">
                        Upload Foto Pria
                    </label>

                    <input
                        type="file"
                        name="groom_photo_file"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <div class="form-help">
                        Format JPG, PNG, WEBP. Maksimal 4MB. Rekomendasi minimal 720 × 720 px.
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">WN</div>

                    <div>
                        <h3 class="form-section-title">
                            Data Mempelai Wanita
                        </h3>

                        <div class="form-section-subtitle">
                            Nama, orang tua, Instagram, dan foto mempelai wanita.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Nama Mempelai Wanita
                        </label>

                        <input
                            type="text"
                            name="bride_name"
                            class="form-control"
                            value="{{ $brideName }}"
                            placeholder="Contoh: Dinda"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Instagram Wanita
                        </label>

                        <input
                            type="text"
                            name="bride_instagram"
                            class="form-control"
                            value="{{ $brideInstagram }}"
                            placeholder="Contoh: @dinda"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Link Instagram Wanita
                        </label>

                        <input
                            type="url"
                            name="bride_instagram_url"
                            class="form-control"
                            value="{{ $brideInstagramUrl }}"
                            placeholder="https://www.instagram.com/username"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Orang Tua Mempelai Wanita
                        </label>

                        <textarea
                            name="bride_parents"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Putri dari <br> Bapak ... <br> & Ibu ..."
                        >{{ $brideParents }}</textarea>

                        <div class="form-help">
                            Boleh gunakan &lt;br&gt; untuk pindah baris.
                        </div>
                    </div>
                </div>

                @if ($coupleStorageImage($bridePhoto))
                    <div style="margin: 18px 0;">
                        <img
                            src="{{ $coupleStorageImage($bridePhoto) }}"
                            alt="Foto Bride"
                            style="width: 140px; height: 180px; object-fit: cover; border-radius: 28px 28px 0 0; border: 1px solid #e5d8bd;"
                        >
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">
                        Upload Foto Wanita
                    </label>

                    <input
                        type="file"
                        name="bride_photo_file"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <div class="form-help">
                        Format JPG, PNG, WEBP. Maksimal 4MB. Rekomendasi minimal 720 × 720 px.
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Couple
                </button>
            </div>
        </form>
    </div>
</section>