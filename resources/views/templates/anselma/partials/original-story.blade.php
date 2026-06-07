@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $storyData = array_replace_recursive([
        'title' => 'Our Story',
        'items' => [
            [
                'title' => 'First Meet',
                'date' => '',
                'description' => 'Our story began with a simple meeting that slowly became something meaningful.',
                'image' => 'bg-cover.png',
            ],
        ],
    ], $storyData ?? []);

    $storyItems = $storyData['items'] ?? [];

    if (! is_array($storyItems)) {
        $storyItems = [];
    }

    $storyImageUrl = function (?string $image) use ($img) {
        $image = trim($image ?? '');

        if ($image === '') {
            return $img('bg-cover.png');
        }

        if (preg_match('/^https?:\/\//', $image)) {
            return $image;
        }

        if (str_starts_with($image, 'story/')) {
            return asset('storage/' . $image);
        }

        return $img($image);
    };
@endphp

<section class="love-story" data-section-order="story">
    <div class="orn-clip-mask">
        <div
            class="image-wrap"
            data-aos="fade-up"
            data-aos-duration="1200"
            data-aos-delay="500"
        >
            <img src="{{ $img('Orn-clip.png') }}" alt="Ornament">
        </div>
    </div>

    <div class="story-inner">
        <div class="story-head">
            <div class="ornaments-wrapper"></div>

            <h1
                class="story-title"
                data-aos="zoom-in"
                data-aos-duration="1000"
            >
                {{ $storyData['title'] }}
            </h1>
        </div>

        <div class="story-body">
            <div class="story-body-item-2-wrap">
                @forelse ($storyItems as $index => $story)
                    @php
                        $storyTitle = $story['title'] ?? 'Our Moment';
                        $storyDate = $story['date'] ?? '';
                        $storyDescription = $story['description'] ?? '';
                        $storyImage = $storyImageUrl($story['image'] ?? null);
                    @endphp

                    <div class="story-item-wrapper">
                        <div
                            class="step-wrapper"
                            data-aos="fade-down"
                            data-aos-duration="1000"
                        >
                            <div class="step-circle"></div>
                            <div class="step-line"></div>
                        </div>

                        <div class="story-item-wrap">
                            <div
                                class="image-wrap st-im"
                                data-aos="zoom-in"
                                data-aos-duration="1000"
                            >
                                <img src="{{ $img('frame-ls.png') }}" alt="">
                            </div>

                            <div
                                class="story__slider-desc-wrap"
                                data-aos="zoom-in"
                                data-aos-duration="1000"
                            >
                                <div class="story__slider-preview">
                                    <div class="story-preview">
                                        <div
                                            class="story-picture lightgallery"
                                            data-aos="zoom-in"
                                            data-aos-duration="1000"
                                        >
                                            <a href="{{ $storyImage }}">
                                                <img src="{{ $storyImage }}" alt="{{ $storyTitle }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="story-mask"></div>

                                <div class="story-content-wrapper">
                                    <h3
                                        class="story-sub-title"
                                        data-aos="fade-up"
                                        data-aos-duration="1000"
                                    >
                                        {{ $storyTitle }}
                                    </h3>

                                    <div class="story__slider-caption">
                                        <div class="story-details-wrapper">
                                            <div class="story-details">
                                                @if (!empty($storyDate))
                                                    <p
                                                        class="story-caption top"
                                                        data-aos="fade-up"
                                                        data-aos-duration="1000"
                                                    >
                                                        {{ $storyDate }}
                                                    </p>
                                                @endif

                                                <p
                                                    class="story-caption"
                                                    data-aos="fade-up"
                                                    data-aos-duration="1000"
                                                >
                                                    {{ $storyDescription }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="story-item-wrapper">
                        <div
                            class="step-wrapper"
                            data-aos="fade-down"
                            data-aos-duration="1000"
                        >
                            <div class="step-circle"></div>
                            <div class="step-line"></div>
                        </div>

                        <div class="story-item-wrap">
                            <div
                                class="image-wrap st-im"
                                data-aos="zoom-in"
                                data-aos-duration="1000"
                            >
                                <img src="{{ $img('frame-ls.png') }}" alt="">
                            </div>

                            <div
                                class="story__slider-desc-wrap"
                                data-aos="zoom-in"
                                data-aos-duration="1000"
                            >
                                <div class="story__slider-preview">
                                    <div class="story-preview">
                                        <div class="story-picture lightgallery">
                                            <a href="{{ $img('bg-cover.png') }}">
                                                <img src="{{ $img('bg-cover.png') }}" alt="Our Story">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="story-mask"></div>

                                <div class="story-content-wrapper">
                                    <h3 class="story-sub-title">
                                        Our Story
                                    </h3>

                                    <div class="story__slider-caption">
                                        <div class="story-details-wrapper">
                                            <div class="story-details">
                                                <p class="story-caption">
                                                    Cerita perjalanan pasangan belum ditambahkan.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>