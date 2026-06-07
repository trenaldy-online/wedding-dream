@php
    $wishData = array_replace_recursive([
        'title' => 'Wedding Wish',
        'description' => '',
        'guest_name' => $guestName ?? 'Katsudoto',
        'placeholder' => 'Give your wish',
        'button_text' => 'Send',
        'show_more' => false,
        'wishes' => [
            [
                'name' => 'Katsudoto',
                'date' => '17 Jan 2026, 11:00',
                'message' => 'May your marriage be filled with love, joy, and endless blessings.',
                'verified' => true,
            ],
        ],
    ], $wishData ?? []);

    $wishes = $wishData['wishes'] ?? [];

    if (! is_array($wishes)) {
        $wishes = [];
    }
@endphp

<section class="wedding-wish-wrap">
    <div class="wedding-wish-inner">

        <div class="wedding-wish-head">
            <h1
                class="wedding-wish-title"
                data-aos="fade-up"
                data-aos-duration="1200"
            >
                {{ $wishData['title'] }}
            </h1>

            @if (!empty($wishData['description']))
                <p
                    class="wedding-wish-description"
                    data-aos="fade-up"
                    data-aos-duration="1200"
                    data-aos-delay="100"
                >
                    {{ $wishData['description'] }}
                </p>
            @endif
        </div>

        <div class="wedding-wish-body">
            <div class="wedding-wish-form">
                <form action="#" method="POST" id="weddingWishForm">
                    @csrf

                    <div
                        class="form-group guest-name-wrap hide"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="200"
                    >
                        <input
                            type="text"
                            name="name"
                            class="form-control guest-name"
                            placeholder="Name"
                            value="{{ $wishData['guest_name'] }}"
                        >
                    </div>

                    <div
                        class="form-group guest-comment-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="300"
                    >
                        <textarea
                            class="form-control guest-comment"
                            name="comment"
                            rows="1"
                            placeholder="{{ $wishData['placeholder'] }}"
                        ></textarea>
                    </div>

                    <div
                        class="submit-comment-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="400"
                    >
                        <button
                            type="submit"
                            class="submit submit-comment"
                            data-last=""
                        >
                            {{ $wishData['button_text'] }}
                        </button>
                    </div>
                </form>
            </div>

            <div
                class="comment-wrap {{ count($wishes) > 0 ? 'show' : '' }}"
                id="wishCommentWrap"
            >
                @forelse ($wishes as $index => $wish)
                    <div
                        class="comment-item"
                        id="comment{{ $index }}"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                    >
                        <div class="comment-head">
                            <div class="ch-name-wrap">
                                <h3 class="comment-name">
                                    {{ $wish['name'] ?? 'Guest' }}

                                    @if (!empty($wish['verified']))
                                        <i class="fas fa-check"></i>
                                    @endif
                                </h3>
                            </div>

                            <small class="comment-date">
                                {{ $wish['date'] ?? '' }}
                            </small>
                        </div>

                        <div class="comment-body">
                            <p class="comment-caption">
                                {{ $wish['message'] ?? '' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="comment-item"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                    >
                        <div class="comment-head">
                            <div class="ch-name-wrap">
                                <h3 class="comment-name">
                                    Belum ada ucapan
                                </h3>
                            </div>
                        </div>

                        <div class="comment-body">
                            <p class="comment-caption">
                                Jadilah yang pertama memberikan ucapan.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if (!empty($wishData['show_more']))
                <div
                    class="more-comment-wrap show"
                    data-aos="fade-up"
                    data-aos-duration="1200"
                >
                    <button
                        type="button"
                        id="moreComment"
                        data-template=""
                        data-start="{{ count($wishes) }}"
                        data-load-text="Loading"
                    >
                        Show more comments
                    </button>
                </div>
            @endif
        </div>
    </div>
</section>