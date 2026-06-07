@php
    $coverSection = $coverSection ?? null;
    $coverContent = $coverSection ? ($coverSection->content ?? []) : [];

    $coverOpeningButtonText = old('cover_opening_button_text', $coverContent['opening_button_text'] ?? 'Open Invitation');
    $coverLabel = old('cover_label', $coverContent['label'] ?? 'Wedding Invitation');
    $coverCoupleName = old('cover_couple_name', $coverContent['couple_name'] ?? (($profile->groom_name ?? 'Ansel') . ' & ' . ($profile->bride_name ?? 'Varo')));
    $coverHashtag = old('cover_hashtag', $coverContent['hashtag'] ?? '#AnselVaroInLove');

    $coverLoaderEnabled = old('cover_loader_enabled', $coverContent['loader_enabled'] ?? true);
    $coverLoaderMarkType = old('cover_loader_mark_type', $coverContent['loader_mark_type'] ?? 'initial');
    $coverLoaderMark = old('cover_loader_mark', $coverContent['loader_mark'] ?? '');
    $coverLoadingText = old('cover_loading_text', $coverContent['loading_text'] ?? 'One moment...');

    $coverOpeningSubtitle = old('cover_opening_subtitle', $coverContent['opening_subtitle'] ?? 'The Wedding Of');
    $coverOpeningGreetingPrefix = old('cover_opening_greeting_prefix', $coverContent['greeting_prefix'] ?? 'Hai');
    $coverOpeningVideo = old('cover_opening_video', $coverContent['opening_video'] ?? 'vid-comp.mp4');
    $coverOpeningPoster = old('cover_opening_poster', $coverContent['opening_poster'] ?? 'bg-cover.png');

    $coverLogo = $coverContent['logo'] ?? null;
    $coverMainImage = $coverContent['main_image'] ?? null;
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Cover / Opening
            </h2>

            <p class="invitation-form-desc">
                Atur teks cover dan opening tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.cover.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">CV</div>

                    <div>
                        <h3 class="form-section-title">
                            Teks Cover
                        </h3>

                        <div class="form-section-subtitle">
                            Teks ini akan mengganti tulisan pada bagian cover dan tombol opening.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Teks Tombol Opening
                        </label>

                        <input
                            type="text"
                            name="cover_opening_button_text"
                            class="form-control"
                            value="{{ $coverOpeningButtonText }}"
                            placeholder="Contoh: Open Invitation"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Label Cover
                        </label>

                        <input
                            type="text"
                            name="cover_label"
                            class="form-control"
                            value="{{ $coverLabel }}"
                            placeholder="Contoh: Wedding Invitation"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Nama Pasangan di Cover
                        </label>

                        <input
                            type="text"
                            name="cover_couple_name"
                            class="form-control"
                            value="{{ $coverCoupleName }}"
                            placeholder="Contoh: Dinda & Teguh"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Hashtag
                        </label>

                        <input
                            type="text"
                            name="cover_hashtag"
                            class="form-control"
                            value="{{ $coverHashtag }}"
                            placeholder="Contoh: #DindaTeguhInLove"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">LD</div>

                    <div>
                        <h3 class="form-section-title">
                            Loading / Preloader
                        </h3>

                        <div class="form-section-subtitle">
                            Atur tampilan loading sebelum undangan muncul.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Status Loading
                        </label>

                        <input type="hidden" name="cover_loader_enabled" value="0">

                        <label class="section-toggle" style="height: 52px;">
                            <input
                                type="checkbox"
                                name="cover_loader_enabled"
                                value="1"
                                {{ $coverLoaderEnabled ? 'checked' : '' }}
                            >

                            <span class="section-toggle-slider"></span>

                            <span class="section-toggle-text">
                                Tampilkan loading screen
                            </span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Tipe Loader Mark
                        </label>

                        <select name="cover_loader_mark_type" class="form-control">
                            <option value="initial" {{ $coverLoaderMarkType === 'initial' ? 'selected' : '' }}>
                                Gunakan Inisial
                            </option>

                            <option value="logo" {{ $coverLoaderMarkType === 'logo' ? 'selected' : '' }}>
                                Gunakan Logo Cover
                            </option>
                        </select>

                        <div class="form-help">
                            Jika memilih Logo Cover, gambar diambil dari upload Logo Cover di bawah.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Teks Loading
                        </label>

                        <input
                            type="text"
                            name="cover_loading_text"
                            class="form-control"
                            value="{{ $coverLoadingText }}"
                            placeholder="Contoh: One moment..."
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Inisial / Loader Mark
                        </label>

                        <input
                            type="text"
                            name="cover_loader_mark"
                            class="form-control"
                            value="{{ $coverLoaderMark }}"
                            placeholder="Contoh: D&T"
                        >

                        <div class="form-help">
                            Dipakai jika tipe loader adalah Inisial. Jika dikosongkan, sistem otomatis memakai inisial nama mempelai.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">OP</div>

                    <div>
                        <h3 class="form-section-title">
                            Opening Gate
                        </h3>

                        <div class="form-section-subtitle">
                            Atur halaman sebelum tamu menekan tombol Open Invitation.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Subtitle Opening
                        </label>

                        <input
                            type="text"
                            name="cover_opening_subtitle"
                            class="form-control"
                            value="{{ $coverOpeningSubtitle }}"
                            placeholder="Contoh: The Wedding Of"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Sapaan
                        </label>

                        <input
                            type="text"
                            name="cover_opening_greeting_prefix"
                            class="form-control"
                            value="{{ $coverOpeningGreetingPrefix }}"
                            placeholder="Contoh: Hai"
                        >

                        <div class="form-help">
                            Nama tamu tetap otomatis mengikuti data guest.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            File Video Opening
                        </label>

                        <input
                            type="text"
                            name="cover_opening_video"
                            class="form-control"
                            value="{{ $coverOpeningVideo }}"
                            placeholder="Contoh: vid-comp.mp4"
                        >

                        <div class="form-help">
                            File harus berada di folder public/templates/anselma/files.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            File Poster / Background Opening
                        </label>

                        <input
                            type="text"
                            name="cover_opening_poster"
                            class="form-control"
                            value="{{ $coverOpeningPoster }}"
                            placeholder="Contoh: bg-cover.png"
                        >

                        <div class="form-help">
                            Dipakai sebagai poster video dan fallback background.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">LG</div>

                    <div>
                        <h3 class="form-section-title">
                            Logo Cover
                        </h3>

                        <div class="form-section-subtitle">
                            Upload logo/inisial pasangan. Format JPG, PNG, WEBP. Minimal 300 × 300 px.
                        </div>
                    </div>
                </div>

                @if (!empty($coverLogo))
                    <div style="margin-bottom: 16px;">
                        <img
                            src="{{ asset('storage/' . $coverLogo) }}"
                            alt="Cover Logo"
                            style="width: 96px; height: 96px; object-fit: cover; border-radius: 18px; border: 1px solid #e5d8bd;"
                        >
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">
                        Upload Logo Baru
                    </label>

                    <input
                        type="file"
                        name="cover_logo_file"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <div class="form-help">
                        Kosongkan jika tidak ingin mengganti logo.
                    </div>
                </div>
            </div>

            <div class="form-section">
                        <div class="form-section-heading">
                            <div class="form-section-icon">IMG</div>

                            <div>
                                <h3 class="form-section-title">
                                    Gambar Utama Cover
                                </h3>

                                <div class="form-section-subtitle">
                                    Upload gambar atau GIF utama pada cover. Jika ingin tetap bergerak/looping, gunakan file GIF.
                                </div>
                            </div>
                        </div>

                        @if (!empty($coverMainImage))
                            <div style="margin-bottom: 16px;">
                                <img
                                    src="{{ asset('storage/' . $coverMainImage) }}"
                                    alt="Cover Main Image"
                                    style="width: 160px; height: 210px; object-fit: cover; border-radius: 22px; border: 1px solid #e5d8bd;"
                                >
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">
                                Upload Gambar / GIF Cover
                            </label>

                            <input
                                type="file"
                                name="cover_main_image_file"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <div class="form-help">
                                Format: JPG, PNG, WEBP, atau GIF. Maksimal 10MB. Rekomendasi portrait minimal 720 × 960 px.
                            </div>
                        </div>
                    </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Cover
                </button>
            </div>
        </form>
    </div>
</section>