@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $footerData = array_replace_recursive([
        'title' => 'Thank You',
        'description' => 'With love and gratitude, thank you for celebrating with us.',
    ], $footerData ?? []);

    $footerTitle = trim($footerData['title'] ?? '');

    if ($footerTitle === '') {
        $footerTitle = 'Thank You';
    }

    $footerDescription = trim($footerData['description'] ?? '');

    if ($footerDescription === '') {
        $footerDescription = 'With love and gratitude, thank you for celebrating with us.';
    }
@endphp

<section class="quote-message-wrap" data-section-order="greet_thanks">
    <div class="quote-message">
        <div class="quote-message-inner-wrap">
            <div class="quote-message-inner">
                <div class="orn-agenda-top">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                        data-aos-delay="600"
                    >
                        <img src="{{ $img('Orn-01.png') }}" alt="orn-cover">
                    </div>
                </div>

                <h1
                    class="quote-message-title zin-3"
                    data-aos="fade-up"
                    data-aos-duration="1000"
                    data-aos-delay="600"
                >
                    {{ $footerTitle }}
                </h1>

                <p
                    class="quote-message-desc zin-3"
                    data-aos="fade-up"
                    data-aos-duration="1000"
                    data-aos-delay="600"
                >
                    {!! nl2br(e($footerDescription)) !!}
                </p>
            </div>

            <div class="ornaments-wrapper quote-message-ornaments">
                <div class="orn-qm-4 right">
                    <div class="orn-qm-4-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1500"
                            data-aos-delay="1100"
                        >
                            <img src="{{ $img('Orn-14.png') }}" alt="">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-24.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-4 left">
                    <div class="orn-qm-4-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1500"
                            data-aos-delay="1100"
                        >
                            <img src="{{ $img('Orn-14.png') }}" alt="">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1000"
                    >
                        <img src="{{ $img('Orn-24.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-3 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-55.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-3 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-55.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-2 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="700"
                    >
                        <img src="{{ $img('Orn-10.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-2 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="700"
                    >
                        <img src="{{ $img('Orn-09.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-1 right">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                        data-aos-delay="500"
                    >
                        <img src="{{ $img('Orn-54.png') }}" alt="">
                    </div>
                </div>

                <div class="orn-qm-1 left">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                        data-aos-delay="500"
                    >
                        <img src="{{ $img('Orn-54.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>