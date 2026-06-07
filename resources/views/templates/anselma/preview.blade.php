@php
    $img = fn ($file) => asset('templates/anselma/files/' . $file);

    $guestName = ($guest ?? null)?->name
        ?? ($guestName ?? null)
        ?? 'Katsudoto';

    $couple = $couple ?? [
        'groom' => 'Ansel',
        'bride' => 'Varo',
        'hashtag' => '#AnselVaroInLove',
        'guest' => $guestName,
        'date' => 'Saturday, January 31st 2026',
        'venue' => 'The Langham Jakarta',
        'address' => 'District 8, SCBD, Jakarta Selatan',
    ];

    $events = $events ?? [];

    $sections = collect($sections ?? []);

    $sectionsByKey = $sections->keyBy('section_key');

    $sectionIsActive = function (string $key) use ($sectionsByKey): bool {
        if (! $sectionsByKey->has($key)) {
            return true;
        }

        return (bool) $sectionsByKey->get($key)->is_active;
    };

    $sectionContent = function (string $key, array $default = []) use ($sectionsByKey): array {
        if (! $sectionsByKey->has($key)) {
            return $default;
        }

        $content = $sectionsByKey->get($key)->content ?? [];

        if (is_string($content)) {
            $decoded = json_decode($content, true);
            $content = is_array($decoded) ? $decoded : [];
        }

        return array_replace_recursive($default, is_array($content) ? $content : []);
    };

    $coverData = $sectionContent('cover', [
        'opening_button_text' => 'Open Invitation',
        'label' => 'Wedding Invitation',
        'couple_name' => trim(($couple['groom'] ?? 'Ansel') . ' & ' . ($couple['bride'] ?? 'Varo')),
        'hashtag' => '#AnselVaroInLove',

        'loader_enabled' => true,
        'loader_mark_type' => 'initial',
        'loader_mark' => '',
        'loading_text' => 'One moment...',

        'opening_subtitle' => 'The Wedding Of',
        'greeting_prefix' => 'Hai',
        'opening_video' => 'vid-comp.mp4',
        'opening_poster' => 'bg-cover.png',

        'logo' => null,
        'main_image' => null,
    ]);

    $anselmaFileAsset = function ($file, string $default = '') use ($img) {
        $file = trim($file ?? '');

        if ($file === '') {
            $file = $default;
        }

        if ($file === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//', $file)) {
            return $file;
        }

        if (str_starts_with($file, 'cover/')) {
            return asset('storage/' . $file);
        }

        return asset('templates/anselma/files/' . $file);
    };

    $coverLoaderMarkType = $coverData['loader_mark_type'] ?? 'initial';

    if (! in_array($coverLoaderMarkType, ['initial', 'logo'], true)) {
        $coverLoaderMarkType = 'initial';
    }

    $coverLogoUrl = null;

    if (! empty($coverData['logo'])) {
        $coverLogoUrl = $anselmaFileAsset($coverData['logo']);
    }

    $coverLoaderEnabled = (bool) ($coverData['loader_enabled'] ?? true);

    $coverLoaderMark = trim($coverData['loader_mark'] ?? '');

    if ($coverLoaderMark === '') {
        $coverLoaderMark = substr($couple['groom'] ?? 'A', 0, 1) . '&' . substr($couple['bride'] ?? 'V', 0, 1);
    }

    $coverLoadingText = $coverData['loading_text'] ?? 'One moment...';
    $coverOpeningSubtitle = $coverData['opening_subtitle'] ?? 'The Wedding Of';
    $coverOpeningGreetingPrefix = $coverData['greeting_prefix'] ?? 'Hai';
    $coverOpeningButtonText = $coverData['opening_button_text'] ?? 'Open Invitation';

    $coverOpeningVideoUrl = $anselmaFileAsset($coverData['opening_video'] ?? '', 'vid-comp.mp4');
    $coverOpeningPosterUrl = $anselmaFileAsset($coverData['opening_poster'] ?? '', 'bg-cover.png');

    $coverCoupleName = trim($coverData['couple_name'] ?? (($couple['groom'] ?? 'Ansel') . ' & ' . ($couple['bride'] ?? 'Varo')));
    $coverNameParts = array_map('trim', explode('&', $coverCoupleName, 2));
    $coverFirstName = $coverNameParts[0] ?? ($couple['groom'] ?? 'Ansel');
    $coverSecondName = $coverNameParts[1] ?? ($couple['bride'] ?? 'Varo');
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $couple['groom'] }} & {{ $couple['bride'] }} | Anselma Preview</title>

    <meta name="description" content="Preview template undangan Anselma versi Laravel.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@400;500;600;700&family=Ovo&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Lancelot&family=Pinyon+Script&display=swap"
        rel="stylesheet"
    >

    {{-- Vendor CSS --}}
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/selectize.default.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/flexbin.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/modal-video.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/video-js.css') }}">

    {{-- Katsudoto base CSS --}}
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style(1).css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style(2).css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style(3).css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style(4).css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/style(5).css') }}">

    {{-- Katsudoto global/module CSS --}}
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/universal.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/kado-template.1759120915.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/rundown-template.1762934182.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/note-template.1756182997.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/bank-template.1759120915.css') }}">

    {{-- Theme utama Anselma --}}
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/exclusive-anselma.1777003773.css') }}">

    {{-- Bridge lokal --}}
    <link rel="stylesheet" href="{{ asset('templates/anselma/css/anselma-clean-bridge.css') }}">

    <style>
        .loader-mark.is-logo {
            overflow: hidden;
            padding: 8px;
        }

        .loader-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
    </style>
</head>

