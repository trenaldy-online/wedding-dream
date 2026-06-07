@php
    $footerSection = $footerSection ?? null;
    $footerContent = $footerSection ? ($footerSection->content ?? []) : [];

    $footerTitle = old('footer_title', $footerContent['title'] ?? 'Thank You');
    $footerDescription = old(
        'footer_description',
        $footerContent['description'] ?? 'With love and gratitude, thank you for celebrating with us.'
    );
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Footer / Closing
            </h2>

            <p class="invitation-form-desc">
                Atur teks penutup undangan tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.footer.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">FT</div>

                    <div>
                        <h3 class="form-section-title">
                            Teks Penutup
                        </h3>

                        <div class="form-section-subtitle">
                            Teks ini muncul pada bagian closing paling bawah undangan.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Footer
                        </label>

                        <input
                            type="text"
                            name="footer_title"
                            class="form-control"
                            value="{{ $footerTitle }}"
                            placeholder="Contoh: Thank You"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Footer
                        </label>

                        <textarea
                            name="footer_description"
                            class="form-control"
                            rows="5"
                            placeholder="Contoh: With love and gratitude, thank you for celebrating with us."
                        >{{ $footerDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Footer
                </button>
            </div>
        </form>
    </div>
</section>