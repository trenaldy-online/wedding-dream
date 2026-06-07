@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $coupleData = array_replace_recursive([
        'order' => 'groom_first',
        'title' => 'Two souls intertwined, a love that will bind',
        'description' => 'They say that some souls are simply meant to find each other. Ours did, and with each shared moment, our connection has grown into a love that will forever bind us. We are so excited to celebrate this beautiful journey with you as we exchange our vows.',
        'groom' => [
            'name' => 'Varo Brian',
            'parents' => 'The Son of <br> Mr. Lerry Brian <br> & Mrs. Lenny Diah',
            'instagram' => '@katsudoto',
            'instagram_url' => 'https://www.instagram.com/katsudoto',
            'photo' => 'thumb-lg-718214-1200-1200-1761643646-157cb5cea53a4db6e8d9c77e.webp',
        ],
        'bride' => [
            'name' => 'Ansel Ginny',
            'parents' => 'The Daughter of <br> Mr. Darwin Davidson <br> & Mrs. Jenny Smith',
            'instagram' => null,
            'instagram_url' => null,
            'photo' => 'thumb-lg-718213-1200-1200-1761643619-f6200e0577ad7d5431553861.webp',
        ],
    ], $coupleData ?? []);

    $groom = $coupleData['groom'] ?? [];
    $bride = $coupleData['bride'] ?? [];

    $coupleTitle = trim($coupleData['title'] ?? '');

    if ($coupleTitle === '') {
        $coupleTitle = 'Two souls intertwined, a love that will bind';
    }

    $coupleDescription = trim($coupleData['description'] ?? '');

    if ($coupleDescription === '') {
        $coupleDescription = 'They say that some souls are simply meant to find each other. Ours did, and with each shared moment, our connection has grown into a love that will forever bind us. We are so excited to celebrate this beautiful journey with you as we exchange our vows.';
    }

    $coupleOrder = $coupleData['order'] ?? 'groom_first';

    if ($coupleOrder === 'bride_first') {
        $firstType = 'bride';
        $firstPerson = $bride;

        $secondType = 'groom';
        $secondPerson = $groom;
    } else {
        $firstType = 'groom';
        $firstPerson = $groom;

        $secondType = 'bride';
        $secondPerson = $bride;
    }
@endphp

<section class="couple-wrap" data-section-order="couple">
    <div class="couple">
        <div class="couple-head">
            <div class="orn-cp-head right">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="1400"
                    data-aos-delay="1200"
                >
                    <img src="{{ $img('Orn-clip-2.png') }}" alt="Ornament">
                </div>
            </div>

            <div class="orn-cp-head left">
                <div
                    class="image-wrap"
                    data-aos="fade-up"
                    data-aos-duration="1400"
                    data-aos-delay="1200"
                >
                    <img src="{{ $img('Orn-clip-2.png') }}" alt="Ornament">
                </div>
            </div>

            <h1
                class="couple-title"
                data-aos="zoom-in"
                data-aos-duration="1000"
            >
                {{ $coupleTitle }}
            </h1>

            <p
                class="couple-description"
                data-aos="fade-up"
                data-aos-duration="1000"
            >
                {!! nl2br(e($coupleDescription)) !!}
            </p>
        </div>

        <div class="couple-body show-picture">
            @include('templates.anselma.partials.original-couple-person', [
                'type' => $firstType,
                'person' => $firstPerson,
                'img' => $img,
            ])

            <div class="separator-wrap">
                <div
                    class="separator"
                    data-aos="zoom-in"
                    data-aos-duration="1500"
                >
                    <h2 class="couple-separator">&amp;</h2>
                </div>
            </div>

            @include('templates.anselma.partials.original-couple-person', [
                'type' => $secondType,
                'person' => $secondPerson,
                'img' => $img,
            ])
        </div>
    </div>
</section>