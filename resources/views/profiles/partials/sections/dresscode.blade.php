@php
    $agendaSection = $agendaSection ?? null;
    $agendaContent = $agendaSection ? ($agendaSection->content ?? []) : [];

    $dressCodeContent = $agendaContent['dress_code'] ?? [];

    $dressTitle = old('dress_title', $dressCodeContent['title'] ?? 'Dresscode');
    $dressDescription = old(
        'dress_description',
        $dressCodeContent['description'] ?? 'Wear a long gown or formal gown, black tie or bow tie'
    );
    $dressNote = old(
        'dress_note',
        $dressCodeContent['note'] ?? 'We kindly ask that guests please attend wearing our wedding colors.'
    );

    $dressMenLabel = old('dress_men_label', $dressCodeContent['men_label'] ?? 'Men');
    $dressWomenLabel = old('dress_women_label', $dressCodeContent['women_label'] ?? 'Women');
    $dressMenStyleLabel = old('dress_men_style_label', $dressCodeContent['men_style_label'] ?? 'Formal');
    $dressWomenStyleLabel = old('dress_women_style_label', $dressCodeContent['women_style_label'] ?? 'Formal');

    $dressColors = old('dress_colors', $dressCodeContent['colors'] ?? [
        '#7F0404',
        '#1C3106',
        '#D2CEAE',
        '#E6E3E4',
    ]);

    $dressColors = array_values(array_pad($dressColors, 4, '#D2CEAE'));
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Dresscode
            </h2>

            <p class="invitation-form-desc">
                Atur teks dresscode dan 4 warna utama tanpa mengubah tampilan original Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.dress-code.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">DC</div>

                    <div>
                        <h3 class="form-section-title">
                            Teks Dresscode
                        </h3>

                        <div class="form-section-subtitle">
                            Teks “Formal” bisa diganti, misalnya menjadi Batik, Casual Formal, atau Adat.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Dresscode
                        </label>

                        <input
                            type="text"
                            name="dress_title"
                            class="form-control"
                            value="{{ $dressTitle }}"
                            placeholder="Contoh: Dresscode"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Catatan Warna / Style
                        </label>

                        <input
                            type="text"
                            name="dress_description"
                            class="form-control"
                            value="{{ $dressDescription }}"
                            placeholder="Contoh: Batik formal atau pakaian bernuansa warna berikut"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Label Pria
                        </label>

                        <input
                            type="text"
                            name="dress_men_label"
                            class="form-control"
                            value="{{ $dressMenLabel }}"
                            placeholder="Contoh: Men / Pria"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Style Pria
                        </label>

                        <input
                            type="text"
                            name="dress_men_style_label"
                            class="form-control"
                            value="{{ $dressMenStyleLabel }}"
                            placeholder="Contoh: Formal / Batik"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Label Wanita
                        </label>

                        <input
                            type="text"
                            name="dress_women_label"
                            class="form-control"
                            value="{{ $dressWomenLabel }}"
                            placeholder="Contoh: Women / Wanita"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Teks Style Wanita
                        </label>

                        <input
                            type="text"
                            name="dress_women_style_label"
                            class="form-control"
                            value="{{ $dressWomenStyleLabel }}"
                            placeholder="Contoh: Formal / Batik"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Note Dresscode
                        </label>

                        <textarea
                            name="dress_note"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Kami mohon tamu berkenan mengenakan warna sesuai tema pernikahan."
                        >{{ $dressNote }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">04</div>

                    <div>
                        <h3 class="form-section-title">
                            Warna Dresscode
                        </h3>

                        <div class="form-section-subtitle">
                            Pilih 4 warna menggunakan color picker atau isi kode HEX secara manual.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    @foreach ($dressColors as $index => $color)
                        <div class="form-group">
                            <label class="form-label">
                                Warna {{ $index + 1 }}
                            </label>

                            <div style="display: grid; grid-template-columns: 70px 1fr; gap: 12px; align-items: center;">
                                <input
                                    type="color"
                                    class="form-control dress-color-picker"
                                    value="{{ $color }}"
                                    data-color-index="{{ $index }}"
                                    style="height: 52px; padding: 6px;"
                                >

                                <input
                                    type="text"
                                    name="dress_colors[{{ $index }}]"
                                    class="form-control dress-color-code"
                                    value="{{ $color }}"
                                    data-color-index="{{ $index }}"
                                    placeholder="#7F0404"
                                    maxlength="7"
                                >
                            </div>

                            <div class="form-help">
                                Format kode warna: #RRGGBB, contoh {{ $color }}.
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Dresscode
                </button>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const colorPickers = [...document.querySelectorAll(".dress-color-picker")];
    const colorCodes = [...document.querySelectorAll(".dress-color-code")];

    function normalizeHex(value) {
        value = String(value || "").trim().toUpperCase();

        if (!value.startsWith("#")) {
            value = "#" + value;
        }

        return value;
    }

    function isValidHex(value) {
        return /^#[0-9A-F]{6}$/.test(value);
    }

    colorPickers.forEach(function (picker) {
        picker.addEventListener("input", function () {
            const index = picker.dataset.colorIndex;
            const codeInput = colorCodes.find((input) => input.dataset.colorIndex === index);

            if (codeInput) {
                codeInput.value = picker.value.toUpperCase();
            }
        });
    });

    colorCodes.forEach(function (input) {
        input.addEventListener("input", function () {
            const index = input.dataset.colorIndex;
            const picker = colorPickers.find((item) => item.dataset.colorIndex === index);

            const value = normalizeHex(input.value);

            if (isValidHex(value) && picker) {
                picker.value = value;
                input.value = value;
            }
        });

        input.addEventListener("blur", function () {
            const value = normalizeHex(input.value);

            if (isValidHex(value)) {
                input.value = value;
            }
        });
    });
});
</script>