<body class="anselma anselma-template original preset-original is-gate-locked" data-template="anselma">

        {{-- Loading Screen --}}
        <div
            class="anselma-loader"
            id="anselmaLoader"
            @unless($coverLoaderEnabled)
                style="display: none;"
            @endunless
        >
            <div class="anselma-loader-inner">
                <div class="loader-mark {{ $coverLoaderMarkType === 'logo' && $coverLogoUrl ? 'is-logo' : '' }}">
                    @if ($coverLoaderMarkType === 'logo' && $coverLogoUrl)
                        <img
                            src="{{ $coverLogoUrl }}"
                            alt="Loader Logo"
                            class="loader-logo"
                        >
                    @else
                        {{ $coverLoaderMark }}
                    @endif
                </div>

                <div class="loader-text">
                    {{ $coverLoadingText }}
                </div>
            </div>
        </div>

        {{-- Opening Invitation --}}
        <div class="opening-gate opening-video-gate" id="openingGate">
            <video
                class="opening-video"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
                poster="{{ $coverOpeningPosterUrl }}"
            >
                <source
                    src="{{ $coverOpeningVideoUrl }}"
                    type="video/mp4"
                >
            </video>

        <div class="opening-video-mask"></div>

        <div class="opening-video-content">
            <div class="opening-video-main">
                <p class="opening-video-subtitle">
                    {{ $coverOpeningSubtitle }}
                </p>

                <h1 class="opening-video-title">
                    <span>{{ $coverFirstName }}</span>
                    <small>&amp;</small>
                    <span>{{ $coverSecondName }}</span>
                </h1>
            </div>

            <div class="opening-video-bottom">
                <p class="opening-video-guest">
                    {{ $coverOpeningGreetingPrefix }}&nbsp;{{ $couple['guest'] }}
                </p>

                <button type="button" class="opening-video-button" id="openInvitation">
                    {{ $coverOpeningButtonText }}
                </button>
            </div>
        </div>
    </div>

    {{-- Side to Side --}}
    <section class="kat-page__side-to-side">

        {{-- Primary Pane --}}
        <section class="primary-pane">
            <video
                class="bg-orn-video"
                id="tcVideo"
                autoplay
                muted
                playsinline
                loop
                preload="auto"
                poster="{{ $coverOpeningPosterUrl }}"
            >
                <source
                    id="tcVideoSource"
                    src="{{ $coverOpeningVideoUrl }}"
                    type="video/mp4"
                    media="(min-width: 961px)"
                >
            </video>

            <div class="inner">
                <div class="primary-pane-content">
                    <div class="primary-pane-details">
                        <div class="primary-pane-details--content">
                            <h1
                                class="primary-pane-title"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="700"
                            >
                                {{ $couple['groom'] }} &amp; {{ $couple['bride'] }}
                            </h1>

                            <div
                                class="link-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="1200"
                            >
                                <p class="greeting-text">
                                    Hai&nbsp;{{ $couple['guest'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Secondary Pane --}}
        <section class="secondary-pane">
            @php
                $orderedSections = collect($sections ?? [])
                    ->values()
                    ->sortBy(fn ($section) => (int) ($section->sort_order ?? 999))
                    ->values();
            @endphp

            @foreach ($orderedSections as $section)
                @continue(! $section->is_active)

                @switch($section->section_key)

                    @case('cover')
                        {{-- COVER --}}
                        @include('templates.anselma.partials.original-cover', [
                            'coverData' => $coverData,
                            'guestName' => $guestName,
                        ])
                        @break

                    @case('couple')
                        {{-- COUPLE --}}
                        @include('templates.anselma.partials.original-couple', [
                            'coupleData' => $sectionContent('couple', [
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
                            ]),
                        ])
                        @break

                    @case('quote')
                        {{-- QUOTES --}}
                        @include('templates.anselma.partials.original-quote', [
                            'quoteData' => $sectionContent('quote', [
                                'text' => '“Love is not about finding someone to live with, but finding someone you can’t imagine life without.”',
                                'source' => '',
                            ]),
                        ])
                        @break

                    @case('story')
                        {{-- STORY --}}
                        @php
                            $storyContent = $sectionContent('story', [
                                'title' => 'Our Story',
                                'items' => [],
                            ]);
                        @endphp

                        @include('templates.anselma.partials.original-story', [
                            'storyData' => $storyContent,
                        ])

                        <script>
                            window.__ANSELMA_STORY_DATA = @json($storyContent);
                        </script>
                        @break

                    @case('gallery')
                        {{-- GALLERY PHOTO --}}
                        @include('templates.anselma.partials.original-gallery', [
                            'galleryData' => $sectionContent('gallery', [
                                'title' => 'Portraits of Us',
                                'items' => [],
                            ]),
                        ])
                        @break

                    @case('video')
                        {{-- VIDEO --}}
                        @include('templates.anselma.partials.original-video', [
                            'videoData' => $sectionContent('video', [
                                'title' => 'Our Footage',
                                'description' => 'Once upon a time in Budapest . .',
                                'youtube_video_id' => 'kfsqKYsxs0o',
                                'youtube_url' => 'https://www.youtube.com/watch?v=kfsqKYsxs0o',
                                'embed_url' => 'https://www.youtube.com/embed/kfsqKYsxs0o?rel=0&modestbranding=1',
                            ]),
                        ])
                        @break

                        @case('save_the_date')
                        {{-- SAVE THE DATE --}}
                        @include('templates.anselma.partials.original-save-date', [
                            'saveDateData' => $sectionContent('save_the_date', [
                                'title' => 'Save the date',
                                'button_text' => 'Add to Calendar',
                                'calendar_title' => trim(($couple['groom'] ?? 'Groom') . ' & ' . ($couple['bride'] ?? 'Bride') . ' Wedding'),
                                'calendar_details' => 'You are invited to our wedding ceremony.',
                                'calendar_duration_minutes' => 180,
                                'event_date' => $profile->event_date
                                    ? \Carbon\Carbon::parse($profile->event_date)->toIso8601String()
                                    : null,
                            ]),
                        ])
                        @break

                        @case('agenda')
                            {{-- AGENDA --}}
                            @php
                                $agendaContent = $sectionContent('agenda', [
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
                                    'dress_code' => [
                                        'title' => 'Dresscode',
                                        'description' => 'Wear a long gown or formal gown, black tie or bow tie',
                                        'note' => 'We kindly ask that guests please attend wearing our wedding colors.',
                                        'men_label' => 'Men',
                                        'women_label' => 'Women',
                                        'men_style_label' => 'Formal',
                                        'women_style_label' => 'Formal',
                                        'colors' => ['#7F0404', '#1C3106', '#D2CEAE', '#E6E3E4'],
                                    ],
                                ]);
                            @endphp

                            @include('templates.anselma.partials.original-agenda', [
                                'agenda' => $agendaContent,
                                'dressCode' => $agendaContent['dress_code'] ?? [],
                            ])
                            @break

                    @case('rsvp')
                        {{-- RSVP --}}
                        @include('templates.anselma.partials.original-rsvp', [
                            'rsvpData' => $sectionContent('rsvp'),
                        ])
                        @break

                    @case('live_streaming')
                        {{-- LIVE STREAMING --}}
                        @include('templates.anselma.partials.original-live-streaming', [
                            'liveStreaming' => $sectionContent('live_streaming', [
                                'title' => 'Live Streaming',
                                'description' => '',
                                'thumbnail' => 'maxresdefault(2).jpg',
                                'video_id' => 'tUXguBEeRCE',
                                'watch_url' => 'https://youtu.be/tUXguBEeRCE?si=oVEheAY_-SVHUP67',
                            ]),
                        ])
                        @break

                    @case('wedding_gift')
                        {{-- WEDDING GIFT --}}
                        @php
                            $giftContent = $sectionContent('wedding_gift', [
                                'title' => 'Wedding Gift',
                                'description' => 'Your blessing and coming to our wedding are enough for us. However, if you want to give a gift we provide a Digital Envelope to make it easier for you. Thank you.',
                                'accounts' => [
                                    [
                                        'id' => 'bri',
                                        'bank' => 'BANK BRI (002)',
                                        'bank_short' => 'BANK BRI',
                                        'number' => '02122333214',
                                        'name' => 'Varo',
                                    ],
                                    [
                                        'id' => 'mandiri',
                                        'bank' => 'BANK MANDIRI (008)',
                                        'bank_short' => 'BANK MANDIRI',
                                        'number' => '0011002230',
                                        'name' => 'Ansel',
                                    ],
                                ],
                                'physical_gift' => [
                                    'title' => 'Send us a gift',
                                    'description' => 'Silahkan kirimkan hadiah kepada kedua mempelai',
                                    'recipient' => 'Ansel',
                                    'phone' => '082365144995',
                                    'address' => 'Jalan Kenangan Raya',
                                ],
                            ]);
                        @endphp

                        @include('templates.anselma.partials.original-gift', [
                            'giftData' => $giftContent,
                            'physicalGift' => $giftContent['physical_gift'] ?? null,
                        ])
                        @break

                    @case('wedding_wish')
                        {{-- WEDDING WISH --}}
                        @include('templates.anselma.partials.original-wish', [
                            'wishData' => $sectionContent('wedding_wish', [
                                'title' => 'Wedding Wish',
                                'description' => '',
                                'guest_name' => $guestName,
                                'placeholder' => 'Give your wish',
                                'button_text' => 'Send',
                                'show_more' => false,
                                'wishes' => [],
                            ]),
                        ])
                        @break

                    @case('footer')
                        {{-- FOOTER / CLOSING --}}
                        @include('templates.anselma.partials.original-footer', [
                            'footerData' => $sectionContent('footer', [
                                'title' => 'Thank You',
                                'description' => 'With love and gratitude, thank you for celebrating with us.',
                            ]),
                        ])
                        @break

                @endswitch
            @endforeach
        </section>
    </section>

        {{-- Music original Katsudoto structure --}}
    <section
        class="music-outer"
        data-aos="fade-up"
        data-aos-duration="1000"
        data-aos-delay="300"
    >
        {{-- Jangan pakai class auto/playing di awal --}}
        <div class="music-box" id="music-box"></div>
    </section>

    {{-- Alert & Modal dummy, dibutuhkan universal.js/template.js --}}
    <div class="alert" id="alert">
        <div class="alert-text"></div>
        <div class="alert-close fas fa-times"></div>
    </div>

    <div id="modal" class="modal modal-center"></div>

    {{-- Config harus berada sebelum universal.js, template.js, dan exclusive-anselma.js --}}
    <script>
        window.__INVITATION_OPENED = false;
        window.__MUSIC_USER_PAUSED = false;

        window.LANG_ID = false;

        window.CROPPED_SONG = {
            start: null,
            end: null,
        };

        window.LANGUAGE_TOGGLE = 1;

        /*
         * Section sekarang dikontrol oleh Laravel/database,
         * bukan oleh section manager bawaan Katsudoto.
         */
        window.MANAGE_SECTION_ENABLED = false;
        window.INVITATION_LAYOUTS = [];
        window.DEFAULT_LAYOUTS = [];

        window.ANSELMA_SECTION_ORDER = @json(
            $orderedSections
                ->where('is_active', true)
                ->pluck('section_key')
                ->values()
        );

        window.SECTION_HIDDEN_CLASS = {
            greet_thanks: "",
            love_story: "",
            gallery_photo: "",
            gallery_video: "",
            rsvp: "",
            live_streaming: "",
            filter_instagram: "",
            health_protocol: "",
            save_the_date: "",
            wedding_gift: "",
            wedding_wish: "",
            agenda: "",
            quote: "",
            cover: "",
            couple: ""
        };

        /*
         * Ini wajib ada.
         * Tanpa window.MUSIC, controller musik lokal tidak bisa membuat audio.
         */
        window.MUSIC = {
            url: "{{ asset('templates/anselma/files/music.mp3') }}",
            box: "#music-box"
        };

        window.RSVP = {
            button_text: {
                attend: "Will Attend",
                not_attend: "Unable To Attend"
            }
        };

        window.RSVP_DATA = {
            post: "rsvp_request",
            request: "get_rsvp",
            content: "",
            template: "template_anselma",
            changeButton: "#changeRSVP",
            amountElement: "#rsvpAmountWrap"
        };

        window.KADO_DATA = {
            post: "post_kado_data",
            request: "getKado",
            content: "",
            template: "template_anselma"
        };

        window.ANSELMA_PREVIEW_MODE = true;
    </script>

    {{-- Guard harus sebelum semua JS Katsudoto --}}
    <script>
        (function () {
            if (window.__ANSELMA_MUSIC_PLAY_GUARD_INSTALLED) {
                return;
            }

            window.__ANSELMA_MUSIC_PLAY_GUARD_INSTALLED = true;

            const originalPlay = HTMLMediaElement.prototype.play;

            HTMLMediaElement.prototype.play = function () {
                const musicUrl = window.MUSIC && window.MUSIC.url;

                const isMusic =
                    this.id === "anselmaLocalMusic" ||
                    this.closest?.("#music-box") ||
                    (musicUrl && (this.currentSrc || this.src || "").includes(musicUrl));

                if (isMusic && !window.__INVITATION_OPENED) {
                    console.warn("Blocked music play before Open Invitation.");
                    return Promise.resolve();
                }

                if (isMusic && window.__MUSIC_USER_PAUSED) {
                    console.warn("Blocked music play because user paused the music.");
                    return Promise.resolve();
                }

                return originalPlay.apply(this, arguments);
            };
        })();
    </script>

    {{-- Katsudoto original JS --}}
    <script src="{{ asset('templates/anselma/js/jquery.js') }}"></script>
    <script>
    (function () {
        window.ANSELMA_PREVIEW_MODE = true;

        function isPreviewPostTarget(url) {
            if (!url || url === "#" || url === window.location.href) {
                return true;
            }

            try {
                const targetUrl = new URL(url, window.location.origin);

                return targetUrl.origin === window.location.origin &&
                    targetUrl.pathname === window.location.pathname;
            } catch (error) {
                return true;
            }
        }

        function getFakePreviewResponse() {
            return {
                error: false,
                message: "",
                modal: "",
                content: "",
                rsvp_content: "",
                hadiah_content: "",
                comment_content: "",
                comments_content: "",
                data: [],
            };
        }

        function installAjaxPreviewGuard() {
            if (!window.jQuery || window.__ANSELMA_AJAX_PREVIEW_GUARD_INSTALLED) {
                return;
            }

            window.__ANSELMA_AJAX_PREVIEW_GUARD_INSTALLED = true;

            const $ = window.jQuery;
            const originalAjax = $.ajax;
            const originalPost = $.post;

            $.ajax = function (url, options) {
                let ajaxOptions = {};

                if (typeof url === "object") {
                    ajaxOptions = url || {};
                } else {
                    ajaxOptions = options || {};
                    ajaxOptions.url = url;
                }

                const method = String(
                    ajaxOptions.type ||
                    ajaxOptions.method ||
                    "GET"
                ).toUpperCase();

                const targetUrl = ajaxOptions.url || window.location.href;

                if (
                    window.ANSELMA_PREVIEW_MODE &&
                    method === "POST" &&
                    isPreviewPostTarget(targetUrl)
                ) {
                    const fakeResponse = getFakePreviewResponse();
                    const deferred = $.Deferred();
                    const jqXHR = deferred.promise();

                    jqXHR.abort = function () {
                        deferred.reject(jqXHR, "abort", "abort");
                        return jqXHR;
                    };

                    window.setTimeout(function () {
                        if (typeof ajaxOptions.beforeSend === "function") {
                            ajaxOptions.beforeSend(jqXHR, ajaxOptions);
                        }

                        if (typeof ajaxOptions.success === "function") {
                            ajaxOptions.success(fakeResponse, "success", jqXHR);
                        }

                        deferred.resolve(fakeResponse, "success", jqXHR);

                        if (typeof ajaxOptions.complete === "function") {
                            ajaxOptions.complete(jqXHR, "success");
                        }
                    }, 0);

                    // console.info("Blocked Katsudoto preview POST:", targetUrl);

                    return jqXHR;
                }

                return originalAjax.apply(this, arguments);
            };

            $.post = function (url, data, success, dataType) {
                if (
                    window.ANSELMA_PREVIEW_MODE &&
                    isPreviewPostTarget(url)
                ) {
                    return $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        success: success,
                        dataType: dataType,
                    });
                }

                return originalPost.apply(this, arguments);
            };

            window.postData = function (
                data,
                onSuccess = function () {},
                onError = function () {},
                beforeSend = function () {},
                callback_xhr = function () {},
                props = {}
            ) {
                const fakeResponse = getFakePreviewResponse();

                if (typeof beforeSend === "function") {
                    beforeSend();
                }

                if (typeof onSuccess === "function") {
                    onSuccess(fakeResponse);
                }

                return false;
            };
        }

        installAjaxPreviewGuard();
    })();
    </script>
    <script src="{{ asset('templates/anselma/js/tsparticles.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/aos.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/slick.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/selectize.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/jquery-modal-video.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/lightgallery.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/video.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/Youtube.min.js') }}"></script>
    <script src="{{ asset('templates/anselma/js/html2canvas-1.4.1.1749130377.js') }}"></script>

    {{-- Universal helper --}}
    <script src="{{ asset('templates/anselma/js/universal.js') }}"></script>

    {{-- Core Katsudoto logic --}}
    <script src="{{ asset('templates/anselma/js/template.js') }}"></script>

    {{-- Theme-specific Anselma logic --}}
    <script src="{{ asset('templates/anselma/js/exclusive-anselma.js') }}"></script>

    <script>
    (function () {
        function getSectionKey(element) {
            if (!element) return null;

            if (element.classList.contains("cover")) {
                return "cover";
            }

            if (element.classList.contains("couple-wrap")) {
                return "couple";
            }

            if (element.classList.contains("quote-sec-wrap")) {
                return "quote";
            }

            if (element.classList.contains("love-story")) {
                return "story";
            }

            if (element.classList.contains("photo-wrap")) {
                return "gallery";
            }

            if (element.classList.contains("video-gallery")) {
                return "video";
            }

            if (element.classList.contains("save-date-wrap")) {
                return "save_the_date";
            }

            if (element.classList.contains("agenda-wrap")) {
                return "agenda";
            }

            if (
                element.classList.contains("rsvp-wrap") ||
                element.querySelector("#rsvpPreviewForm")
            ) {
                return "rsvp";
            }

            if (element.classList.contains("live-streaming")) {
                return "live_streaming";
            }

            if (
                element.classList.contains("wedding-gift-wrap") ||
                element.querySelector(".wedding-gift-wrap")
            ) {
                return "wedding_gift";
            }

            if (element.classList.contains("wedding-wish-wrap")) {
                return "wedding_wish";
            }

            if (element.classList.contains("quote-message-wrap")) {
                return "footer";
            }

            return null;
        }

        window.enforceAnselmaServerSectionOrder = function () {
            const pane = document.querySelector(".secondary-pane");

            if (!pane) {
                return;
            }

            const desiredOrder = window.ANSELMA_SECTION_ORDER || [];

            if (!desiredOrder.length) {
                return;
            }

            const children = [...pane.children];
            const sectionMap = new Map();

            children.forEach(function (child) {
                const key = getSectionKey(child);

                if (key && !sectionMap.has(key)) {
                    sectionMap.set(key, child);
                }
            });

            desiredOrder.forEach(function (key) {
                const sectionElement = sectionMap.get(key);

                if (sectionElement) {
                    pane.appendChild(sectionElement);
                }
            });
        };

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", function () {
                window.enforceAnselmaServerSectionOrder();

                setTimeout(window.enforceAnselmaServerSectionOrder, 300);
            });
        } else {
            window.enforceAnselmaServerSectionOrder();

            setTimeout(window.enforceAnselmaServerSectionOrder, 300);
        }

        window.addEventListener("load", function () {
            setTimeout(window.enforceAnselmaServerSectionOrder, 300);
        });
    })();
    </script>

    {{-- Final local preview controller --}}
    <script>
        (function () {
            let localAudio = null;

            function getMusicBox() {
                let musicBox = document.querySelector("#music-box");

                if (musicBox) {
                    return musicBox;
                }

                let musicOuter = document.querySelector(".music-outer");

                if (!musicOuter) {
                    musicOuter = document.createElement("section");
                    musicOuter.className = "music-outer";
                    document.body.appendChild(musicOuter);
                }

                musicBox = document.createElement("div");
                musicBox.id = "music-box";
                musicBox.className = "music-box";
                musicOuter.appendChild(musicBox);

                return musicBox;
            }

            function resetMusicBoxListeners() {
                const oldBox = getMusicBox();

                if (!oldBox || !oldBox.parentNode) {
                    return null;
                }

                const newBox = oldBox.cloneNode(false);
                newBox.id = "music-box";
                newBox.className = "music-box";
                newBox.style.animationPlayState = "paused";

                oldBox.parentNode.replaceChild(newBox, oldBox);

                return newBox;
            }

            function getMusicAudio() {
                const musicUrl =
                    (window.MUSIC && window.MUSIC.url)
                        ? window.MUSIC.url
                        : "{{ asset('templates/anselma/files/music.mp3') }}";

                if (!window.MUSIC) {
                    window.MUSIC = {
                        url: musicUrl,
                        box: "#music-box"
                    };
                }

                let audio = document.getElementById("anselmaLocalMusic");

                if (!audio) {
                    audio = document.createElement("audio");
                    audio.id = "anselmaLocalMusic";
                    audio.loop = true;
                    audio.preload = "auto";
                    audio.setAttribute("playsinline", "");
                    audio.style.display = "none";

                    document.body.appendChild(audio);
                }

                if (audio.getAttribute("src") !== musicUrl) {
                    audio.setAttribute("src", musicUrl);
                }

                audio.loop = true;
                audio.muted = false;
                audio.volume = 1;

                return audio;
            }

            function setMusicState(isPlaying) {
                const musicBox = getMusicBox();

                if (!musicBox) {
                    return;
                }

                musicBox.classList.remove("auto");
                musicBox.classList.toggle("playing", isPlaying);
                musicBox.classList.toggle("active", isPlaying);
                musicBox.style.animationPlayState = isPlaying ? "running" : "paused";
            }

            function forceMusicOff(resetTime = false) {
                const audio = getMusicAudio();

                window.__MUSIC_USER_PAUSED = true;

                if (audio && typeof audio.pause === "function") {
                    audio.pause();

                    if (resetTime) {
                        try {
                            audio.currentTime = 0;
                        } catch (error) {}
                    }
                }

                setMusicState(false);
            }

            function playWeddingMusic() {
                const audio = getMusicAudio();

                if (!audio || typeof audio.play !== "function") {
                    console.warn("Music audio element not available.");
                    return;
                }

                window.__MUSIC_USER_PAUSED = false;

                const playPromise = audio.play();

                if (playPromise && typeof playPromise.then === "function") {
                    playPromise
                        .then(function () {
                            setMusicState(true);
                        })
                        .catch(function (error) {
                            console.warn("Music play blocked or failed:", error);
                            setMusicState(false);
                        });
                }
            }

            function unlockInvitation(event) {
                if (event) {
                    event.preventDefault();
                }

                window.__INVITATION_OPENED = true;
                window.__MUSIC_USER_PAUSED = false;

                /*
                 * Play harus dipanggil langsung di event click.
                 * Jangan pakai setTimeout sebelum audio.play().
                 */
                playWeddingMusic();

                const body = document.body;
                const html = document.documentElement;
                const openingGate = document.getElementById("openingGate");

                body.classList.remove("is-gate-locked", "template-locked");

                html.style.overflowY = "auto";
                html.style.overflowX = "hidden";
                body.style.overflowY = "auto";
                body.style.overflowX = "hidden";

                if (openingGate) {
                    openingGate.classList.add("is-hidden");
                }

                if (window.AOS) {
                    setTimeout(function () {
                        window.AOS.refresh();
                    }, 300);
                }
            }

            function bindOpenInvitation() {
                const openButton = document.getElementById("openInvitation");

                if (!openButton) {
                    document.body.classList.remove("is-gate-locked");
                    return;
                }

                openButton.addEventListener("click", unlockInvitation);
            }

            function bindMusicToggle() {
                const musicBox = getMusicBox();

                if (!musicBox) {
                    return;
                }

                musicBox.addEventListener("click", function (event) {
                    event.preventDefault();

                    if (!window.__INVITATION_OPENED) {
                        return;
                    }

                    const audio = getMusicAudio();

                    if (!audio) {
                        return;
                    }

                    if (audio.paused) {
                        playWeddingMusic();
                    } else {
                        forceMusicOff(false);
                    }
                });
            }

            function hideLoader() {
                const loader = document.getElementById("anselmaLoader");

                if (loader) {
                    loader.classList.add("is-hidden");
                }
            }

            function bindFormsPreviewGuard() {
                document.querySelectorAll("form").forEach(function (form) {
                    form.addEventListener("submit", function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    });
                });
            }

            function initAOS() {
                if (!window.AOS) {
                    return;
                }

                window.AOS.init({
                    offset: 10,
                    duration: 400,
                    easing: "ease",
                    once: true,
                    mirror: false
                });

                setTimeout(function () {
                    window.AOS.refresh();
                }, 500);
            }

            function initPreviewController() {
                window.__INVITATION_OPENED = false;
                window.__MUSIC_USER_PAUSED = false;

                /*
                 * Ini penting:
                 * setelah template.js/exclusive-anselma.js selesai,
                 * kita clone #music-box agar event listener bawaan yang memicu autoplay
                 * tidak ikut aktif di preview Laravel.
                 */
                resetMusicBoxListeners();

                getMusicAudio();
                setMusicState(false);
                forceMusicOff(true);

                bindOpenInvitation();
                bindMusicToggle();
                bindFormsPreviewGuard();
                initAOS();

                if (document.readyState === "complete") {
                    setTimeout(hideLoader, 500);
                } else {
                    window.addEventListener("load", function () {
                        setTimeout(hideLoader, 500);
                    });
                }

                setTimeout(hideLoader, 1800);
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initPreviewController);
            } else {
                initPreviewController();
            }
        })();
    </script>

    <script>
    (function () {
        if (window.__anselmaRsvpDelegatedFixInstalled) {
            return;
        }

        window.__anselmaRsvpDelegatedFixInstalled = true;

        function initAnselmaRsvpFix() {
            /*console.log("✅ Installing RSVP delegated fix...");*/

            const rsvpWrap = document.querySelector(".rsvp-wrap.anselma-rsvp-original");
            const form = document.getElementById("rsvpPreviewForm");

            if (!rsvpWrap || !form) {
                console.warn("RSVP delegated fix skipped.", { rsvpWrap, form });
                return;
            }

            const sessionField = document.getElementById("rsvpSessionField");
            const totalField = document.getElementById("rsvpTotalField");
            const eventAll = document.getElementById("rsvpEventAll");
            const totalInput = document.getElementById("rsvpTotalGuests");

            const minusBtn = form.querySelector("[data-preview-rsvp-minus]");
            const plusBtn = form.querySelector("[data-preview-rsvp-plus]");

            function getEventInputs() {
                return [...form.querySelectorAll('input[name="selected_event[]"]')];
            }

            function getSelectedStatus() {
                const checked = form.querySelector('input[name="rsvp_status"]:checked');
                return checked ? checked.value : "attend";
            }

            function setFieldVisibility(element, isVisible) {
                if (!element) return;

                element.classList.toggle("is-hidden", !isVisible);
                element.style.display = isVisible ? "" : "none";
            }

            function syncAttendanceState() {
                const status = getSelectedStatus();
                const isAttend = status === "attend";

                rsvpWrap.classList.toggle("is-not-attending", !isAttend);

                setFieldVisibility(sessionField, isAttend);
                setFieldVisibility(totalField, isAttend);

                /*console.log("RSVP status changed:", {
                    status,
                    sessionDisplay: sessionField ? getComputedStyle(sessionField).display : null,
                    totalDisplay: totalField ? getComputedStyle(totalField).display : null,
                    wrapClass: rsvpWrap.className
                });*/
            }

            function syncChildrenFromEventAll() {
                const eventInputs = getEventInputs();

                if (!eventAll || !eventInputs.length) return;

                eventInputs.forEach(function (input) {
                    input.checked = eventAll.checked;
                });

                eventAll.indeterminate = false;

                /*console.log("Attend all changed:", {
                    all: eventAll.checked,
                    events: eventInputs.map(function (input) {
                        return {
                            value: input.value,
                            checked: input.checked
                        };
                    })
                });*/
            }

            function syncEventAllFromChildren() {
                const eventInputs = getEventInputs();

                if (!eventAll || !eventInputs.length) return;

                const checkedCount = eventInputs.filter(function (input) {
                    return input.checked;
                }).length;

                const allChecked = checkedCount === eventInputs.length;
                const noneChecked = checkedCount === 0;

                eventAll.checked = allChecked;
                eventAll.indeterminate = !allChecked && !noneChecked;
            }

            function clampTotal(value) {
                if (!totalInput) return 1;

                const min = Number(totalInput.getAttribute("min") || 1);
                const max = Number(totalInput.getAttribute("max") || 10);

                return Math.min(Math.max(value, min), max);
            }

            function updateAmountButtonState() {
                if (!totalInput) return;

                const value = Number(totalInput.value || 1);
                const min = Number(totalInput.getAttribute("min") || 1);
                const max = Number(totalInput.getAttribute("max") || 10);

                if (minusBtn) {
                    minusBtn.disabled = value <= min;
                    minusBtn.classList.toggle("is-disabled", value <= min);
                }

                if (plusBtn) {
                    plusBtn.disabled = value >= max;
                    plusBtn.classList.toggle("is-disabled", value >= max);
                }
            }

            function flashButton(button) {
                if (!button) return;

                button.classList.add("is-pressed");

                setTimeout(function () {
                    button.classList.remove("is-pressed");
                }, 160);
            }

            function updateTotal(delta, button) {
                if (!totalInput) return;

                const current = Number(totalInput.value || 1);
                const next = clampTotal(current + delta);

                totalInput.value = next;

                flashButton(button);
                updateAmountButtonState();

                /*console.log("Guest total:", totalInput.value);*/
            }

            /*
            * Delegated change handler.
            * Ini menggantikan listener langsung ke attend/notAttend/eventAll.
            */
            form.addEventListener("change", function (event) {
            const target = event.target;

            if (!target) return;

            if (target.matches('input[name="rsvp_status"]')) {
                syncAttendanceState();
                return;
            }

            /*
            * Jangan handle #rsvpEventAll di change.
            * Hadir Semua akan di-handle khusus lewat click supaya tidak dobel.
            */
            if (target.matches('input[name="selected_event[]"]')) {
                syncEventAllFromChildren();
                return;
            }
        }, true);

        // click handler khusus Hadir Semua
        form.addEventListener("click", function (event) {
            const allButton = event.target.closest(".rsvp-session-btn.all");
            const allInput = event.target.matches("#rsvpEventAll")
                ? event.target
                : null;

            const isAllControl = allButton || allInput;

            if (!isAllControl || !eventAll) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            /*
            * Karena default click kita prevent,
            * maka checked harus kita toggle manual.
            */
            eventAll.checked = !eventAll.checked;
            eventAll.indeterminate = false;

            const eventInputs = getEventInputs();

            eventInputs.forEach(function (input) {
                input.checked = eventAll.checked;
            });

            /*console.log("Attend all clicked manually:", {
                all: eventAll.checked,
                events: eventInputs.map(function (input) {
                    return {
                        value: input.value,
                        checked: input.checked
                    };
                })
            });*/
        }, true);

            /*
            * Delegated click untuk plus/minus.
            * Karena atributnya sudah data-preview, tidak bentrok dengan Katsudoto.
            */
            form.addEventListener("click", function (event) {
                const plus = event.target.closest("[data-preview-rsvp-plus]");
                const minus = event.target.closest("[data-preview-rsvp-minus]");

                if (!plus && !minus) return;

                event.preventDefault();
                event.stopPropagation();

                if (plus && !plus.disabled) {
                    updateTotal(1, plus);
                }

                if (minus && !minus.disabled) {
                    updateTotal(-1, minus);
                }
            }, true);

            form.addEventListener("submit", function (event) {
            event.preventDefault();
            event.stopPropagation();

            const status = getSelectedStatus();
            const isAttend = status === "attend";

            const submittedData = {
                status: status,
                events: isAttend
                    ? getEventInputs()
                        .filter(function (input) {
                            return input.checked;
                        })
                        .map(function (input) {
                            return input.value;
                        })
                    : [],
                attendAll: isAttend && eventAll ? eventAll.checked : false,
                total: isAttend && totalInput ? totalInput.value : "0"
            };

            /*console.log("RSVP preview submitted", submittedData);*/

            return false;
        });

            syncAttendanceState();
            syncEventAllFromChildren();
            updateAmountButtonState();

            /*console.log("✅ RSVP delegated fix installed.");*/
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", initAnselmaRsvpFix);
        } else {
            initAnselmaRsvpFix();
        }
    })();
    </script>

    <script>
    document.addEventListener("click", function (event) {
        const playButton = event.target.closest(".live-streaming .play-btn");

        if (!playButton) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const videoId = playButton.getAttribute("data-video-id");

        if (!videoId) {
            return;
        }

        window.open("https://www.youtube.com/watch?v=" + videoId, "_blank", "noopener");
    }, true);
    </script>

    <script>
    (function () {
        if (window.__anselmaWishPreviewGuardInstalled) {
            return;
        }

        window.__anselmaWishPreviewGuardInstalled = true;

        const wishForm = document.getElementById("weddingWishForm");

        if (wishForm) {
            wishForm.addEventListener("submit", function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                const nameInput = wishForm.querySelector(".guest-name");
                const commentInput = wishForm.querySelector(".guest-comment");

                const name = nameInput ? nameInput.value.trim() : "";
                const comment = commentInput ? commentInput.value.trim() : "";

                if (!comment) {
                    return false;
                }

                const commentWrap = document.querySelector(".wedding-wish-wrap .comment-wrap");

                if (commentWrap) {
                    const newComment = document.createElement("div");
                    newComment.className = "comment-item";
                    newComment.setAttribute("data-aos", "fade-up");
                    newComment.setAttribute("data-aos-duration", "1200");

                    newComment.innerHTML = `
                        <div class="comment-head">
                            <div class="ch-name-wrap">
                                <h3 class="comment-name">${name || "Guest"}</h3>
                            </div>
                            <small class="comment-date">Just now</small>
                        </div>
                        <div class="comment-body">
                            <p class="comment-caption">${comment}</p>
                        </div>
                    `;

                    commentWrap.prepend(newComment);
                    commentWrap.classList.add("show");
                }

                if (commentInput) {
                    commentInput.value = "";
                }

                return false;
            }, true);
        }

        document.addEventListener("click", function (event) {
            const moreCommentButton = event.target.closest("#moreComment");
            const deleteCommentButton = event.target.closest(".delete-comment");

            if (!moreCommentButton && !deleteCommentButton) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            return false;
        }, true);
    })();
    </script>

    @if (isset($guest))
    @php
        $guestTrackingUrl = route('invitation.guest.track', [
            'slug' => request()->route('slug'),
            'code' => request()->route('code'),
        ]);
    @endphp

    <script>
        (function () {
            const trackUrl = @json($guestTrackingUrl);
            const csrfToken = @json(csrf_token());

            const startTime = Date.now();

            let maxScrollPercent = 0;
            let isSending = false;

            /*
             * DEBUG MODE
             * Ubah ke false kalau sudah aman.
             */
            const debugTracking = true;

            function getScrollContainer() {
                const candidates = [
                    document.querySelector('.secondary-pane'),
                    document.querySelector('.kat-page__side-to-side'),
                    document.scrollingElement,
                    document.documentElement,
                    document.body
                ];

                for (const element of candidates) {
                    if (!element) {
                        continue;
                    }

                    const scrollHeight = element.scrollHeight || 0;
                    const clientHeight = element.clientHeight || window.innerHeight || 0;

                    if (scrollHeight > clientHeight + 20) {
                        return element;
                    }
                }

                return document.scrollingElement || document.documentElement;
            }

            function calculateScrollPercent() {
                const container = getScrollContainer();

                let scrollTop = 0;
                let scrollHeight = 0;
                let clientHeight = 0;

                if (
                    container === document.scrollingElement ||
                    container === document.documentElement ||
                    container === document.body
                ) {
                    scrollTop = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
                    scrollHeight = Math.max(
                        document.documentElement.scrollHeight,
                        document.body.scrollHeight
                    );
                    clientHeight = window.innerHeight;
                } else {
                    scrollTop = container.scrollTop;
                    scrollHeight = container.scrollHeight;
                    clientHeight = container.clientHeight;
                }

                const scrollableHeight = scrollHeight - clientHeight;

                /*
                 * Jangan langsung return 100.
                 * Kalau belum ada scrollable height, anggap 0 dulu.
                 */
                if (scrollableHeight <= 0) {
                    if (debugTracking) {
                        console.log('[Guest Tracking] Scroll height belum valid', {
                            container: container.className || container.tagName,
                            scrollTop,
                            scrollHeight,
                            clientHeight,
                            scrollableHeight
                        });
                    }

                    return 0;
                }

                const percent = Math.round((scrollTop / scrollableHeight) * 100);

                const safePercent = Math.max(0, Math.min(100, percent));

                if (debugTracking) {
                    console.log('[Guest Tracking] Scroll debug', {
                        container: container.className || container.tagName,
                        scrollTop,
                        scrollHeight,
                        clientHeight,
                        scrollableHeight,
                        percent: safePercent
                    });
                }

                return safePercent;
            }

            function updateScrollPercent() {
                maxScrollPercent = Math.max(maxScrollPercent, calculateScrollPercent());
            }

            function getDurationSeconds() {
                return Math.max(0, Math.round((Date.now() - startTime) / 1000));
            }

            function buildPayload() {
                return {
                    _token: csrfToken,
                    duration_seconds: getDurationSeconds(),
                    max_scroll_percent: maxScrollPercent,
                };
            }

            function sendTracking(useBeacon = false) {
                updateScrollPercent();

                const payload = buildPayload();

                if (debugTracking) {
                    console.log('[Guest Tracking] Sending payload', payload);
                }

                if (useBeacon && navigator.sendBeacon) {
                    const formData = new FormData();

                    formData.append('_token', payload._token);
                    formData.append('duration_seconds', payload.duration_seconds);
                    formData.append('max_scroll_percent', payload.max_scroll_percent);

                    navigator.sendBeacon(trackUrl, formData);
                    return;
                }

                if (isSending) {
                    return;
                }

                isSending = true;

                fetch(trackUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                })
                    .catch(function (error) {
                        if (debugTracking) {
                            console.warn('[Guest Tracking] Failed', error);
                        }
                    })
                    .finally(function () {
                        isSending = false;
                    });
            }

            function bindScrollListeners() {
                const container = getScrollContainer();

                window.addEventListener('scroll', updateScrollPercent, {
                    passive: true
                });

                if (
                    container &&
                    container !== document.scrollingElement &&
                    container !== document.documentElement &&
                    container !== document.body
                ) {
                    container.addEventListener('scroll', updateScrollPercent, {
                        passive: true
                    });
                }

                if (debugTracking) {
                    console.log('[Guest Tracking] Scroll listener attached to', {
                        container: container.className || container.tagName,
                        scrollHeight: container.scrollHeight,
                        clientHeight: container.clientHeight
                    });
                }
            }

            setTimeout(function () {
                updateScrollPercent();
                bindScrollListeners();
            }, 500);

            setInterval(function () {
                sendTracking(false);
            }, 15000);

            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    sendTracking(true);
                }
            });

            window.addEventListener('beforeunload', function () {
                sendTracking(true);
            });
        })();
    </script>
@endif
</body>
</html>