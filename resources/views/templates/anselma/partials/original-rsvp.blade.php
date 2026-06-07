@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $guestName = $guestName ?? 'Katsudoto';
    $rsvpAction = $rsvpAction ?? '#';

    $rsvpStatus = $rsvpStatus ?? 'attend';
    $rsvpTotal = $rsvpTotal ?? 2;

    $rsvpData = array_replace_recursive([
        'title' => 'RSVP',
        'description' => 'Please confirm your attendance to help us prepare the best seat for you.',

        'status_question' => 'Apakah kamu datang?',
        'attend_text' => 'Hadir',
        'not_attend_text' => 'Tidak Hadir',

        'session_question' => 'Acara mana yang akan Anda hadiri?',
        'events' => [
            [
                'value' => 'akad',
                'label' => 'Akad Nikah',
            ],
            [
                'value' => 'resepsi',
                'label' => 'Resepsi',
            ],
        ],
        'all_events_text' => 'Hadir Semua',

        'total_question' => 'Jumlah tamu yang datang termasuk kamu?',
        'max_attendance' => 10,

        'submit_text' => 'Send RSVP',
        'change_text' => 'Change',

        'success_attend_title' => 'Will Attend',
        'success_attend_caption' => 'Yeay, Thank you for the attendance. See you there ;)',
        'success_not_attend_title' => 'Unable to Attend',
        'success_not_attend_caption' => 'Thank you for confirming. Your wishes mean a lot to us.',
    ], $rsvpData ?? []);

    $rsvpEvents = array_values($rsvpData['events'] ?? []);

    $maxAttendance = (int) ($rsvpData['max_attendance'] ?? 10);

    if ($maxAttendance < 1) {
        $maxAttendance = 1;
    }

    if ($maxAttendance > 20) {
        $maxAttendance = 20;
    }

    $rsvpTotal = min(max((int) $rsvpTotal, 1), $maxAttendance);
@endphp

