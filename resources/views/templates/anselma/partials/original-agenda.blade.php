@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $agenda = $agenda ?? [
        'title' => "It's Wedding Day",
        'day' => 'Saturday,',
        'date' => '31 January 2026',
        'same_location' => true,
        'maps_button_text' => 'View Maps',
        'activities' => [
            [
                'title' => 'Akad Nikah',
                'time' => '09:00 - 10:00',
                'hall' => 'Mason Pine Hotel',
                'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                'city' => 'Kabupaten Bandung Barat',
                'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
            ],
            [
                'title' => 'Resepsi',
                'time' => '11:00 - 14:00',
                'hall' => 'Mason Pine Hotel',
                'address' => 'Jl. Parahyangan Raya No.KM 1, RW.8, Cipeundeuy, Kec. Padalarang, Jawa Barat',
                'city' => 'Kabupaten Bandung Barat',
                'maps_url' => 'https://maps.google.com/?cid=16992323544258832489',
            ],
        ],
    ];

    $dressCode = $dressCode ?? [
        'title' => 'Dresscode',
        'description' => 'Wear a long gown or formal gown, black tie or bow tie',
        'note' => 'We kindly ask that guests please attend wearing our wedding colors.',
        'men_label' => 'Men',
        'women_label' => 'Women',
        'men_style_label' => 'Formal',
        'women_style_label' => 'Formal',
        'colors' => ['#7F0404', '#1C3106', '#D2CEAE', '#E6E3E4'],
    ];
@endphp

