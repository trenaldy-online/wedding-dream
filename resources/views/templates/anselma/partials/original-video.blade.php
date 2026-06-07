@php
    $videoData = array_replace_recursive([
        'title' => 'Our Footage',
        'description' => 'Once upon a time in Budapest . .',
        'youtube_video_id' => 'kfsqKYsxs0o',
        'youtube_url' => 'https://www.youtube.com/watch?v=kfsqKYsxs0o',
        'embed_url' => 'https://www.youtube.com/embed/kfsqKYsxs0o?rel=0&modestbranding=1',
    ], $videoData ?? []);

    $videoTitle = $videoData['title'] ?? 'Our Footage';
    $videoDescription = $videoData['description'] ?? '';
    $videoId = $videoData['youtube_video_id'] ?? 'kfsqKYsxs0o';

    $videoEmbedUrl = $videoData['embed_url'] ?? '';

    if ($videoEmbedUrl === '' && $videoId !== '') {
        $videoEmbedUrl = 'https://www.youtube.com/embed/' . $videoId . '?rel=0&modestbranding=1';
    }
@endphp

<section
    class="video-gallery autoplay-video-section original-video-section"
    data-section-order="gallery_video"
    data-onview="false"
    data-pos="0"
>
    <div class="inner">
        <div class="title">
            <h1
                data-aos="zoom-out-up"
                data-aos-duration="1000"
            >
                {{ $videoTitle }}
            </h1>

            @if (!empty($videoDescription))
                <p
                    data-aos="fade-up"
                    data-aos-duration="1000"
                >
                    {{ $videoDescription }}
                </p>
            @endif
        </div>

        <div class="video-outer">
            <div class="video">
                <div class="video-inner">
                    <div
                        class="preview autoplay-video-box"
                        data-aos="zoom-in"
                        data-aos-duration="1000"
                    >
                        <iframe
                            class="anselma-youtube-frame"
                            src="{{ $videoEmbedUrl }}"
                            title="{{ $videoTitle }}"
                            frameborder="0"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>