@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $type = $type ?? 'groom';

    $person = array_replace_recursive([
        'name' => '',
        'parents' => '',
        'instagram' => null,
        'instagram_url' => null,
        'photo' => null,
    ], $person ?? []);

    $defaultGroomPhoto = 'thumb-lg-718214-1200-1200-1761643646-157cb5cea53a4db6e8d9c77e.webp';
    $defaultBridePhoto = 'thumb-lg-718213-1200-1200-1761643619-f6200e0577ad7d5431553861.webp';

    $defaultPhoto = $type === 'bride'
        ? $defaultBridePhoto
        : $defaultGroomPhoto;

    $photo = trim($person['photo'] ?? '');

    if ($photo !== '' && preg_match('/^https?:\/\//i', $photo)) {
        $photoUrl = $photo;
    } elseif ($photo !== '' && strpos($photo, 'couple/') === 0) {
        $photoUrl = asset('storage/' . ltrim($photo, '/'));
    } elseif ($photo !== '') {
        $photoUrl = $img($photo);
    } else {
        $photoUrl = $img($defaultPhoto);
    }

    $personName = trim($person['name'] ?? '');

    if ($personName === '') {
        $personName = $type === 'bride' ? 'Ansel Ginny' : 'Varo Brian';
    }

    $parentsText = trim($person['parents'] ?? '');

    if ($parentsText === '') {
        $parentsText = $type === 'bride'
            ? 'The Daughter of <br> Mr. Darwin Davidson <br> & Mrs. Jenny Smith'
            : 'The Son of <br> Mr. Lerry Brian <br> & Mrs. Lenny Diah';
    }

    $instagramLabel = trim($person['instagram'] ?? '');
    $instagramUrl = trim($person['instagram_url'] ?? '');

    if ($instagramLabel !== '' && $instagramUrl === '') {
        $username = ltrim($instagramLabel, '@');
        $instagramUrl = 'https://www.instagram.com/' . $username;
    }
@endphp

<div class="couple-info {{ $type }}">
    <div class="couple-details">
        <div class="cp-top">
            <div
                class="image-wrap"
                data-aos="fade-up"
                data-aos-duration="1000"
                data-aos-delay="600"
            >
                <img src="{{ $img('Orn-cp.png') }}" alt="orn-cover">
            </div>
        </div>

        <h2
            class="couple-name"
            data-aos="fade-up"
            data-aos-duration="1000"
        >
            {{ $personName }}
        </h2>
    </div>

    <div class="couple-preview">
        <div class="couple-frame">
            <div class="orn-couple-3">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="1300"
                    data-aos-delay="1000"
                >
                    <img src="{{ $img('Orn-23.png') }}" alt="ornaments">
                </div>
            </div>

            <div class="orn-couple-4 right">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="1300"
                    data-aos-delay="1100"
                >
                    <img src="{{ $img('Orn-24.png') }}" alt="ornaments">
                </div>
            </div>

            <div class="orn-couple-4 left">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="1300"
                    data-aos-delay="1100"
                >
                    <img src="{{ $img('Orn-24.png') }}" alt="ornaments">
                </div>
            </div>

            <div class="orn-couple-5">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="900"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-22.png') }}" alt="ornaments">
                </div>
            </div>

            <div
                class="image-wrap"
                data-aos="zoom-in"
                data-aos-duration="1000"
            >
                <img
                    src="{{ $img('frame-couple.png') }}"
                    class="img-couple-frame"
                    alt="Frame"
                >
            </div>

            <div class="couple-picture-wrap">
                <div
                    class="couple-picture lightgallery"
                    data-aos="zoom-out"
                    data-aos-duration="1000"
                    data-aos-once="false"
                >
                    <a
                        class="img-wrap"
                        href="{{ $photoUrl }}"
                        target="_blank"
                    >
                        <img
                            class="img"
                            src="{{ $photoUrl }}"
                            alt="{{ $personName }}"
                        >
                    </a>
                </div>
            </div>

            <div class="orn-couple-1">
                <div class="orn-couple-1-1">
                    <div
                        class="image-wrap"
                        data-aos="zoom-in"
                        data-aos-duration="1100"
                        data-aos-delay="650"
                    >
                        <img src="{{ $img('Orn-19.png') }}" alt="ornaments">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1000"
                    data-aos-delay="600"
                >
                    <img src="{{ $img('Orn-18.png') }}" alt="ornaments">
                </div>
            </div>

            <div class="orn-couple-2">
                <div class="orn-couple-2-1">
                    <div
                        class="image-wrap"
                        data-aos="zoom-in"
                        data-aos-duration="1200"
                        data-aos-delay="750"
                    >
                        <img src="{{ $img('Orn-21.png') }}" alt="ornaments">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1400"
                    data-aos-delay="900"
                >
                    <img src="{{ $img('Orn-20.png') }}" alt="ornaments">
                </div>
            </div>
        </div>

        <div class="orn-couple-edge right">
            <div
                class="image-wrap"
                data-aos="fade-up-right"
                data-aos-duration="1600"
                data-aos-delay="1500"
            >
                <img src="{{ $img('Orn-25.png') }}" alt="ornaments">
            </div>
        </div>

        <div class="orn-couple-edge left">
            <div
                class="image-wrap"
                data-aos="fade-up-right"
                data-aos-duration="1600"
                data-aos-delay="1500"
            >
                <img src="{{ $img('Orn-25.png') }}" alt="ornaments">
            </div>
        </div>
    </div>

    <div class="couple-details">
        <p
            class="couple-parents"
            data-aos="fade-up"
            data-aos-duration="1000"
        >
            {!! $parentsText !!}
        </p>

        @if ($instagramLabel !== '')
            <div
                class="couple-link-wrap"
                data-aos="fade-up"
                data-aos-duration="1000"
            >
                <a
                    href="{{ $instagramUrl ?: '#' }}"
                    target="_blank"
                    class="couple-link"
                >
                    <i class="fab fa-instagram"></i> {{ $instagramLabel }}
                </a>
            </div>
        @endif
    </div>
</div>