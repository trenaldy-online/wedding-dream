<section class="editor-section-box">
            <div class="editor-section-box-header">
                <div class="editor-section-eyebrow">
                    Section Editor
                </div>

                <h2 class="editor-section-title">
                    Pilih Bagian Undangan
                </h2>

                <p class="editor-section-desc">
                    Pilih section yang ingin diedit. Panel kanan hanya akan menampilkan form dari section yang sedang aktif.
                </p>
            </div>

            <div class="editor-section-box-body">
                <div class="editor-section-search">
                    <input
                        type="text"
                        id="editorSectionSearch"
                        class="form-control"
                        placeholder="Cari section, contoh: cover, couple, agenda, rsvp..."
                    >
                </div>

                <div class="editor-section-switcher" id="editorSectionSwitcher"></div>

                <div class="editor-section-settings is-hidden" id="editorSectionSettings">
                    <form method="POST" action="{{ route('profile.sections.update') }}">
                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="sections[0][id]"
                            id="editorSectionId"
                            value=""
                        >

                        <div class="editor-setting-head">
                            <div>
                                <h3 class="editor-setting-title" id="editorSectionSettingTitle">
                                    Pengaturan Section
                                </h3>

                                <p class="editor-setting-desc">
                                    Atur apakah section ini tampil di undangan dan posisi urutannya.
                                </p>
                            </div>
                        </div>

                        <div class="editor-setting-grid">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">
                                    Urutan
                                </label>

                                <input
                                    type="number"
                                    name="sections[0][sort_order]"
                                    id="editorSectionSortOrder"
                                    class="form-control section-order-input"
                                    min="1"
                                    max="999"
                                    value=""
                                >
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">
                                    Status Tampil
                                </label>

                                <label class="section-toggle" style="height: 48px;">
                                    <input
                                        type="checkbox"
                                        name="sections[0][is_active]"
                                        id="editorSectionActive"
                                        value="1"
                                    >

                                    <span class="section-toggle-slider"></span>

                                    <span class="section-toggle-text">
                                        Tampilkan section ini
                                    </span>
                                </label>
                            </div>

                            <button class="btn-gold-inline" type="submit">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>