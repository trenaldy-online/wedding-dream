<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan {{ $profile->groom_name }} & {{ $profile->bride_name }}</title>

    <link href="{{ asset('css/invitation.css') }}" rel="stylesheet">

    <style>
        .event-list {
            display: grid;
            gap: 18px;
            margin-top: 22px;
        }

        .event-card {
            background: #f9f6ef;
            border: 1px solid #eee3d2;
            border-radius: 24px;
            padding: 24px;
            text-align: center;
        }

        .event-side {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #c8a96a;
            border: 1px solid #eee3d2;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .event-name {
            color: #405244;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .event-detail {
            color: #777;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .event-venue {
            color: #405244;
            font-weight: 800;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        .empty-event {
            background: #fff6d7;
            border: 1px solid #eee3d2;
            border-radius: 20px;
            padding: 20px;
            color: #405244;
            text-align: center;
            line-height: 1.7;
        }
    </style>
</head>

<body>
    <main class="invitation-wrapper">
        <div class="invitation-card">

            <section class="hero-section">
                <div class="hero-label">
                    The Wedding Of
                </div>

                <h1 class="couple-name">
                    {{ $profile->groom_name }}
                    <br>
                    &
                    <br>
                    {{ $profile->bride_name }}
                </h1>

                <div class="divider"></div>

                @if ($profile->opening_text)
                    <p class="opening-text">
                        {{ $profile->opening_text }}
                    </p>
                @else
                    <p class="opening-text">
                        Dengan penuh rasa syukur, kami mengundang Bapak/Ibu/Saudara/i
                        untuk hadir dalam rangkaian acara pernikahan kami.
                    </p>
                @endif
            </section>

            <section class="content-section">
                <h2 class="section-title">
                    Rangkaian Acara
                </h2>

                @if ($events->count() > 0)
                    <div class="event-list">
                        @foreach ($events as $event)
                            <div class="event-card">
                                <div class="event-side">
                                    @if ($event->event_side === 'cpw')
                                        Pihak CPW
                                    @elseif ($event->event_side === 'cpp')
                                        Pihak CPP
                                    @else
                                        Acara Bersama
                                    @endif
                                </div>

                                <div class="event-name">
                                    {{ $event->event_name }}
                                </div>

                                @if ($event->event_date)
                                    <p class="event-detail">
                                        {{ $event->event_date->translatedFormat('l, d F Y') }}
                                        <br>
                                        Pukul {{ $event->event_date->translatedFormat('H:i') }} WIB
                                    </p>
                                @else
                                    <p class="event-detail">
                                        Tanggal dan waktu acara belum diatur.
                                    </p>
                                @endif

                                @if ($event->venue_name)
                                    <div class="event-venue">
                                        {{ $event->venue_name }}
                                    </div>
                                @endif

                                @if ($event->venue_address)
                                    <p class="event-detail">
                                        {{ $event->venue_address }}
                                    </p>
                                @endif

                                @if ($event->note)
                                    <p class="event-detail">
                                        {{ $event->note }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-event">
                        Rangkaian acara belum diatur oleh admin.
                    </div>
                @endif

                @if ($profile->story)
                    <div class="story-section">
                        <h2 class="section-title">
                            Cerita Kami
                        </h2>

                        <p class="story-text">
                            {{ $profile->story }}
                        </p>
                    </div>
                @endif
            </section>

            <section class="footer-section">
                <p>
                    Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila
                    Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.
                </p>
            </section>

        </div>
    </main>
</body>
</html>