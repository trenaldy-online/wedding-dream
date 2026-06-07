@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $saveDateData = array_replace_recursive([
        'title' => 'Save the date',
        'button_text' => 'Add to Calendar',
        'calendar_title' => trim(($couple['groom'] ?? 'Groom') . ' & ' . ($couple['bride'] ?? 'Bride') . ' Wedding'),
        'calendar_details' => 'You are invited to our wedding ceremony.',
        'calendar_duration_minutes' => 180,
    ], $saveDateData ?? []);

    $eventDate = $profile?->event_date;

    if (! $eventDate && !empty($events[0]['date_iso'])) {
        try {
            $eventDate = \Carbon\Carbon::parse($events[0]['date_iso']);
        } catch (\Throwable $e) {
            $eventDate = null;
        }
    }

    $eventDate = $eventDate ?: now()->addMonths(3);

    $durationMinutes = (int) ($saveDateData['calendar_duration_minutes'] ?? 180);
    $calendarStart = $eventDate->copy();
    $calendarEnd = $eventDate->copy()->addMinutes($durationMinutes);

    $calendarDates = $calendarStart->format('Ymd\THis') . '/' . $calendarEnd->format('Ymd\THis');

    $calendarLocation = trim(($couple['venue'] ?? '') . ' | ' . ($couple['address'] ?? ''));

    $calendarUrl = 'https://www.google.com/calendar/render?' . http_build_query([
        'action' => 'TEMPLATE',
        'text' => $saveDateData['calendar_title'],
        'dates' => $calendarDates,
        'location' => $calendarLocation,
        'details' => $saveDateData['calendar_details'],
    ]);

    $targetIso = $eventDate->toIso8601String();
@endphp

<section
    class="save-date-wrap"
    data-section-order="save_the_date"
    data-countdown-target="{{ $targetIso }}"
>
    <div class="ornaments-wrapper">
        <div class="orn-sd-bg">
            <div
                class="image-wrap"
                data-aos="zoom-out"
                data-aos-duration="1200"
                data-aos-delay="500"
            >
                <img src="{{ $img('bg-sd.png') }}" alt="">
            </div>
        </div>
    </div>

    <div class="save-date-head">
        <h1
            class="save-date-title"
            data-aos="zoom-in"
            data-aos-duration="1000"
        >
            {{ $saveDateData['title'] }}
        </h1>
    </div>

    <div class="save-date-frame">
        <div class="ornaments-wrapper">
            <div class="orn-sd-5">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-36.png') }}" alt="">
                </div>
            </div>
        </div>

        <div
            class="image-wrap"
            data-aos="zoom-in"
            data-aos-duration="1000"
        >
            <img src="{{ $img('frame-sd.png') }}" alt="">
        </div>

        <div class="ornaments-wrapper">
            <div class="orn-sd-4">
                <div class="orn-sd-4-1">
                    <div
                        class="image-wrap"
                        data-aos="zoom-in"
                        data-aos-duration="1200"
                        data-aos-delay="500"
                    >
                        <img src="{{ $img('Orn-35.png') }}" alt="">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-34.png') }}" alt="">
                </div>
            </div>

            <div class="orn-sd-3">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-14.png') }}" alt="">
                </div>
            </div>

            <div class="orn-sd-2">
                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-33.png') }}" alt="">
                </div>
            </div>

            <div class="orn-sd-1">
                <div class="orn-sd-1-1">
                    <div
                        class="image-wrap"
                        data-aos="zoom-in"
                        data-aos-duration="1200"
                        data-aos-delay="500"
                    >
                        <img src="{{ $img('Orn-11.png') }}" alt="">
                    </div>
                </div>

                <div
                    class="image-wrap"
                    data-aos="zoom-in"
                    data-aos-duration="1200"
                    data-aos-delay="500"
                >
                    <img src="{{ $img('Orn-32.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="save-date">
            <div class="save-date-body">
                <div class="countdown">
                    <div
                        class="count-item"
                        data-aos="fade-down-right"
                        data-aos-duration="1200"
                        data-aos-delay="100"
                    >
                        <h2 class="count-num count-day">0</h2>
                        <small class="count-text">Days</small>
                    </div>

                    <div
                        class="count-item"
                        data-aos="fade-down-left"
                        data-aos-duration="1200"
                        data-aos-delay="300"
                    >
                        <h2 class="count-num count-hour">0</h2>
                        <small class="count-text">Hours</small>
                    </div>

                    <div
                        class="count-item"
                        data-aos="fade-up-right"
                        data-aos-duration="1200"
                        data-aos-delay="500"
                    >
                        <h2 class="count-num count-minute">0</h2>
                        <small class="count-text">Minutes</small>
                    </div>

                    <div
                        class="count-item"
                        data-aos="fade-up-left"
                        data-aos-duration="1200"
                        data-aos-delay="700"
                    >
                        <h2 class="count-num count-second">0</h2>
                        <small class="count-text">Seconds</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="add-to-calendar-wrap"
        data-aos="fade-up"
        data-aos-duration="1000"
        data-aos-delay="1100"
    >
        <a
            class="add-to-calendar"
            href="{{ $calendarUrl }}"
            target="_blank"
            rel="nofollow noopener"
            id="addToCalendar"
        >
            {{ $saveDateData['button_text'] }}
        </a>
    </div>
</section>

<script>
(function () {
    function initSaveDateCountdown() {
        document.querySelectorAll(".save-date-wrap[data-countdown-target]").forEach(function (section) {
            const targetRaw = section.getAttribute("data-countdown-target");

            if (!targetRaw) {
                return;
            }

            const targetTime = new Date(targetRaw).getTime();

            if (Number.isNaN(targetTime)) {
                return;
            }

            const dayEl = section.querySelector(".count-day");
            const hourEl = section.querySelector(".count-hour");
            const minuteEl = section.querySelector(".count-minute");
            const secondEl = section.querySelector(".count-second");

            function renderCountdown() {
                const now = Date.now();
                const distance = Math.max(0, targetTime - now);

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((distance / (1000 * 60)) % 60);
                const seconds = Math.floor((distance / 1000) % 60);

                if (dayEl) dayEl.textContent = days;
                if (hourEl) hourEl.textContent = hours;
                if (minuteEl) minuteEl.textContent = minutes;
                if (secondEl) secondEl.textContent = seconds;
            }

            renderCountdown();
            setInterval(renderCountdown, 1000);
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSaveDateCountdown);
    } else {
        initSaveDateCountdown();
    }
})();
</script>