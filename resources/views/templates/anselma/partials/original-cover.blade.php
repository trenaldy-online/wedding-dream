@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $coverData = array_replace_recursive([
        'opening_button_text' => 'Open Invitation',
        'label' => 'Wedding Invitation',
        'couple_name' => trim(($couple['groom'] ?? 'Ansel') . ' & ' . ($couple['bride'] ?? 'Varo')),
        'hashtag' => '#AnselVaroInLove',
        'logo' => null,
        'main_image' => null,
    ], $coverData ?? []);

    $defaultLogoUrl = $img('thumb-lg-718286-800-800-1760508495-7556fbd3bf40ca908956a931.webp');

    $coverLogoUrl = $defaultLogoUrl;

    if (!empty($coverData['logo'])) {
        $logo = trim($coverData['logo']);

        if (preg_match('/^https?:\/\//i', $logo)) {
            $coverLogoUrl = $logo;
        } else {
            $coverLogoUrl = asset('storage/' . ltrim($logo, '/'));
        }
    }

    $defaultMainImageUrl = $img('gif-718250-1760507954-5658a47e80aa6692ff63d344.gif');

    $coverMainImageUrl = $defaultMainImageUrl;

    if (!empty($coverData['main_image'])) {
        $mainImage = trim($coverData['main_image']);

        if (preg_match('/^https?:\/\//i', $mainImage)) {
            $coverMainImageUrl = $mainImage;
        } else {
            $coverMainImageUrl = asset('storage/' . ltrim($mainImage, '/'));
        }
    }

    $coverCoupleName = trim($coverData['couple_name'] ?? '');

    if ($coverCoupleName === '') {
        $coverCoupleName = trim(($couple['groom'] ?? 'Ansel') . ' & ' . ($couple['bride'] ?? 'Varo'));
    }

    $coverHashtag = trim($coverData['hashtag'] ?? '');

    if ($coverHashtag === '') {
        $coverHashtag = '#AnselVaroInLove';
    }

    $coverLabel = trim($coverData['label'] ?? '');

    if ($coverLabel === '') {
        $coverLabel = 'Wedding Invitation';
    }
@endphp

