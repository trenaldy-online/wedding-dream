@php
    $storySection = $storySection ?? null;
    $storyContent = $storySection ? ($storySection->content ?? []) : [];

    $storyTitle = old('story_title', $storyContent['title'] ?? 'Our Story');

    $storyItems = old('story_items', $storyContent['items'] ?? []);

    if (empty($storyItems)) {
        $storyItems = [
            [
                'title' => 'First Meet',
                'date' => '',
                'description' => 'Our story began with a simple meeting that slowly became something meaningful.',
                'image' => 'bg-cover.png',
            ],
        ];
    }

    $storyItems = array_values($storyItems);

    $storyImageUrl = function ($image) {
        $image = trim($image ?? '');

        if ($image === '') {
            return asset('templates/anselma/files/bg-cover.png');
        }

        if (preg_match('/^https?:\/\//', $image)) {
            return $image;
        }

        if (str_starts_with($image, 'story/')) {
            return asset('storage/' . $image);
        }

        return asset('templates/anselma/files/' . $image);
    };
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Our Story
            </h2>

            <p class="invitation-form-desc">
                Atur cerita perjalanan pasangan. Kamu bisa menambah, menghapus, dan mengunggah foto untuk setiap story.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.story.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">OS</div>

                    <div>
                        <h3 class="form-section-title">
                            Judul Story
                        </h3>

                        <div class="form-section-subtitle">
                            Judul utama yang tampil di section story.
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
                            name="story_title"
                            class="form-control"
                            value="{{ $storyTitle }}"
                            placeholder="Contoh: Our Story"
                        >
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">+</div>

                    <div>
                        <h3 class="form-section-title">
                            Daftar Story
                        </h3>

                        <div class="form-section-subtitle">
                            Tambahkan story sesuai kebutuhan pasangan.
                        </div>
                    </div>
                </div>

                <div id="storyItemsWrap">
                    @foreach ($storyItems as $index => $story)
                        <div class="section-manager-item story-admin-item" style="align-items: flex-start; margin-bottom: 16px;">
                            <div class="section-manager-main">
                                <div class="section-manager-icon story-number">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </div>

                                <div>
                                    <div class="section-manager-title">
                                        Story {{ $index + 1 }}
                                    </div>

                                    <div class="section-manager-key">
                                        story_item_{{ $index + 1 }}
                                    </div>
                                </div>
                            </div>

                            <div style="flex: 1; width: 100%;">
                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Judul Cerita
                                        </label>

                                        <input
                                            type="text"
                                            name="story_items[{{ $index }}][title]"
                                            class="form-control"
                                            value="{{ $story['title'] ?? '' }}"
                                            placeholder="Contoh: First Meet"
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            Tanggal / Keterangan Waktu
                                        </label>

                                        <input
                                            type="text"
                                            name="story_items[{{ $index }}][date]"
                                            class="form-control"
                                            value="{{ $story['date'] ?? '' }}"
                                            placeholder="Contoh: 2021"
                                        >
                                    </div>
                                </div>

                                <div class="form-grid-1" style="margin-top: 18px;">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Deskripsi Cerita
                                        </label>

                                        <textarea
                                            name="story_items[{{ $index }}][description]"
                                            class="form-control"
                                            rows="5"
                                            placeholder="Tulis deskripsi cerita"
                                        >{{ $story['description'] ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="form-grid-2" style="margin-top: 18px;">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Upload Foto Story
                                        </label>

                                        <input
                                            type="file"
                                            name="story_items[{{ $index }}][image_file]"
                                            class="form-control story-image-input"
                                            accept="image/*"
                                        >

                                        <input
                                            type="hidden"
                                            name="story_items[{{ $index }}][existing_image]"
                                            value="{{ $story['image'] ?? '' }}"
                                        >

                                        <div class="form-help">
                                            Format: JPG, JPEG, PNG, atau WEBP. Minimal 720×960 px, ideal portrait 3:4, maksimal 4 MB.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            Preview Foto Saat Ini
                                        </label>

                                        <div class="story-crop-preview-box">
                                            <div class="story-crop-preview-frame">
                                                <img
                                                    src="{{ $storyImageUrl($story['image'] ?? '') }}"
                                                    alt="Story preview"
                                                    class="story-image-preview"
                                                >
                                            </div>

                                            <div class="story-crop-guide">
                                                Rekomendasi foto <strong>portrait 3:4</strong>, minimal <strong>720×960 px</strong>,
                                                ideal <strong>1200×1580 px</strong>, maksimal <strong>4 MB</strong>.
                                                Area tengah foto adalah area aman karena gambar akan otomatis memenuhi frame dan ter-crop dari tengah.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-top: 18px;">
                                    <button type="button" class="btn-soft-inline story-remove-btn">
                                        Hapus Story
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn-soft-inline" id="addStoryBtn">
                    + Tambah Story
                </button>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Our Story
                </button>
            </div>
        </form>
    </div>
</section>

<template id="storyItemTemplate">
    <div class="section-manager-item story-admin-item" style="align-items: flex-start; margin-bottom: 16px;">
        <div class="section-manager-main">
            <div class="section-manager-icon story-number">
                __NUMBER__
            </div>

            <div>
                <div class="section-manager-title">
                    Story __DISPLAY__
                </div>

                <div class="section-manager-key">
                    story_item___DISPLAY__
                </div>
            </div>
        </div>

        <div style="flex: 1; width: 100%;">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">
                        Judul Cerita
                    </label>

                    <input
                        type="text"
                        name="story_items[__INDEX__][title]"
                        class="form-control"
                        placeholder="Contoh: First Meet"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Tanggal / Keterangan Waktu
                    </label>

                    <input
                        type="text"
                        name="story_items[__INDEX__][date]"
                        class="form-control"
                        placeholder="Contoh: 2021"
                    >
                </div>
            </div>

            <div class="form-grid-1" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">
                        Deskripsi Cerita
                    </label>

                    <textarea
                        name="story_items[__INDEX__][description]"
                        class="form-control"
                        rows="5"
                        placeholder="Tulis deskripsi cerita"
                    ></textarea>
                </div>
            </div>

            <div class="form-grid-2" style="margin-top: 18px;">
                <div class="form-group">
                    <label class="form-label">
                        Upload Foto Story
                    </label>

                    <input
                        type="file"
                        name="story_items[__INDEX__][image_file]"
                        class="form-control story-image-input"
                        accept="image/*"
                    >

                    <input
                        type="hidden"
                        name="story_items[__INDEX__][existing_image]"
                        value=""
                    >

                    <div class="form-help">
                        Format: JPG, JPEG, PNG, atau WEBP. Minimal 720×960 px, ideal portrait 3:4, maksimal 4 MB.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Preview Foto
                    </label>

                    <div class="story-crop-preview-box">
                        <div class="story-crop-preview-frame">
                            <img
                                src="{{ asset('templates/anselma/files/bg-cover.png') }}"
                                alt="Story preview"
                                class="story-image-preview"
                            >
                        </div>

                        <div class="story-crop-guide">
                            Rekomendasi foto <strong>portrait 3:4</strong>, minimal <strong>720×960 px</strong>,
                            ideal <strong>1200×1580 px</strong>, maksimal <strong>4 MB</strong>.
                            Area tengah foto adalah area aman.
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <button type="button" class="btn-soft-inline story-remove-btn">
                    Hapus Story
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const wrap = document.getElementById("storyItemsWrap");
    const addBtn = document.getElementById("addStoryBtn");
    const template = document.getElementById("storyItemTemplate");

    if (!wrap || !addBtn || !template) {
        return;
    }

    function padNumber(number) {
        return String(number).padStart(2, "0");
    }

    function refreshStoryIndexes() {
        const items = [...wrap.querySelectorAll(".story-admin-item")];

        items.forEach(function (item, index) {
            const display = index + 1;

            item.querySelectorAll("input, textarea").forEach(function (field) {
                if (!field.name) {
                    return;
                }

                field.name = field.name.replace(/story_items\[\d+\]/, "story_items[" + index + "]");
            });

            const number = item.querySelector(".story-number");

            if (number) {
                number.textContent = padNumber(display);
            }

            const title = item.querySelector(".section-manager-title");

            if (title) {
                title.textContent = "Story " + display;
            }

            const key = item.querySelector(".section-manager-key");

            if (key) {
                key.textContent = "story_item_" + display;
            }
        });
    }

    function bindImagePreview(scope) {
        scope.querySelectorAll(".story-image-input").forEach(function (input) {
            input.addEventListener("change", function () {
                const file = input.files && input.files[0];

                if (!file) {
                    return;
                }

                const preview = input
                    .closest(".story-admin-item")
                    ?.querySelector(".story-image-preview");

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
        const index = wrap.querySelectorAll(".story-admin-item").length;
        const display = index + 1;

        const html = template.innerHTML
            .replaceAll("__INDEX__", index)
            .replaceAll("__DISPLAY__", display)
            .replaceAll("__NUMBER__", padNumber(display));

        wrap.insertAdjacentHTML("beforeend", html);

        const newItem = wrap.lastElementChild;

        bindImagePreview(newItem);
        refreshStoryIndexes();
    });

    wrap.addEventListener("click", function (event) {
        const removeButton = event.target.closest(".story-remove-btn");

        if (!removeButton) {
            return;
        }

        const items = wrap.querySelectorAll(".story-admin-item");

        if (items.length <= 1) {
            alert("Minimal harus ada 1 story.");
            return;
        }

        removeButton.closest(".story-admin-item").remove();
        refreshStoryIndexes();
    });

    bindImagePreview(wrap);
    refreshStoryIndexes();
});
</script>