<section class="agenda-wrap" data-section-order="event">
    <div class="agenda-inner">
        <div class="agenda-head">
            <h2
                class="agenda-title"
                data-aos="zoom-in"
                data-aos-duration="1500"
            >
                {{ $agenda['title'] }}
            </h2>
        </div>

        <div class="agenda-body">
            <div class="event-item ev-0">
                <div class="event-head">
                    <div class="orn-act-top">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1000"
                            data-aos-delay="600"
                        >
                            <img src="{{ $img('Orn-67.png') }}" alt="orn-cover">
                        </div>
                    </div>

                    <h3
                        class="event-day"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                    >
                        {{ $agenda['day'] }}
                    </h3>

                    <h2
                        class="event-day"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                    >
                        {{ $agenda['date'] }}
                    </h2>
                </div>

                <div class="activity-wrap {{ $agenda['same_location'] ? 'same-location' : '' }}">
                    @foreach ($agenda['activities'] as $activity)
                        <div class="activity-item">
                            <div class="activity-frame">
                                <div class="ornaments-wrapper">
                                    <div class="orn-event-8 center">
                                        <div
                                            class="image-wrap"
                                            data-aos="zoom-out"
                                            data-aos-duration="1600"
                                            data-aos-delay="1200"
                                        >
                                            <img src="{{ $img('Orn-48.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div class="orn-event-7 left">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1600"
                                            data-aos-delay="1200"
                                        >
                                            <img src="{{ $img('Orn-47.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div class="orn-event-7 right">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1600"
                                            data-aos-delay="1200"
                                        >
                                            <img src="{{ $img('Orn-47.png') }}" alt="Orn">
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="frame-wrap"
                                    data-aos="zoom-in"
                                    data-aos-duration="1000"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('frame-event.png') }}" alt="" width="100">
                                </div>
                            </div>

                            <div class="activity-content">
                                <div class="activity-head">
                                    <div
                                        class="activity-title-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1000"
                                        data-aos-delay="450"
                                        data-aos-anchor-placement="top-bottom"
                                    >
                                        <h3 class="activity-title">
                                            {{ $activity['title'] }}
                                        </h3>
                                    </div>
                                </div>

                                <p
                                    class="activity-time"
                                    data-aos="zoom-in"
                                    data-aos-duration="1000"
                                    data-aos-delay="450"
                                    data-aos-anchor-placement="top-bottom"
                                >
                                    {{ $activity['time'] }}
                                </p>

                                <div class="activity-details">
                                    <p
                                        class="activity-hall"
                                        data-aos="fade-up"
                                        data-aos-duration="1000"
                                    >
                                        {{ $activity['hall'] }}
                                    </p>

                                    <p
                                        class="activity-address"
                                        data-aos="fade-up"
                                        data-aos-duration="1000"
                                    >
                                        {{ $activity['address'] }}
                                    </p>

                                    <p
                                        class="activity-city"
                                        data-aos="fade-up"
                                        data-aos-duration="1000"
                                    >
                                        {{ $activity['city'] }}
                                    </p>

                                    <div
                                        class="activity-link-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1000"
                                    >
                                        <a
                                            href="{{ $activity['maps_url'] }}"
                                            class="event-link"
                                            target="_blank"
                                        >
                                            {{ $activity['button_text'] ?? $agenda['maps_button_text'] ?? 'View Maps' }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="ornaments-wrapper">
                                <div class="orn-event-6 center">
                                    <div
                                        class="image-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1400"
                                        data-aos-delay="1000"
                                    >
                                        <img src="{{ $img('Orn-46.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-5">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1600"
                                        data-aos-delay="1200"
                                    >
                                        <img src="{{ $img('Orn-17.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-4">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1600"
                                        data-aos-delay="1200"
                                    >
                                        <img src="{{ $img('Orn-45.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-3">
                                    <div class="orn-event-3-1">
                                        <div class="orn-event-3-1-1">
                                            <div
                                                class="image-wrap"
                                                data-aos="fade-up"
                                                data-aos-duration="1600"
                                                data-aos-delay="1100"
                                            >
                                                <img src="{{ $img('Orn-36.png') }}" alt="Orn">
                                            </div>

                                            <div class="orn-event-3-1-1-1">
                                                <div class="orn-event-3-1-1-1-1">
                                                    <div
                                                        class="image-wrap"
                                                        data-aos="fade-up"
                                                        data-aos-duration="1500"
                                                        data-aos-delay="1000"
                                                    >
                                                        <img src="{{ $img('Orn-34.png') }}" alt="Orn">
                                                    </div>
                                                </div>

                                                <div
                                                    class="image-wrap"
                                                    data-aos="fade-up"
                                                    data-aos-duration="1300"
                                                    data-aos-delay="900"
                                                >
                                                    <img src="{{ $img('Orn-40.png') }}" alt="Orn">
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1200"
                                            data-aos-delay="800"
                                        >
                                            <img src="{{ $img('Orn-10.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div
                                        class="image-wrap"
                                        data-aos="fade-right"
                                        data-aos-duration="1000"
                                        data-aos-delay="600"
                                    >
                                        <img src="{{ $img('Orn-44.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-2">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1600"
                                        data-aos-delay="1000"
                                    >
                                        <img src="{{ $img('Orn-43.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-1">
                                    <div class="orn-event-1-1">
                                        <div class="orn-event-1-1-1">
                                            <div class="orn-event-1-1-1-1">
                                                <div
                                                    class="image-wrap"
                                                    data-aos="fade-up"
                                                    data-aos-duration="1600"
                                                    data-aos-delay="1000"
                                                >
                                                    <img src="{{ $img('Orn-08.png') }}" alt="Orn">
                                                </div>
                                            </div>

                                            <div
                                                class="image-wrap"
                                                data-aos="fade-up"
                                                data-aos-duration="1400"
                                                data-aos-delay="900"
                                            >
                                                <img src="{{ $img('Orn-42.png') }}" alt="Orn">
                                            </div>
                                        </div>

                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1200"
                                            data-aos-delay="700"
                                        >
                                            <img src="{{ $img('Orn-09.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div
                                        class="image-wrap"
                                        data-aos="fade-left"
                                        data-aos-duration="1000"
                                        data-aos-delay="600"
                                    >
                                        <img src="{{ $img('Orn-41.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-9 burung-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1600"
                                        data-aos-delay="1000"
                                    >
                                        <img src="{{ $img('Orn-burung-1.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div class="orn-event-10 burung-2">
                                    <div
                                        class="image-wrap"
                                        data-aos="zoom-in"
                                        data-aos-duration="1600"
                                        data-aos-delay="1000"
                                    >
                                        <img src="{{ $img('Orn-burung-2.png') }}" alt="Orn">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-relative">
                    <div class="ornaments-wrapper">
                        <div class="orn-dc-1 left">
                            <div class="orn-dc-1-1">
                                <div class="orn-dc-1-1-1">
                                    <div class="orn-dc-1-1-1-2">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1400"
                                            data-aos-delay="1200"
                                        >
                                            <img src="{{ $img('Orn-35.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div class="orn-dc-1-1-1-1">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1200"
                                            data-aos-delay="1000"
                                        >
                                            <img src="{{ $img('Orn-34.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1100"
                                        data-aos-delay="800"
                                    >
                                        <img src="{{ $img('Orn-65.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="900"
                                    data-aos-delay="600"
                                >
                                    <img src="{{ $img('Orn-11.png') }}" alt="Orn">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="600"
                                data-aos-delay="400"
                            >
                                <img src="{{ $img('Orn-64.png') }}" alt="Orn">
                            </div>
                        </div>

                        <div class="orn-dc-1 right">
                            <div class="orn-dc-1-1">
                                <div class="orn-dc-1-1-1">
                                    <div class="orn-dc-1-1-1-2">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1400"
                                            data-aos-delay="1200"
                                        >
                                            <img src="{{ $img('Orn-35.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div class="orn-dc-1-1-1-1">
                                        <div
                                            class="image-wrap"
                                            data-aos="fade-up"
                                            data-aos-duration="1200"
                                            data-aos-delay="1000"
                                        >
                                            <img src="{{ $img('Orn-34.png') }}" alt="Orn">
                                        </div>
                                    </div>

                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1100"
                                        data-aos-delay="800"
                                    >
                                        <img src="{{ $img('Orn-65.png') }}" alt="Orn">
                                    </div>
                                </div>

                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="900"
                                    data-aos-delay="600"
                                >
                                    <img src="{{ $img('Orn-11.png') }}" alt="Orn">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="600"
                                data-aos-delay="400"
                            >
                                <img src="{{ $img('Orn-64.png') }}" alt="Orn">
                            </div>
                        </div>
                    </div>

                    <div class="dress-wrapper {{ $agenda['same_location'] ? 'same-location' : '' }}">
                        <div class="dress-inner">
                            <div
                                class="dress-header"
                                data-aos="fade-up"
                                data-aos-duration="1000"
                            >
                                <h2 class="dress-title">{{ $dressCode['title'] }}</h2>
                                <p class="dress-desc">{{ $dressCode['description'] }}</p>
                            </div>

                            <div class="dress-body">
                                <div
                                    class="dress-list"
                                    data-aos="fade-up"
                                    data-aos-duration="1000"
                                >
                                    <div class="dress-item">
                                        <p class="dress-item-title">{{ $dressCode['men_label'] ?? 'Men' }}</p>

                                        <div class="dress-preview man-preview">
                                            <div class="dress-icon">
                                                <img
                                                    class="dress-icon-img"
                                                    src="{{ $img('ic-dress-man-formal.png') }}"
                                                    alt="Dress Man Formal"
                                                    width="36"
                                                    height="36"
                                                >
                                                <p class="dress-icon-label">{{ $dressCode['men_style_label'] ?? 'Formal' }}</p>
                                            </div>
                                        </div>

                                        <div class="dress-color-list">
                                            @foreach ($dressCode['colors'] as $color)
                                                <div
                                                    class="dress-color-item"
                                                    style="--bg-color: {{ $color }};"
                                                ></div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="dress-item">
                                        <p class="dress-item-title">{{ $dressCode['women_label'] ?? 'Women' }}</p>

                                        <div class="dress-preview woman-preview">
                                            <div class="dress-icon">
                                                <img
                                                    class="dress-icon-img"
                                                    src="{{ $img('ic-dress-woman-formal.png') }}"
                                                    alt="Dress Woman Formal"
                                                    width="36"
                                                    height="36"
                                                >
                                                <p class="dress-icon-label">{{ $dressCode['women_style_label'] ?? 'Formal' }}</p>
                                            </div>
                                        </div>

                                        <div class="dress-color-list">
                                            @foreach ($dressCode['colors'] as $color)
                                                <div
                                                    class="dress-color-item"
                                                    style="--bg-color: {{ $color }};"
                                                    title="{{ $color }}"
                                                ></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dress-footer">
                                <p class="dress-note">{{ $dressCode['note'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>