<div data-section-order="rsvp">
    <section class="rsvp-wrap anselma-rsvp-original" id="toRsvp">

        <div class="orn-clip-mask bot">
            <div
                class="image-wrap"
                data-aos="fade-up"
                data-aos-duration="1200"
                data-aos-delay="500"
            >
                <img src="{{ $img('Orn-clip.png') }}" alt="Ornament">
            </div>
        </div>

        <div class="rsvp-inner">
            <div class="rsvp-body-wrapper">

                <div class="rsvp-head">
                    <div class="orn-rsvp-top">
                        <div
                            class="image-wrap"
                            data-aos="fade-up"
                            data-aos-duration="1000"
                            data-aos-delay="600"
                        >
                            <img src="{{ $img('Orn-rt.png') }}" alt="orn-cover">
                        </div>
                    </div>

                    <h1
                        class="rsvp-title"
                        data-aos="zoom-in"
                        data-aos-duration="1500"
                    >
                        {{ $rsvpData['title'] }}
                    </h1>
                </div>

                <div class="rsvp-body">

                    {{-- Ornament seperti form Katsudoto --}}
                    <div class="ornaments-wrapper rsvp-original-ornaments">
                        <div class="orn-rsvp-divid">
                            <div
                                class="image-wrap"
                                data-aos="zoom-in"
                                data-aos-duration="1200"
                                data-aos-delay="500"
                            >
                                <img src="{{ $img('Orn-cp.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-2 right">
                            <div class="orn-rsvp-2-1">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1500"
                                    data-aos-delay="1100"
                                >
                                    <img src="{{ $img('Orn-16.png') }}" alt="">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1400"
                                data-aos-delay="1000"
                            >
                                <img src="{{ $img('Orn-49.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-2 left">
                            <div class="orn-rsvp-2-1">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1500"
                                    data-aos-delay="1100"
                                >
                                    <img src="{{ $img('Orn-16.png') }}" alt="">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1400"
                                data-aos-delay="1000"
                            >
                                <img src="{{ $img('Orn-49.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-1 right">
                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1000"
                                data-aos-delay="500"
                            >
                                <img src="{{ $img('Orn-24.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-1 left">
                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1000"
                                data-aos-delay="500"
                            >
                                <img src="{{ $img('Orn-24.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-3 right">
                            <div class="orn-rsvp-3-1">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="700"
                                >
                                    <img src="{{ $img('Orn-55.png') }}" alt="">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="700"
                            >
                                <img src="{{ $img('Orn-54.png') }}" alt="">
                            </div>
                        </div>

                        <div class="orn-rsvp-3 left">
                            <div class="orn-rsvp-3-1">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="700"
                                >
                                    <img src="{{ $img('Orn-55.png') }}" alt="">
                                </div>
                            </div>

                            <div
                                class="image-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="700"
                            >
                                <img src="{{ $img('Orn-54.png') }}" alt="">
                            </div>
                        </div>
                    </div>

                    {{-- RSVP FORM --}}
                    <form
                        id="rsvpPreviewForm"
                        class="rsvp-form-wrapper"
                        method="POST"
                        action="{{ $rsvpAction }}"
                        data-max-attendance="{{ $maxAttendance }}"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                    >
                        @csrf

                        <div class="bg-rsvp"></div>

                        <div class="rsvp-form-content">

                            @if (!empty($rsvpData['description']))
                                <div class="rsvp-status-wrap">
                                    <div class="rsvp-status-head">
                                        <p class="rsvp-status-caption">
                                            {{ $rsvpData['description'] }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div class="rsvp-status-wrap">
                                <div class="rsvp-status-head">
                                    <p class="rsvp-status-caption">
                                        {{ $rsvpData['status_question'] }}
                                    </p>
                                </div>

                                <div class="rsvp-confirm-wrap rsvp-status-body">
                                    <label>
                                        <input
                                            type="radio"
                                            name="rsvp_status"
                                            value="attend"
                                            class="rsvp-option-input"
                                            {{ $rsvpStatus !== 'not_attend' ? 'checked' : '' }}
                                        >

                                        <span class="rsvp-confirm-btn going">
                                            {{ $rsvpData['attend_text'] }}
                                            <span class="check-rsvp">✓</span>
                                        </span>
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            name="rsvp_status"
                                            value="not_attend"
                                            class="rsvp-option-input"
                                            {{ $rsvpStatus === 'not_attend' ? 'checked' : '' }}
                                        >

                                        <span class="rsvp-confirm-btn not-going">
                                            {{ $rsvpData['not_attend_text'] }}
                                            <span class="check-rsvp">✓</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="rsvp-session-wrap" id="rsvpSessionField">
                                <div class="session-caption-wrap">
                                    <p class="caption">
                                        {{ $rsvpData['session_question'] }}
                                    </p>
                                </div>

                                <div class="rsvp-confirm-wrap session-btn-wrap">
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="selected_event[]"
                                            value="{{ $rsvpEvents[0]['value'] ?? 'akad' }}"
                                            class="rsvp-event-input"
                                            checked
                                        >

                                        <span class="rsvp-session-btn">
                                            {{ $rsvpEvents[0]['label'] ?? 'Akad Nikah' }}
                                            <span class="check-rsvp">✓</span>
                                        </span>
                                    </label>

                                    <label>
                                        <input
                                            type="checkbox"
                                            name="selected_event[]"
                                            value="{{ $rsvpEvents[1]['value'] ?? 'resepsi' }}"
                                            class="rsvp-event-input"
                                            checked
                                        >

                                        <span class="rsvp-session-btn">
                                            {{ $rsvpEvents[1]['label'] ?? 'Resepsi' }}
                                            <span class="check-rsvp">✓</span>
                                        </span>
                                    </label>

                                    <label>
                                        <input
                                            id="rsvpEventAll"
                                            type="checkbox"
                                            name="selected_event_all"
                                            value="all"
                                            checked
                                        >

                                        <span class="rsvp-session-btn all">
                                            {{ $rsvpData['all_events_text'] }}
                                            <span class="check-rsvp">✓</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="rsvp-amount-wrap" id="rsvpTotalField">
                                <div class="rsvp-amount-head">
                                    <p class="rsvp-amount-caption">
                                        {{ $rsvpData['total_question'] }}
                                    </p>
                                </div>

                                <div class="rsvp-amount-body">
                                    <div class="rsvp-amount-controller-wrap">
                                        <div class="rsvp-amount-controller">
                                            <button
                                                type="button"
                                                class="toggle-btn minus"
                                                data-preview-rsvp-minus
                                                aria-label="Decrease guest amount"
                                            >
                                                <span class="toggle-text">−</span>
                                            </button>

                                            <div class="input-wrap">
                                                <input
                                                    id="rsvpTotalGuests"
                                                    class="input-control"
                                                    type="number"
                                                    name="attendance_total"
                                                    min="1"
                                                    max="{{ $maxAttendance }}"
                                                    value="{{ $rsvpTotal }}"
                                                    readonly
                                                >
                                            </div>

                                            <button
                                                type="button"
                                                class="toggle-btn plus"
                                                data-preview-rsvp-plus
                                                aria-label="Increase guest amount"
                                            >
                                                <span class="toggle-text">+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rsvp-confirm-wrap">
                                <button
                                    type="submit"
                                    class="rsvp-confirm-btn confirm"
                                    id="submitRSVPPreview"
                                >
                                    {{ $rsvpData['submit_text'] }}
                                </button>
                            </div>

                        </div>
                    </form>

                    <div class="rsvp-result-wrapper" id="rsvpPreviewResult" style="display: none;">
                        <div class="rsvp-result-content">
                            <h4 class="rsvp-message-title">
                                {{ $rsvpStatus === 'not_attend'
                                    ? ($rsvpData['success_not_attend_title'] ?? 'Unable to Attend')
                                    : ($rsvpData['success_attend_title'] ?? 'Will Attend') }}
                            </h4>

                            <p class="rsvp-message-caption">
                                {!! nl2br(e(
                                    $rsvpStatus === 'not_attend'
                                        ? ($rsvpData['success_not_attend_caption'] ?? 'Thank you for confirming. Your wishes mean a lot to us.')
                                        : ($rsvpData['success_attend_caption'] ?? 'Yeay, Thank you for the attendance. See you there ;)')
                                )) !!}
                            </p>

                            <button
                                type="button"
                                class="rsvp-confirm-btn confirm"
                                id="changeRSVP"
                            >
                                {{ $rsvpData['change_text'] ?? 'Change' }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
</div>