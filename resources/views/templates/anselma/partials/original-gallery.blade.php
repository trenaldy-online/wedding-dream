@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $galleryData = array_replace_recursive([
        'title' => 'Portraits of Us',
        'items' => [
            [
                'caption' => '',
                'image' => 'bg-cover.png',
            ],
        ],
    ], $galleryData ?? []);

    $galleryItems = $galleryData['items'] ?? [];

    if (! is_array($galleryItems) || count($galleryItems) === 0) {
        $galleryItems = [
            [
                'caption' => '',
                'image' => 'bg-cover.png',
            ],
        ];
    }

    $galleryImageUrl = function ($image) use ($img) {
        $image = trim($image ?? '');

        if ($image === '') {
            return $img('bg-cover.png');
        }

        if (preg_match('/^https?:\/\//', $image)) {
            return $image;
        }

        if (str_starts_with($image, 'gallery/')) {
            return asset('storage/' . $image);
        }

        return $img($image);
    };
@endphp

<section class="photo-wrap" data-section-order="gallery_photo">
    <div class="orn-photo-4 right">
        <div
            class="image-wrap"
            data-aos="fade-up"
            data-aos-duration="1200"
            data-aos-delay="500"
        >
            <img src="{{ $img('Orn-30.png') }}" alt="">
        </div>
    </div>

    <div class="orn-photo-4 left">
        <div
            class="image-wrap"
            data-aos="fade-up"
            data-aos-duration="1200"
            data-aos-delay="500"
        >
            <img src="{{ $img('Orn-30.png') }}" alt="">
        </div>
    </div>

    <div class="photo-inner">
        <div class="photo-head">
            <h1
                class="photo-title"
                data-aos="fade-up"
                data-aos-duration="1200"
            >
                {{ $galleryData['title'] }}
            </h1>
        </div>

        <div class="photo-body">
            <div class="photo-nav-wrap">
                <div class="photo-item">
                    <div class="preview-wrap" aria-hidden="true">
                        <div class="ornaments-wrapper">
                            <div class="orn-photo-3 right">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-23.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-photo-3 left">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-23.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-photo-2 right">
                                <div class="orn-photo-2-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-left"
                                        data-aos-duration="1300"
                                        data-aos-delay="700"
                                    >
                                        <img src="{{ $img('Orn-28.png') }}" alt="">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="fade-left"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-27.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-photo-2 left">
                                <div class="orn-photo-2-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-left"
                                        data-aos-duration="1300"
                                        data-aos-delay="700"
                                    >
                                        <img src="{{ $img('Orn-28.png') }}" alt="">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="fade-left"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-27.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-photo-1 right">
                                <div class="orn-photo-1-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1200"
                                        data-aos-delay="500"
                                    >
                                        <img src="{{ $img('Orn-29.png') }}" alt="">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="zoom-in"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-26.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-photo-1 left">
                                <div class="orn-photo-1-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1200"
                                        data-aos-delay="500"
                                    >
                                        <img src="{{ $img('Orn-29.png') }}" alt="">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="zoom-in"
                                    data-aos-duration="1200"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-26.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="photo-nav slider-syncing__preview js-photo-main js-lightgallery"
                    data-aos="fade-up"
                    data-aos-duration="1200"
                >
                    @foreach ($galleryItems as $item)
                        @php
                            $imageUrl = $galleryImageUrl($item['image'] ?? '');
                            $caption = $item['caption'] ?? 'Gallery';
                        @endphp

                        <div class="photo-item">
                            <div class="preview-wrap">
                                <div class="photo-img-wrap lightgallery">
                                    <a
                                        href="{{ $imageUrl }}"
                                        class="photo-link"
                                    >
                                        <img
                                            alt="{{ $caption ?: 'Gallery' }}"
                                            class="photo-img"
                                            src="{{ $imageUrl }}"
                                        >
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="photo-slider-wrap">
                <div
                    class="photo-slider slider-syncing__nav js-photo-thumbs"
                    data-aos="fade-up"
                    data-aos-duration="1200"
                >
                    @foreach ($galleryItems as $item)
                        @php
                            $imageUrl = $galleryImageUrl($item['image'] ?? '');
                            $caption = $item['caption'] ?? 'Gallery';
                        @endphp

                        <div class="photo-item">
                            <div class="photo-img-wrap">
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $caption ?: 'Gallery' }}"
                                    class="photo-img"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="photo-arrow next" type="button" aria-label="Next photo">
                    <svg width="108" height="198" viewBox="0 0 108 198" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 190L100 99L8 8" stroke="black" stroke-width="15" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>

                <button class="photo-arrow prev" type="button" aria-label="Previous photo">
                    <svg width="108" height="198" viewBox="0 0 108 198" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 190L8 99L100 8" stroke="black" stroke-width="15" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>