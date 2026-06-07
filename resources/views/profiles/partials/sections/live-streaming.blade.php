@php
    $liveStreamingSection = $liveStreamingSection ?? null;
    $liveStreamingContent = $liveStreamingSection ? ($liveStreamingSection->content ?? []) : [];

    $liveStreamingTitle = old('title', $liveStreamingContent['title'] ?? 'Live Streaming');
    $liveStreamingDescription = old('description', $liveStreamingContent['description'] ?? '');
    $liveStreamingThumbnail = old('thumbnail', $liveStreamingContent['thumbnail'] ?? 'maxresdefault(2).jpg');
    $liveStreamingVideoId = old('video_id', $liveStreamingContent['video_id'] ?? '');
    $liveStreamingWatchUrl = old('watch_url', $liveStreamingContent['watch_url'] ?? '');

    $liveStreamingThumbnailUrl = null;

    if ($liveStreamingThumbnail) {
        if (str_starts_with($liveStreamingThumbnail, 'live-streaming/')) {
            $liveStreamingThumbnailUrl = asset('storage/' . $liveStreamingThumbnail);
        } elseif (str_starts_with($liveStreamingThumbnail, 'http://') || str_starts_with($liveStreamingThumbnail, 'https://')) {
            $liveStreamingThumbnailUrl = $liveStreamingThumbnail;
        } else {
            $liveStreamingThumbnailUrl = asset('templates/anselma/files/' . $liveStreamingThumbnail);
        }
    }
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Live Streaming
            </h2>

            <p class="invitation-form-desc">
                Atur judul, thumbnail, dan link YouTube yang tampil pada section Live Streaming template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form
            method="POST"
            action="{{ route('profile.sections.live-streaming.update') }}"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">LS</div>

                    <div>
                        <h3 class="form-section-title">
                            Detail Live Streaming
                        </h3>

                        <div class="form-section-subtitle">
                            Data ini akan masuk ke partial original-live-streaming Anselma.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul Section
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ $liveStreamingTitle }}"
                            placeholder="Contoh: Live Streaming"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Upload Thumbnail
                        </label>

                        <input
                            type="file"
                            name="thumbnail_file"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <input
                            type="hidden"
                            name="thumbnail"
                            value="{{ $liveStreamingThumbnail }}"
                        >

                        <div class="form-help">
                            Format: JPG, PNG, atau WEBP. Maksimal 4 MB. Disarankan rasio 16:9, contoh 1280×720 px.
                        </div>

                        @if ($liveStreamingThumbnailUrl)
                            <div style="margin-top: 14px;">
                                <div class="form-help" style="margin-bottom: 8px;">
                                    Thumbnail saat ini:
                                </div>

                                <img
                                    src="{{ $liveStreamingThumbnailUrl }}"
                                    alt="Live Streaming Thumbnail"
                                    style="width: 220px; max-width: 100%; border-radius: 16px; border: 1px solid var(--border); display: block;"
                                >
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            YouTube Video ID
                        </label>

                        <input
                            type="text"
                            name="video_id"
                            class="form-control"
                            value="{{ $liveStreamingVideoId }}"
                            placeholder="Contoh: tUXguBEeRCE"
                        >

                        <div class="form-help">
                            Jika link YouTube adalah https://www.youtube.com/watch?v=tUXguBEeRCE, maka video ID-nya adalah <strong>tUXguBEeRCE</strong>.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Link YouTube
                        </label>

                        <input
                            type="url"
                            name="watch_url"
                            class="form-control"
                            value="{{ $liveStreamingWatchUrl }}"
                            placeholder="https://youtu.be/..."
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            placeholder="Tulis deskripsi live streaming jika diperlukan"
                        >{{ $liveStreamingDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <button class="btn-gold-inline" type="submit">
                    Simpan Live Streaming
                </button>
            </div>
        </form>
    </div>
</section>