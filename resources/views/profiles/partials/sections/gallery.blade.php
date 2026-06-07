@php
    $gallerySection = $gallerySection ?? null;
    $galleryContent = $gallerySection ? ($gallerySection->content ?? []) : [];

    $galleryTitle = old('gallery_title', $galleryContent['title'] ?? 'Portraits of Us');

    $galleryItems = old('gallery_items', $galleryContent['items'] ?? []);

    if (empty($galleryItems)) {
        $galleryItems = [
            [
                'caption' => '',
                'image' => 'bg-cover.png',
            ],
        ];
    }

    $galleryItems = array_values($galleryItems);

    $galleryImageUrl = function ($image) {
        $image = trim($image ?? '');

        if ($image === '') {
            return asset('templates/anselma/files/bg-cover.png');
        }

        if (preg_match('/^https?:\/\//', $image)) {
            return $image;
        }

        if (str_starts_with($image, 'gallery/')) {
            return asset('storage/' . $image);
        }

        return asset('templates/anselma/files/' . $image);
    };
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Gallery
            </h2>

            <p class="invitation-form-desc">
                Atur foto-foto yang tampil pada section Gallery template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.gallery.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">GL</div>

                    <div>
                        <h3 class="form-section-title">
                            Judul Gallery
                        </h3>

                        <div class="form-section-subtitle">
                            Judul utama yang tampil di section gallery.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Section
                        </label>

                        <input
                            type="text"
                            name="gallery_title"
                            class="form-control"
                            value="{{ $galleryTitle }}"
                            placeholder="Contoh: Portraits of Us"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">+</div>

                    <div>
                        <h3 class="form-section-title">
                            Daftar Foto Gallery
                        </h3>

                        <div class="form-section-subtitle">
                            Tambahkan foto sesuai kebutuhan. Foto square 1:1 paling aman.
                        </div>
                    </div>
                </div>

                <div id="galleryItemsWrap">
                    @foreach ($galleryItems as $index => $item)
                        <div class="section-manager-item gallery-admin-item" style="align-items: flex-start; margin-bottom: 16px;">
                            <div class="section-manager-main">
                                <div class="section-manager-icon gallery-number">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </div>

                                <div>
                                    <div class="section-manager-title">
                                        Foto {{ $index + 1 }}
                                    </div>

                                    <div class="section-manager-key">
                                        gallery_item_{{ $index + 1 }}
                                    </div>
                                </div>
                            </div>

                            <div style="flex: 1; width: 100%;">
                                <div class="form-grid-1">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Caption Foto
                                        </label>

                                        <input
                                            type="text"
                                            name="gallery_items[{{ $index }}][caption]"
                                            class="form-control"
                                            value="{{ $item['caption'] ?? '' }}"
                                            placeholder="Opsional"
                                        >
                                    </div>
                                </div>

                                <div class="form-grid-2" style="margin-top: 18px;">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Upload Foto Gallery
                                        </label>

                                        <input
                                            type="file"
                                            name="gallery_items[{{ $index }}][image_file]"
                                            class="form-control gallery-image-input"
                                            accept="image/*"
                                        >

                                        <input
                                            type="hidden"
                                            name="gallery_items[{{ $index }}][existing_image]"
                                            value="{{ $item['image'] ?? '' }}"
                                        >

                                        <div class="form-help">
                                            Format JPG, JPEG, PNG, WEBP. Minimal 720×720 px, ideal square 1:1, maksimal 4 MB.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            Preview Foto
                                        </label>

                                        <div class="story-crop-preview-box">
                                            <div class="story-crop-preview-frame" style="aspect-ratio: 1 / 1;">
                                                <img
                                                    src="{{ $galleryImageUrl($item['image'] ?? '') }}"
                                                    alt="Gallery preview"
                                                    class="gallery-image-preview"
                                                >
                                            </div>

                                            <div class="story-crop-guide">
                                                Area tengah foto adalah area aman. Foto akan memenuhi frame dan bisa ter-crop dari tengah.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 18px;">
                                    <button type="button" class="btn-soft-inline gallery-remove-btn">
                                        Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn-soft-inline" id="addGalleryBtn">
                    + Tambah Foto
                </button>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Gallery
                </button>
            </div>
        </form>
    </div>