<section class="cover" data-section-order="cover">
    <div class="bg-cover">
        <div
            class="image-wrap"
            data-aos="zoom-out"
            data-aos-duration="5000"
            data-aos-delay="600"
        >
            <img src="{{ $img('bg-cover.png') }}" alt="orn-cover">
        </div>
    </div>

    <div class="cover-mask"></div>

    <div class="orn-cover-1 top">
        <div
            class="image-wrap"
            data-aos="fade-down"
            data-aos-duration="3000"
            data-aos-delay="3000"
        >
            <img src="{{ $img('Orn-slip.png') }}" alt="Ornament">
        </div>
    </div>

    <div class="ornaments-wrapper">
        <div class="orn-cover-14 right">
            <div
                class="image-wrap"
                data-aos="fade-right"
                data-aos-duration="3000"
                data-aos-delay="1700"
            >
                <img src="{{ $img('Orn-17.png') }}" alt="Ornament">
            </div>

            <div class="orn-cover-13">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="3000"
                    data-aos-delay="1000"
                >
                    <img src="{{ $img('Orn-16.png') }}" alt="Ornament">
                </div>
            </div>

            <div class="orn-cover-12 right">
                <div
                    class="image-wrap"
                    data-aos="fade-right"
                    data-aos-duration="3000"
                    data-aos-delay="800"
                >
                    <img src="{{ $img('Orn-15.png') }}" alt="Ornament">
                </div>
            </div>
        </div>

        <div class="orn-cover-14 left">
            <div
                class="image-wrap"
                data-aos="fade-right"
                data-aos-duration="3000"
                data-aos-delay="1700"
            >
                <img src="{{ $img('Orn-17.png') }}" alt="Ornament">
            </div>

            <div class="orn-cover-13">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="3000"
                    data-aos-delay="1000"
                >
                    <img src="{{ $img('Orn-16.png') }}" alt="Ornament">
                </div>
            </div>

            <div class="orn-cover-12 right">
                <div
                    class="image-wrap"
                    data-aos="fade-right"
                    data-aos-duration="3000"
                    data-aos-delay="800"
                >
                    <img src="{{ $img('Orn-15.png') }}" alt="Ornament">
                </div>
            </div>
        </div>
    </div>

    <div class="inner">
        <div
            class="head"
            data-aos="zoom-in"
            data-aos-duration="3000"
            data-aos-delay="3000"
        >
            <div
                class="logo-wrap"
                data-aos="fade-down"
                data-aos-duration="3000"
                data-aos-delay="3000"
            >
                <img
                    src="{{ $coverLogoUrl }}"
                    alt="Logo {{ $coverCoupleName }}"
                    class="logo"
                >
            </div>

            <p
                data-aos="fade-down"
                data-aos-duration="3000"
                data-aos-delay="3000"
            >
                {{ $coverLabel }}
            </p>

            <h1
                data-aos="zoom-in"
                data-aos-duration="3000"
                data-aos-delay="3000"
            >
                {{ $coverCoupleName }}
            </h1>

            <p
                data-aos="fade-up"
                data-aos-duration="3000"
                data-aos-delay="3000"
            >
                {{ $coverHashtag }}
            </p>
        </div>

        <div
            class="body highlight"
            data-aos="zoom-in-up"
            data-aos-duration="2900"
            data-aos-delay="700"
        >
            <div class="orn-cover-frame">
                <div class="orn-cover-11 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-up-right"
                        data-aos-duration="3000"
                        data-aos-delay="2700"
                    >
                        <img src="{{ $img('Orn-14.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-10 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="3000"
                        data-aos-delay="1400"
                    >
                        <img src="{{ $img('Orn-13.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-8 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="3000"
                        data-aos-delay="2700"
                    >
                        <img src="{{ $img('Orn-11.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-9 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="3000"
                        data-aos-delay="1800"
                    >
                        <img src="{{ $img('Orn-12.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-6 left">
                    <div class="orn-cover-6-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-left"
                            data-aos-duration="3000"
                            data-aos-delay="1200"
                        >
                            <img src="{{ $img('Orn-08.png') }}" alt="Ornament">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-left"
                        data-aos-duration="3000"
                        data-aos-delay="1200"
                    >
                        <img src="{{ $img('Orn-07.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-7 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-right"
                        data-aos-duration="3000"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-10.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-7 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-left"
                        data-aos-duration="3000"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-09.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-6 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-down-right"
                        data-aos-duration="3000"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-07.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="cover-frame" id="coverFrame">
                    <div class="cover-picture cover-show" id="cover-main">
                        <div class="picture mobile">
                            <img
                                src="{{ $coverMainImageUrl }}"
                                alt="{{ $coverCoupleName }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="image-wrap">
                    <img src="{{ $img('frame-cover.png') }}" alt="Cover Frame">
                </div>

                <div class="orn-cover-5 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-down-left"
                        data-aos-duration="3000"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-06.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-3 center">
                    <div
                        class="image-wrap"
                        data-aos="fade-right"
                        data-aos-duration="3000"
                        data-aos-delay="1400"
                    >
                        <img src="{{ $img('Orn-04.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-4 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-right"
                        data-aos-duration="3000"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-05.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-cover-2 right">
                    <div class="orn-cover-2-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-down-left"
                            data-aos-duration="3000"
                            data-aos-delay="700"
                        >
                            <img src="{{ $img('Orn-03.png') }}" alt="Ornament">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-down-left"
                        data-aos-duration="3000"
                        data-aos-delay="600"
                    >
                        <img src="{{ $img('Orn-02.png') }}" alt="Ornament">
                    </div>
                </div>
            </div>
        </div>

        <div class="ornaments-wrapper">
            <div class="orn-cover-15 burung-1">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="1000"
                >
                    <img src="{{ $img('Orn-burung-1.png') }}" alt="Bird Ornament">
                </div>
            </div>

            <div class="orn-cover-16 burung-2">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="1000"
                >
                    <img src="{{ $img('Orn-burung-2.png') }}" alt="Bird Ornament">
                </div>
            </div>
        </div>
    </div>
</section>