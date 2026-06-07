@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $liveStreaming = is_array($liveStreaming ?? null) ? $liveStreaming : [];

    $liveStreaming = array_merge([
        'title' => 'Live Streaming',
        'description' => '',
        'thumbnail' => 'maxresdefault(2).jpg',
        'video_id' => 'tUXguBEeRCE',
        'watch_url' => 'https://youtu.be/tUXguBEeRCE?si=oVEheAY_-SVHUP67',
    ], $liveStreaming);

    $liveStreamingTitle = $liveStreaming['title'] ?: 'Live Streaming';
    $liveStreamingDescription = $liveStreaming['description'] ?: '';
    $liveStreamingThumbnail = $liveStreaming['thumbnail'] ?: 'maxresdefault(2).jpg';
    $liveStreamingVideoId = $liveStreaming['video_id'] ?: '';
    $liveStreamingWatchUrl = $liveStreaming['watch_url'] ?: '';

    if ($liveStreamingThumbnail) {
        if (\Illuminate\Support\Str::startsWith($liveStreamingThumbnail, ['http://', 'https://'])) {
            $liveStreamingThumbnailUrl = $liveStreamingThumbnail;
        } elseif (\Illuminate\Support\Str::startsWith($liveStreamingThumbnail, 'live-streaming/')) {
            $liveStreamingThumbnailUrl = asset('storage/' . $liveStreamingThumbnail);
        } else {
            $liveStreamingThumbnailUrl = $img($liveStreamingThumbnail);
        }
    } else {
        $liveStreamingThumbnailUrl = $img('maxresdefault(2).jpg');
    }

    if (! $liveStreamingWatchUrl && $liveStreamingVideoId) {
        $liveStreamingWatchUrl = 'https://www.youtube.com/watch?v=' . $liveStreamingVideoId;
    }
@endphp

<section class="live-streaming" data-section-order="live_streaming">
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

    <div class="ornaments-wrapper"></div>

    <div class="inner">
        <div class="head">
            <h1
                data-aos="zoom-in"
                data-aos-duration="1000"
            >
                {{ $liveStreamingTitle }}
            </h1>

            @if ($liveStreamingDescription)
                <p
                    data-aos="fade-up"
                    data-aos-duration="1000"
                >
                    {{ $liveStreamingDescription }}
                </p>
            @endif
        </div>

        <div class="body">
            <div class="ornaments-wrapper">
                <div class="orn-lv-5">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1200"
                    >
                        <img src="{{ $img('Orn-14.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-lv-2">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1200"
                    >
                        <img src="{{ $img('Orn-24.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-lv-3">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1200"
                    >
                        <img src="{{ $img('Orn-26.png') }}" alt="Ornament">
                    </div>
                </div>

                <div class="orn-lv-1 right">
                    <div class="orn-lv-1-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1400"
                            data-aos-delay="1200"
                        >
                            <img src="{{ $img('Orn-15.png') }}" alt="Ornament">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="900"
                    >
                        <img src="{{ $img('Orn-13.png') }}" alt="Ornament">
                    </div>
                </div>
            </div>

            <div
                class="streaming-info"
                data-aos="fade-up"
                data-aos-duration="1000"
            >
                <div class="p-relative">
                    <div
                        class="preview wide youtube"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                    >
                        <img src="{{ $liveStreamingThumbnailUrl }}" alt="Live Streaming Thumbnail">

                        @if ($liveStreamingVideoId)
                            <button
                                type="button"
                                class="play-btn"
                                data-video-id="{{ $liveStreamingVideoId }}"
                                aria-label="Play live streaming"
                            >
                                <i class="fas fa-play"></i>
                                <span class="play-icon-fallback">▶</span>
                            </button>
                        @endif
                    </div>
                </div>

                @if ($liveStreamingWatchUrl)
                    <div
                        class="link"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                    >
                        <a
                            href="{{ $liveStreamingWatchUrl }}"
                            target="_blank"
                            rel="noopener"
                        >
                            Open Link
                        </a>
                    </div>
                @endif
            </div>

            <div class="ornaments-wrapper">
                <div class="orn-lv-4">
                    <div class="orn-lv-4-1">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1400"
                            data-aos-delay="1200"
                        >
                            <img src="{{ $img('Orn-03.png') }}" alt="Ornament">
                        </div>
                    </div>

                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1400"
                        data-aos-delay="1200"
                    >
                        <img src="{{ $img('Orn-31.png') }}" alt="Ornament">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>