</section>
<template id="galleryItemTemplate">
    <div class="section-manager-item gallery-admin-item" style="align-items: flex-start; margin-bottom: 16px;">
        <div class="section-manager-main">
            <div class="section-manager-icon gallery-number">
                __NUMBER__
            </div>

            <div>
                <div class="section-manager-title">
                    Foto __DISPLAY__
                </div>

                <div class="section-manager-key">
                    gallery_item___DISPLAY__
                </div>
            </div>
        </div>

        <div style="flex: 1; width: 100%;">
            <div class="form-grid-1">
                <div class="form-group">
                    <label class="form-label">
                        Caption Foto
                    </label>

                    <input
                        type="text"
                        name="gallery_items[__INDEX__][caption]"
                        class="form-control"
                        placeholder="Opsional"
                    >
                </div>
            </div>

            <div class="form-grid-2" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">
                        Upload Foto Gallery
                    </label>

                    <input
                        type="file"
                        name="gallery_items[__INDEX__][image_file]"
                        class="form-control gallery-image-input"
                        accept="image/*"
                    >

                    <input
                        type="hidden"
                        name="gallery_items[__INDEX__][existing_image]"
                        value=""
                    >

                    <div class="form-help">
                        Format JPG, JPEG, PNG, WEBP. Minimal 720×720 px, ideal square 1:1, maksimal 4 MB.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Preview Foto
                    </label>

                    <div class="story-crop-preview-box">
                        <div class="story-crop-preview-frame" style="aspect-ratio: 1 / 1;">
                            <img
                                src="{{ asset('templates/anselma/files/bg-cover.png') }}"
                                alt="Gallery preview"
                                class="gallery-image-preview"
                            >
                        </div>

                        <div class="story-crop-guide">
                            Area tengah foto adalah area aman. Foto akan memenuhi frame dan bisa ter-crop dari tengah.
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <button type="button" class="btn-soft-inline gallery-remove-btn">
                    Hapus Foto
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const wrap = document.getElementById("galleryItemsWrap");
    const addBtn = document.getElementById("addGalleryBtn");
    const template = document.getElementById("galleryItemTemplate");

    if (!wrap || !addBtn || !template) {
        return;
    }

    function padNumber(number) {
        return String(number).padStart(2, "0");
    }

    function refreshGalleryIndexes() {
        const items = [...wrap.querySelectorAll(".gallery-admin-item")];

        items.forEach(function (item, index) {
            const display = index + 1;

            item.querySelectorAll("input, textarea").forEach(function (field) {
                if (!field.name) {
                    return;
                }

                field.name = field.name.replace(/gallery_items\[\d+\]/, "gallery_items[" + index + "]");
            });

            const number = item.querySelector(".gallery-number");

            if (number) {
                number.textContent = padNumber(display);
            }

            const title = item.querySelector(".section-manager-title");

            if (title) {
                title.textContent = "Foto " + display;
            }

            const key = item.querySelector(".section-manager-key");

            if (key) {
                key.textContent = "gallery_item_" + display;
            }
        });
    }

    function bindImagePreview(scope) {
        scope.querySelectorAll(".gallery-image-input").forEach(function (input) {
            if (input.dataset.previewBound === "1") {
                return;
            }

            input.dataset.previewBound = "1";

            input.addEventListener("change", function () {
                const file = input.files && input.files[0];

                if (!file) {
                    return;
                }

                const preview = input
                    .closest(".gallery-admin-item")
                    ?.querySelector(".gallery-image-preview");

                if (!preview) {
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                };

                reader.readAsDataURL(file);
            });
        });
    }

    addBtn.addEventListener("click", function () {
        const index = wrap.querySelectorAll(".gallery-admin-item").length;
        const display = index + 1;

        const html = template.innerHTML
            .replaceAll("__INDEX__", index)
            .replaceAll("__DISPLAY__", display)
            .replaceAll("__NUMBER__", padNumber(display));

        wrap.insertAdjacentHTML("beforeend", html);

        const newItem = wrap.lastElementChild;

        bindImagePreview(newItem);
        refreshGalleryIndexes();
    });

    wrap.addEventListener("click", function (event) {
        const removeButton = event.target.closest(".gallery-remove-btn");

        if (!removeButton) {
            return;
        }

        const items = wrap.querySelectorAll(".gallery-admin-item");

        if (items.length <= 1) {
            alert("Minimal harus ada 1 foto gallery.");
            return;
        }

        removeButton.closest(".gallery-admin-item").remove();
        refreshGalleryIndexes();
    });

    bindImagePreview(wrap);
    refreshGalleryIndexes();
});
</script>