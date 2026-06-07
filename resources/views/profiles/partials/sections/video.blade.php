@php
    $videoSection = $videoSection ?? null;
    $videoContent = $videoSection ? ($videoSection->content ?? []) : [];

    $videoTitle = old('video_title', $videoContent['title'] ?? 'Our Footage');
    $videoDescription = old('video_description', $videoContent['description'] ?? 'Once upon a time in Budapest . .');
    $youtubeVideoId = old('youtube_video_id', $videoContent['youtube_video_id'] ?? 'kfsqKYsxs0o');
    $youtubeUrl = old('youtube_url', $videoContent['youtube_url'] ?? 'https://www.youtube.com/watch?v=' . $youtubeVideoId);
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Video
            </h2>

            <p class="invitation-form-desc">
                Atur video yang tampil pada section Our Footage template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.video.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">VD</div>

                    <div>
                        <h3 class="form-section-title">
                            Detail Video
                        </h3>

                        <div class="form-section-subtitle">
                            Gunakan video YouTube yang mengizinkan embed di website.
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
                            name="video_title"
                            class="form-control"
                            value="{{ $videoTitle }}"
                            placeholder="Contoh: Our Footage"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            YouTube Video ID
                        </label>

                        <input
                            type="text"
                            name="youtube_video_id"
                            class="form-control"
                            value="{{ $youtubeVideoId }}"
                            placeholder="Contoh: kfsqKYsxs0o"
                        >

                        <div class="form-help">
                            Jika kamu isi YouTube URL, Video ID boleh dikosongkan. Sistem akan mencoba mengambil ID otomatis.
                        </div>
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            YouTube URL
                        </label>

                        <input
                            type="url"
                            name="youtube_url"
                            class="form-control"
                            value="{{ $youtubeUrl }}"
                            placeholder="https://www.youtube.com/watch?v=..."
                        >

                        <div class="form-help">
                            Contoh format yang didukung: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., atau youtube.com/shorts/...
                        </div>
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Video
                        </label>

                        <textarea
                            name="video_description"
                            class="form-control"
                            rows="4"
                            placeholder="Tulis deskripsi singkat video"
                        >{{ $videoDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Video
                </button>
            </div>
        </form>
    </div>
</section>