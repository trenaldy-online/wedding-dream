@php
    $weddingGiftSection = $weddingGiftSection ?? null;
    $weddingGiftContent = $weddingGiftSection ? ($weddingGiftSection->content ?? []) : [];

    $weddingGiftTitle = old('title', $weddingGiftContent['title'] ?? 'Wedding Gift');
    $weddingGiftDescription = old('description', $weddingGiftContent['description'] ?? '');

    $giftAccounts = old('accounts', $weddingGiftContent['accounts'] ?? []);

    if (empty($giftAccounts)) {
        $giftAccounts = [
            [
                'id' => 'bri',
                'bank' => 'BANK BRI (002)',
                'bank_short' => 'BANK BRI',
                'number' => '',
                'name' => '',
            ],
            [
                'id' => 'mandiri',
                'bank' => 'BANK MANDIRI (008)',
                'bank_short' => 'BANK MANDIRI',
                'number' => '',
                'name' => '',
            ],
        ];
    }

    $giftAccounts = array_values($giftAccounts);

    $physicalGift = old('physical_gift', $weddingGiftContent['physical_gift'] ?? []);

    $physicalGiftTitle = $physicalGift['title'] ?? 'Send us a gift';
    $physicalGiftDescription = $physicalGift['description'] ?? 'Silahkan kirimkan hadiah kepada kedua mempelai';
    $physicalGiftRecipient = $physicalGift['recipient'] ?? '';
    $physicalGiftPhone = $physicalGift['phone'] ?? '';
    $physicalGiftAddress = $physicalGift['address'] ?? '';
@endphp

<section class="invitation-form-panel" style="margin-top: 34px;">
    <div class="invitation-form-header">
        <div>
            <h2 class="invitation-form-title">
                Konten Wedding Gift
            </h2>

            <p class="invitation-form-desc">
                Atur rekening, deskripsi amplop digital, dan alamat pengiriman hadiah fisik pada template Anselma.
            </p>
        </div>
    </div>

    <div class="invitation-form-body">
        <form method="POST" action="{{ route('profile.sections.wedding-gift.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">WG</div>

                    <div>
                        <h3 class="form-section-title">
                            Informasi Wedding Gift
                        </h3>

                        <div class="form-section-subtitle">
                            Data ini akan tampil pada section Wedding Gift Anselma.
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
                            value="{{ $weddingGiftTitle }}"
                            placeholder="Contoh: Wedding Gift"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Catatan
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="Data rekening akan mengikuti isian di bawah."
                            disabled
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi Wedding Gift
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            placeholder="Tulis deskripsi untuk amplop digital"
                        >{{ $weddingGiftDescription }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">01</div>

                    <div>
                        <h3 class="form-section-title">
                            Rekening Bank
                        </h3>

                        <div class="form-section-subtitle">
                            Isi rekening yang akan muncul di tombol accordion bank.
                        </div>
                    </div>
                </div>

                @foreach ($giftAccounts as $index => $account)
                    <div class="section-manager-item" style="align-items: flex-start; margin-bottom: 16px;">
                        <div class="section-manager-main">
                            <div class="section-manager-icon">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div>
                                <div class="section-manager-title">
                                    Rekening {{ $index + 1 }}
                                </div>

                                <div class="section-manager-key">
                                    bank_account_{{ $index + 1 }}
                                </div>
                            </div>
                        </div>

                        <div style="flex: 1; width: 100%;">
                            <input
                                type="hidden"
                                name="accounts[{{ $index }}][id]"
                                value="{{ $account['id'] ?? 'bank_' . ($index + 1) }}"
                            >

                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Bank Lengkap
                                    </label>

                                    <input
                                        type="text"
                                        name="accounts[{{ $index }}][bank]"
                                        class="form-control"
                                        value="{{ $account['bank'] ?? '' }}"
                                        placeholder="Contoh: BANK BRI (002)"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Bank Singkat
                                    </label>

                                    <input
                                        type="text"
                                        name="accounts[{{ $index }}][bank_short]"
                                        class="form-control"
                                        value="{{ $account['bank_short'] ?? '' }}"
                                        placeholder="Contoh: BANK BRI"
                                    >
                                </div>
                            </div>

                            <div class="form-grid-2" style="margin-top: 18px;">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nomor Rekening
                                    </label>

                                    <input
                                        type="text"
                                        name="accounts[{{ $index }}][number]"
                                        class="form-control"
                                        value="{{ $account['number'] ?? '' }}"
                                        placeholder="Contoh: 02122333214"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Pemilik Rekening
                                    </label>

                                    <input
                                        type="text"
                                        name="accounts[{{ $index }}][name]"
                                        class="form-control"
                                        value="{{ $account['name'] ?? '' }}"
                                        placeholder="Contoh: Varo"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="form-help">
                    Untuk tahap ini jumlah rekening mengikuti data default yang sudah ada. Nanti bisa kita buat tombol tambah/hapus rekening secara dinamis.
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-heading">
                    <div class="form-section-icon">02</div>

                    <div>
                        <h3 class="form-section-title">
                            Alamat Hadiah Fisik
                        </h3>

                        <div class="form-section-subtitle">
                            Data ini tampil pada bagian “Send us a gift”.
                        </div>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">
                            Judul
                        </label>

                        <input
                            type="text"
                            name="physical_gift[title]"
                            class="form-control"
                            value="{{ $physicalGiftTitle }}"
                            placeholder="Contoh: Send us a gift"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Penerima
                        </label>

                        <input
                            type="text"
                            name="physical_gift[recipient]"
                            class="form-control"
                            value="{{ $physicalGiftRecipient }}"
                            placeholder="Contoh: Ansel"
                        >
                    </div>
                </div>

                <div class="form-grid-2" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            name="physical_gift[phone]"
                            class="form-control"
                            value="{{ $physicalGiftPhone }}"
                            placeholder="Contoh: 082365144995"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Deskripsi
                        </label>

                        <input
                            type="text"
                            name="physical_gift[description]"
                            class="form-control"
                            value="{{ $physicalGiftDescription }}"
                            placeholder="Contoh: Silahkan kirimkan hadiah kepada kedua mempelai"
                        >
                    </div>
                </div>

                <div class="form-grid-1" style="margin-top: 18px;">
                    <div class="form-group">
                        <label class="form-label">
                            Alamat Lengkap
                        </label>

                        <textarea
                            name="physical_gift[address]"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan alamat lengkap pengiriman hadiah"
                        >{{ $physicalGiftAddress }}</textarea>
                    </div>
                </div>
            </div>

            <div class="invitation-submit-row">
                <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-soft-inline">
                    Preview Anselma
                </a>

                <button class="btn-gold-inline" type="submit">
                    Simpan Wedding Gift
                </button>
            </div>
        </form>
    </div>
</section>