@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $quoteData = array_replace_recursive([
        'text' => '“Love is not about finding someone to live with, but finding someone you can’t imagine life without.”',
        'source' => '',
    ], $quoteData ?? []);

    $quoteText = trim($quoteData['text'] ?? '');

    if ($quoteText === '') {
        $quoteText = '“Love is not about finding someone to live with, but finding someone you can’t imagine life without.”';
    }
@endphp

<div class="quote-sec-wrap" data-section-order="quote">

    <div class="orn-clip-mask bot">
        <div
            class="image-wrap"
            data-aos="fade-up"
            data-aos-duration="1200"
            data-aos-delay="500"
        >
            <img src="{{ $img('Orn-clip.png') }}" alt="Ornament">
        </div>
    </div>

    <div class="quote-sec-inner">

        <div class="ornaments-wrapper">
            <div class="orn-qt-bg">
                <div
                    class="image-wrap"
                    data-aos="zoom-out"
                    data-aos-duration="1500"
                    data-aos-delay="800"
                >
                    <img src="{{ $img('bg-sd.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="frame-qt">
            <div
                class="image-wrap"
                data-aos="zoom-out"
                data-aos-duration="1200"
                data-aos-delay="500"
            >
                <img src="{{ $img('frame-qt.png') }}" alt="Background Awan">
            </div>
        </div>

        <div class="quote-sec">

            <p
                class="quote-sec-caption"
                data-aos="fade-up"
                data-aos-duration="1200"
                data-aos-delay="500"
            >
                {!! nl2br(e($quoteText)) !!}
            </p>

        </div>

        <div class="ornaments-wrapper">
            <div class="orn-qt-2 right">
                <div class="orn-qt-2-2">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1500"
                        data-aos-delay="800"
                    >
                        <img src="{{ $img('Orn-11.png') }}" alt="">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="fade-left"
                    data-aos-duration="1400"
                    data-aos-delay="700"
                >
                    <img src="{{ $img('Orn-17.png') }}" alt="">
                </div>

                <div class="orn-qt-2-1">
                    <div class="orn-qt-2-1-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-left"
                            data-aos-duration="1500"
                            data-aos-delay="800"
                        >
                            <img src="{{ $img('Orn-13.png') }}" alt="">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-left"
                        data-aos-duration="1300"
                        data-aos-delay="800"
                    >
                        <img src="{{ $img('Orn-46.png') }}" alt="">
                    </div>
                </div>
            </div>

            <div class="orn-qt-2 left">
                <div class="orn-qt-2-2">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1500"
                        data-aos-delay="800"
                    >
                        <img src="{{ $img('Orn-11.png') }}" alt="">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="fade-left"
                    data-aos-duration="1400"
                    data-aos-delay="700"
                >
                    <img src="{{ $img('Orn-17.png') }}" alt="">
                </div>

                <div class="orn-qt-2-1">
                    <div class="orn-qt-2-1-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-left"
                            data-aos-duration="1500"
                            data-aos-delay="800"
                        >
                            <img src="{{ $img('Orn-13.png') }}" alt="">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-left"
                        data-aos-duration="1300"
                        data-aos-delay="800"
                    >
                        <img src="{{ $img('Orn-46.png') }}" alt="">
                    </div>
                </div>
            </div>

            <div class="orn-qt-1 center">
                <div class="orn-qt-1-1 right">
                    <div
                        class="image-wrap"
                        data-aos-anchor="#qt1-tr"
                        data-aos="fade-up-right"
                        data-aos-duration="1300"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-47.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qt-1-1 left">
                    <div
                        class="image-wrap"
                        data-aos-anchor="#qt1-tr"
                        data-aos="fade-up-right"
                        data-aos-duration="1300"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-47.png') }}" alt="">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    id="qt1-tr"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-53.png') }}" alt="">
                </div>
            </div>
        </div>

    </div>
</div>