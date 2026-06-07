<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan {{ $profile->groom_name }} & {{ $profile->bride_name }}</title>

    <link href="{{ asset('css/invitation.css') }}" rel="stylesheet">

    <style>
        .rsvp-card {
            margin-top: 24px;
            background: #ffffff;
            border: 1px solid #eee3d2;
            border-radius: 24px;
            padding: 24px;
            text-align: center;
        }

        .rsvp-title {
            color: #405244;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .rsvp-desc {
            color: #777;
            line-height: 1.7;
            margin-bottom: 18px;
        }

        .rsvp-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .rsvp-button {
            border: 0;
            border-radius: 14px;
            padding: 13px 18px;
            font-weight: 800;
            cursor: pointer;
        }

        .rsvp-attend-btn {
            background: #6f8f72;
            color: white;
        }

        .rsvp-not-btn {
            background: #f3f4f6;
            color: #405244;
        }

        .rsvp-success {
            margin-top: 16px;
            background: #dcfce7;
            color: #166534;
            border-radius: 14px;
            padding: 12px;
            font-weight: 700;
        }

        .guest-name-box {
            margin-bottom: 24px;
            background: #f9f6ef;
            border-radius: 20px;
            padding: 18px;
        }

        .guest-label {
            color: #777;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .guest-name {
            color: #405244;
            font-size: 22px;
            font-weight: 800;
        }

        .event-personal-card {
            background: #f9f6ef;
            border: 1px solid #eee3d2;
            border-radius: 24px;
            padding: 24px;
            text-align: center;
            margin-bottom: 22px;
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

        .rsvp-number-field {
            margin-bottom: 18px;
            text-align: left;
        }

        .rsvp-number-label {
            display: block;
            color: #777;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .rsvp-number-input {
            width: 100%;
            border: 1px solid #eee3d2;
            border-radius: 14px;
            padding: 13px 14px;
            font-size: 16px;
        }

        .rsvp-help {
            color: #777;
            font-size: 13px;
            margin-top: 8px;
            line-height: 1.6;
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

                <div class="guest-name-box">
                    <div class="guest-label">
                        Kepada Yth.
                    </div>
                    <div class="guest-name">
                        {{ $guest->name }}
                    </div>
                </div>

                @if ($profile->opening_text)
                    <p class="opening-text">
                        {{ $profile->opening_text }}
                    </p>
                @else
                    <p class="opening-text">
                        Dengan penuh rasa syukur, kami mengundang Bapak/Ibu/Saudara/i
                        untuk hadir dalam acara pernikahan kami.
                    </p>
                @endif
            </section>

            <section class="content-section">
                <h2 class="section-title">
                    Detail Acara
                </h2>

                @if ($event)
                    <div class="event-personal-card">
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
                    </div>
                @else
                    <div class="event-personal-card">
                        <div class="event-name">
                            Acara belum ditentukan
                        </div>

                        <p class="event-detail">
                            Admin belum menghubungkan tamu ini ke salah satu acara.
                        </p>
                    </div>
                @endif

                <div class="rsvp-card">
                    <h2 class="rsvp-title">
                        Konfirmasi Kehadiran
                    </h2>

                    <p class="rsvp-desc">
                        Mohon bantu kami dengan mengonfirmasi kehadiran Anda pada acara ini.
                    </p>

                    @if (session('success'))
                        <div class="rsvp-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('invitation.rsvp', [$profile->slug, $guest->invitation_code]) }}">
                        @csrf

                        <div class="rsvp-number-field">
                            <label class="rsvp-number-label">
                                Jika hadir, total orang yang akan datang
                            </label>

                            <input
                                type="number"
                                name="total_invited"
                                min="1"
                                max="10"
                                value="{{ old('total_invited', $guest->rsvp_status === 'attend' ? max(1, $guest->total_invited) : 1) }}"
                                class="rsvp-number-input"
                            >

                            <div class="rsvp-help">
                                Isi total orang termasuk Anda. Contoh: datang sendiri isi 1, membawa 1 pendamping isi 2.
                            </div>
                        </div>

                        <div class="rsvp-actions">
                            <button
                                type="submit"
                                name="rsvp_status"
                                value="attend"
                                class="rsvp-button rsvp-attend-btn"
                            >
                                Saya Akan Hadir
                            </button>

                            <button
                                type="submit"
                                name="rsvp_status"
                                value="not_attend"
                                class="rsvp-button rsvp-not-btn"
                            >
                                Maaf, Tidak Hadir
                            </button>
                        </div>
                    </form>
                </div>

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