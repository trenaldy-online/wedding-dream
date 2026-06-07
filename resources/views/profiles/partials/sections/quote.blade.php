@php
    $quoteSection = $quoteSection ?? null;
    $quoteContent = $quoteSection ? ($quoteSection->content ?? []) : [];

    $quoteText = old(
        'quote_text',
        $quoteContent['text'] ?? '“Love is not about finding someone to live with, but finding someone you can’t imagine life without.”'
    );

    $quoteSource = old('quote_source', $quoteContent['source'] ?? '');
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Quote
            </h2>

            <p class="invitation-form-desc">
                Atur kutipan yang tampil pada section Quote template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.quote.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">QT</div>

                    <div>
                        <h3 class="form-section-title">
                            Kutipan Undangan
                        </h3>

                        <div class="form-section-subtitle">
                            Data ini akan tampil di bagian quote setelah section Couple.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Isi Quote
                        </label>

                        <textarea
                            name="quote_text"
                            class="form-control"
                            rows="5"
                            placeholder="Tulis quote undangan"
                        >{{ $quoteText }}</textarea>
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Sumber Quote
                        </label>

                        <input
                            type="text"
                            name="quote_source"
                            class="form-control"
                            value="{{ $quoteSource }}"
                            placeholder="Contoh: QS. Ar-Rum: 21"
                        >

                        <div class="form-help">
                            Boleh dikosongkan jika tidak ingin menampilkan sumber quote.
                        </div>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Quote
                </button>
            </div>
        </form>
    </div>
</section>