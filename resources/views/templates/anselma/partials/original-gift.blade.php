@php
    $img = $img ?? fn ($file) => asset('templates/anselma/files/' . $file);

    $giftData = $giftData ?? [
        'title' => 'Wedding Gift',
        'description' => 'Your blessing and coming to our wedding are enough for us. However, if you want to give a gift we provide a Digital Envelope to make it easier for you. thank you',
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
    ];

    $physicalGift = $physicalGift ?? [
        'title' => 'Send us a gift',
        'description' => 'Silahkan kirimkan hadiah kepada kedua mempelai',
        'recipient' => 'Ansel',
        'phone' => '082365144995',
        'address' => 'Jalan Kenangan Raya',
    ];
@endphp

<div data-section-order="wedding_gift">
    <section class="wedding-gift-wrap">
        <div class="wedding-gift-inner">
            <div class="wedding-gift-head">
                <div class="orn-agenda-top">
                    <div
                        class="image-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1000"
                        data-aos-delay="600"
                    >
                        <img src="{{ $img('Orn-01.png') }}" alt="orn-cover">
                    </div>
                </div>

                <h1
                    class="wedding-gift-title"
                    data-aos="zoom-in"
                    data-aos-duration="1500"
                >
                    {{ $giftData['title'] }}
                </h1>

                <p
                    class="wedding-gift-description"
                    data-aos="fade-up"
                    data-aos-duration="1000"
                >
                    {{ $giftData['description'] }}
                </p>
            </div>

            <div class="wedding-gift-body-wrap">
                <div class="wedding-gift-body">
                    <div class="wedding-gift-body-inner">
                        <div class="wedding-gift-form">
                            <form action="#" method="POST" id="weddingGiftForm" enctype="multipart/form-data">
                                @csrf

                                <div class="wedding-gift-details wedding-gift__first-slide wedding-gift-slide">
                                    <div class="wedding-gift-bank-wrap">
                                        @foreach ($giftData['accounts'] as $account)
                                            <div class="accor-bank-wrap">
                                                <div
                                                    class="bank-btn-accordion bankBtnAccordion"
                                                    data-aos="fade-up"
                                                    data-aos-duration="1200"
                                                    data-aos-delay="500"
                                                >
                                                    <p class="bank-btop-txt">
                                                        {{ $account['bank'] }}
                                                    </p>

                                                    <span class="bank-accordion-icon" aria-hidden="true"></span>
                                                </div>

                                                <div class="gift-frame bankItemAccordion">
                                                    <div class="frame-wrap">
                                                        <div
                                                            class="image-wrap"
                                                            data-aos="fade-up"
                                                            data-aos-duration="1500"
                                                            data-aos-delay="400"
                                                        >
                                                            <img src="{{ $img('frame-bank.png') }}" alt="Frame Gift">
                                                        </div>

                                                        <div
                                                            class="wedding-gift-body-ins"
                                                            data-aos="fade-up"
                                                            data-aos-duration="1200"
                                                            data-aos-delay="500"
                                                        >
                                                            <div class="wedding-gift-bank-wrap no-scrollbar">
                                                                <div class="bank-item show">
                                                                    <div class="bank-detail">
                                                                        <h3 class="bank-name">
                                                                            {{ $account['bank'] }}
                                                                        </h3>

                                                                        <p class="bank-account-number-label">
                                                                            Account Number:
                                                                            <span class="bank-account-number">
                                                                                {{ $account['number'] }}
                                                                            </span>
                                                                        </p>

                                                                        <p class="bank-account-name-label">
                                                                            Account Name:
                                                                            <span class="bank-account-name">
                                                                                {{ $account['name'] }}
                                                                            </span>
                                                                        </p>

                                                                        <div
                                                                            class="bank-button-wrap"
                                                                            data-copy="{{ $account['number'] }}"
                                                                        >
                                                                            <i class="ph ph-copy-simple"></i>
                                                                            <p class="p">Copy</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="ornaments-wrapper bank-ornaments">
                                                        <div class="orn-bank-1">
                                                            <div class="orn-bank-1-1">
                                                                <div class="orn-bank-1-1-1">
                                                                    <div
                                                                        class="image-wrap"
                                                                        data-aos="fade-up"
                                                                        data-aos-duration="1400"
                                                                        data-aos-delay="700"
                                                                    >
                                                                        <img src="{{ $img('Orn-38.png') }}" alt="">
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="image-wrap"
                                                                    data-aos="fade-up"
                                                                    data-aos-duration="1200"
                                                                    data-aos-delay="600"
                                                                >
                                                                    <img src="{{ $img('Orn-14.png') }}" alt="">
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="image-wrap"
                                                                data-aos="zoom-in"
                                                                data-aos-duration="1000"
                                                                data-aos-delay="500"
                                                            >
                                                                <img src="{{ $img('Orn-37.png') }}" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-3">
                                                            <div
                                                                class="image-wrap"
                                                                data-aos="zoom-in"
                                                                data-aos-duration="1400"
                                                                data-aos-delay="700"
                                                            >
                                                                <img src="{{ $img('Orn-12.png') }}" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-2">
                                                            <div class="orn-bank-2-1">
                                                                <div
                                                                    class="image-wrap"
                                                                    data-aos="zoom-in"
                                                                    data-aos-duration="1200"
                                                                    data-aos-delay="600"
                                                                >
                                                                    <img src="{{ $img('Orn-40.png') }}" alt="">
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="image-wrap"
                                                                data-aos="zoom-in"
                                                                data-aos-duration="1000"
                                                                data-aos-delay="500"
                                                            >
                                                                <img src="{{ $img('Orn-39.png') }}" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-4 right">
                                                            <div
                                                                class="image-wrap"
                                                                data-aos="fade-up"
                                                                data-aos-duration="1500"
                                                                data-aos-delay="700"
                                                            >
                                                                <img src="{{ $img('Orn-30.png') }}" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-4 left">
                                                            <div
                                                                class="image-wrap"
                                                                data-aos="fade-up"
                                                                data-aos-duration="1500"
                                                                data-aos-delay="700"
                                                            >
                                                                <img src="{{ $img('Orn-30.png') }}" alt="">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-5 burung-1">
                                                            <div
                                                                class="image-wrap"
                                                                data-aos="zoom-in"
                                                                data-aos-duration="1200"
                                                                data-aos-delay="1000"
                                                            >
                                                                <img src="{{ $img('Orn-burung-1.png') }}" alt="Ornament">
                                                            </div>
                                                        </div>

                                                        <div class="orn-bank-6 burung-2">
                                                            <div
                                                                class="image-wrap"
                                                                data-aos="zoom-in"
                                                                data-aos-duration="1200"
                                                                data-aos-delay="1000"
                                                            >
                                                                <img src="{{ $img('Orn-burung-2.png') }}" alt="Ornament">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div
                                        class="slider-bank-wrap active"
                                        data-aos="fade-up"
                                        data-aos-duration="1200"
                                        data-aos-delay="500"
                                    >
                                        <p class="sld-bank">Confirmation Form</p>

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path
                                                fill-rule="evenodd"
                                                clip-rule="evenodd"
                                                d="M4.23431 10.1661C4.54673 10.4785 5.05327 10.4785 5.36569 10.1661L8 7.53176L10.6343 10.1661C10.9467 10.4785 11.4533 10.4785 11.7657 10.1661C12.0781 9.85366 12.0781 9.34712 11.7657 9.03471L8.56569 5.83471C8.25327 5.52229 7.74673 5.52229 7.43431 5.83471L4.23431 9.03471C3.9219 9.34712 3.9219 9.85366 4.23431 10.1661Z"
                                                fill="#8c0b06"
                                            />
                                        </svg>
                                    </div>

                                    <div
                                        class="gift-form-sender-wrapper is-open"
                                        data-aos="fade-up"
                                        data-aos-duration="1200"
                                        data-aos-delay="500"
                                    >
                                        <div class="wedding-gift-sender-data-wrap">
                                            <div class="form-group">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="name"
                                                    placeholder="Name"
                                                    value="{{ $guestName ?? 'Katsudoto' }}"
                                                >
                                            </div>

                                            <div class="form-group">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="account_name"
                                                    placeholder="Nama pemilik rekening"
                                                >
                                            </div>

                                            <div class="form-group">
                                                <textarea
                                                    name="message"
                                                    rows="1"
                                                    class="form-control"
                                                    placeholder="Pesan"
                                                ></textarea>
                                            </div>

                                            <div class="form-group">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="amount"
                                                    placeholder="Nominal"
                                                >
                                            </div>
                                        </div>

                                        <div style="display: none;">
                                            <input
                                                type="file"
                                                name="picture"
                                                id="weddingGiftPicture"
                                                data-wgu-preview="#weddingGiftPreview"
                                                style="display: none;"
                                            >
                                            <input type="hidden" name="post" value="sendGift">
                                        </div>

                                        <div class="wedding-gift-page-wrap">
                                            <button type="button" class="wedding-gift-page wedding-gift__next">
                                                Lanjutkan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="wedding-gift-picture wedding-gift-slide">
                                    <div class="wedding-gift-back-page-wrap">
                                        <button type="button" class="wedding-gift-back-page wedding-gift__prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                    </div>

                                    <div class="wedding-gift-select-bank-wrap">
                                        <select name="select_bank" id="selectBank" class="form-control">
                                            @foreach ($giftData['accounts'] as $account)
                                                <option value="{{ $account['id'] }}">
                                                    {{ $account['bank_short'] }} - {{ $account['number'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="wedding-gift-upload-wrap" data-wgu-file="#weddingGiftPicture">
                                        <div class="wgu-description show">
                                            <img class="wgu-icon" src="{{ $img('cloud-upload.png') }}" alt="">
                                            <p class="wgu-title">Upload proof of transfer</p>
                                            <p class="wgu-desc">Screen Shoot / Photo Slip Transfer</p>
                                        </div>

                                        <div class="wgu-img-wrap">
                                            <img class="wgu-img" id="weddingGiftPreview" src="" alt="">
                                        </div>
                                    </div>

                                    <div class="wedding-gift-page-wrap">
                                        <button type="submit" class="wedding-gift-page submit">
                                            Confirm
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="ornaments-wrapper"></div>
            </div>
        </div>
    </section>

    <section class="gift-section-wrap">
        <div class="ornaments-wrapper"></div>

        <div id="wedding-gifts" class="container wedding-gifts-wrap">
            <div class="wedding-gifts-inner">
                <div class="wedding-gifts-body">
                    <div class="gift-address-wrap">
                        <div class="ornaments-wrapper">
                            <div class="orn-qm-5">
                                <div
                                    class="image-wrap"
                                    data-aos="zoom-out"
                                    data-aos-duration="1400"
                                    data-aos-delay="1000"
                                >
                                    <img src="{{ $img('bg-sd.png') }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div
                            class="image-wrap frame-kado"
                            data-aos="zoom-in"
                            data-aos-duration="1300"
                            data-aos-delay="900"
                        >
                            <img src="{{ $img('frame-kado.png') }}" alt="orn-bank">
                        </div>

                        <div class="wedding-gift-address-wrap no-scrollbar">
                            <div
                                class="wedding-gifts-head"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="500"
                            >
                                <h1 class="wedding-gifts-title">
                                    {{ $physicalGift['title'] }}
                                </h1>

                                <p class="wedding-gifts-description">
                                    {{ $physicalGift['description'] }}
                                </p>
                            </div>

                            <div
                                class="kado-info-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="500"
                            >
                                <div class="wedding-gift-info-wrap">
                                    <span class="inner-address-info">
                                        {{ $physicalGift['recipient'] }}
                                    </span>
                                </div>

                                <div class="wedding-gift-info-wrap">
                                    <span class="inner-address-info sec">
                                        {{ $physicalGift['phone'] }}
                                    </span>
                                </div>

                                <div class="inner-address-wrap">
                                    <div class="wedding-gift-info-wrap">
                                        <span class="inner-address-info sec">
                                            {{ $physicalGift['address'] }}
                                        </span>
                                    </div>
                                </div>

                                <a
                                    href="#"
                                    data-toggle="modal"
                                    class="btn-hadiah-copy"
                                    data-copy="{{ $physicalGift['address'] }}"
                                >
                                    <i class="ph ph-copy-simple"></i>
                                    <p class="kado-copy-text">Copy Address</p>
                                </a>
                            </div>
                        </div>

                        <div class="ornaments-wrapper">
                            <div class="orn-qm-4 right">
                                <div class="orn-qm-4-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1500"
                                        data-aos-delay="1100"
                                    >
                                        <img src="{{ $img('Orn-14.png') }}" alt="">
                                    </div>
                                </div>
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1400"
                                    data-aos-delay="1000"
                                >
                                    <img src="{{ $img('Orn-24.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-4 left">
                                <div class="orn-qm-4-1">
                                    <div
                                        class="image-wrap"
                                        data-aos="fade-up"
                                        data-aos-duration="1500"
                                        data-aos-delay="1100"
                                    >
                                        <img src="{{ $img('Orn-14.png') }}" alt="">
                                    </div>
                                </div>
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1400"
                                    data-aos-delay="1000"
                                >
                                    <img src="{{ $img('Orn-24.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-3 right">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1400"
                                    data-aos-delay="900"
                                >
                                    <img src="{{ $img('Orn-55.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-3 left">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1400"
                                    data-aos-delay="900"
                                >
                                    <img src="{{ $img('Orn-55.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-2 right">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="700"
                                >
                                    <img src="{{ $img('Orn-10.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-2 left">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1200"
                                    data-aos-delay="700"
                                >
                                    <img src="{{ $img('Orn-09.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-1 right">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1000"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-54.png') }}" alt="">
                                </div>
                            </div>

                            <div class="orn-qm-1 left">
                                <div
                                    class="image-wrap"
                                    data-aos="fade-up"
                                    data-aos-duration="1000"
                                    data-aos-delay="500"
                                >
                                    <img src="{{ $img('Orn-54.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <p
                        id="toggleWrap"
                        class="wedding-gifts-label active wk-wrap"
                        data-aos="fade-up"
                        data-aos-duration="1200"
                        data-aos-delay="500"
                    >
                        Gift Recommendations <i class="ph ph-caret-up"></i>
                    </p>

                    <div class="hadiah-content">
                        <div class="hadiah-wrap">
                            <div
                                class="hadiah-card-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="500"
                            >
                                <div class="img-wrap">
                                    <img
                                        src="{{ $img('kado-lg-729991-2000-2000-1761644116-b454bdc45a2b4fc2763e0674.webp') }}"
                                        alt=""
                                        class="hadiah-img"
                                    >
                                </div>

                                <div class="hadiah-card">
                                    <div class="hadiah-card-inner">
                                        <div class="card-title-wrap">
                                            <p class="hadiah-card-title">Mirror</p>
                                        </div>

                                        <span class="hadiah-card-price">Rp 300.000</span>
                                        <span class="hadiah-card-amount">
                                            Total: <span class="total-amount">3</span>
                                        </span>
                                    </div>

                                    <div class="hadiah-card-footer">
                                        <button
                                            class="hadiah-card-button"
                                            type="button"
                                            data-img="{{ $img('kado-lg-729991-2000-2000-1761644116-b454bdc45a2b4fc2763e0674.webp') }}"
                                            data-name="Mirror"
                                            data-price="300000"
                                            data-amount="3"
                                            data-address="{{ $physicalGift['address'] }}"
                                            data-description="Long Mirror"
                                            data-web="#"
                                        >
                                            Gift Details
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="hadiah-card-wrap"
                                data-aos="fade-up"
                                data-aos-duration="1200"
                                data-aos-delay="500"
                            >
                                <div class="img-wrap">
                                    <img
                                        src="{{ $img('kado-lg-729977-2000-2000-1761644023-773dc038c111e31f1b2c0931.webp') }}"
                                        alt=""
                                        class="hadiah-img"
                                    >
                                </div>

                                <div class="hadiah-card">
                                    <div class="hadiah-card-inner">
                                        <div class="card-title-wrap">
                                            <p class="hadiah-card-title">Digital Rice Cooker</p>
                                        </div>

                                        <span class="hadiah-card-price">Rp 849.000</span>
                                        <span class="hadiah-card-amount">
                                            Total: <span class="total-amount">2</span>
                                        </span>
                                    </div>

                                    <div class="hadiah-card-footer">
                                        <button
                                            class="hadiah-card-button"
                                            type="button"
                                            data-img="{{ $img('kado-lg-729977-2000-2000-1761644023-773dc038c111e31f1b2c0931.webp') }}"
                                            data-name="Digital Rice Cooker"
                                            data-price="849000"
                                            data-amount="2"
                                            data-address="{{ $physicalGift['address'] }}"
                                            data-description="Digital Rice Cooker"
                                            data-web="#"
                                        >
                                            Gift Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="more-gift-wrap show" data-aos="fade-up" data-aos-duration="1200">
                            <button type="button" id="moreGifts" class="gifts-more-button">
                                Load more
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>