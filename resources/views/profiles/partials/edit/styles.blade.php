<style>
/* INVITATION EDIT PAGE */
/* Khusus tampilan halaman Undangan Digital */

.invitation-edit-grid {
    display: grid;
    grid-template-columns: 390px 1fr;
    gap: 34px;
    align-items: start;
}

.invitation-preview-card {
    background: linear-gradient(180deg, #fffdf7 0%, #fff6d7 100%);
    border: 1px solid var(--border);
    border-radius: 28px;
    padding: 28px;
    box-shadow: var(--shadow);
    position: sticky;
    top: 32px;
}

.preview-top-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: var(--gold-dark);
    border: 1px solid var(--border);
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 34px;
}

.preview-ornament {
    width: 82px;
    height: 82px;
    border-radius: 28px;
    background: white;
    border: 1px solid var(--border);
    display: grid;
    place-items: center;
    color: var(--gold);
    font-size: 38px;
    margin: 0 auto 26px;
    box-shadow: 0 12px 26px rgba(17, 24, 39, 0.06);
}

.preview-subtitle {
    text-align: center;
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.preview-couple-name {
    font-family: "Playfair Display", Georgia, serif;
    color: var(--navy);
    text-align: center;
    font-size: 42px;
    font-weight: 900;
    line-height: 1.05;
    margin-bottom: 24px;
}

.preview-divider {
    width: 72px;
    height: 3px;
    background: var(--gold);
    border-radius: 999px;
    margin: 0 auto 24px;
}

.preview-info-box {
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 18px;
    margin-bottom: 14px;
}

.preview-info-label {
    color: var(--muted-light);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.preview-info-value {
    color: var(--navy);
    font-weight: 800;
    line-height: 1.5;
}

.preview-info-muted {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
    margin-top: 4px;
}

.preview-link {
    display: block;
    text-align: center;
    text-decoration: none;
    background: var(--gold);
    color: white;
    border-radius: 14px;
    padding: 14px 18px;
    font-weight: 800;
    margin-top: 22px;
    box-shadow: 0 12px 24px rgba(216, 181, 50, 0.28);
}

.preview-link:hover {
    background: var(--gold-dark);
    color: white;
}

.invitation-form-panel {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 28px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.invitation-form-header {
    padding: 28px 32px;
    border-bottom: 1px solid var(--soft-border);
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: center;
}

.invitation-form-title {
    margin: 0;
    color: var(--navy);
    font-size: 24px;
    font-weight: 900;
}

.invitation-form-desc {
    margin: 6px 0 0;
    color: var(--muted);
    line-height: 1.6;
}

.invitation-form-body {
    padding: 32px;
}

@media (max-width: 1050px) {
    .invitation-edit-grid {
        grid-template-columns: 1fr;
    }

    .invitation-preview-card {
        position: relative;
        top: 0;
    }
}

@media (max-width: 700px) {
    .invitation-form-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* SECTION MANAGER */

.section-manager-list {
    display: grid;
    gap: 14px;
}

.section-manager-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    background: #fffdf7;
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 18px;
}

.section-manager-main {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.section-manager-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: var(--gold-soft);
    color: var(--gold-dark);
    display: grid;
    place-items: center;
    font-weight: 900;
    font-size: 13px;
    flex: 0 0 auto;
}

.section-manager-title {
    color: var(--navy);
    font-weight: 900;
    font-size: 16px;
    margin-bottom: 4px;
}

.section-manager-key {
    color: var(--muted-light);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.section-manager-controls {
    display: flex;
    align-items: center;
    gap: 18px;
    flex: 0 0 auto;
}

.section-order-field {
    width: 92px;
}

.section-mini-label {
    display: block;
    color: var(--muted-light);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.section-order-input {
    height: 42px;
    text-align: center;
    font-weight: 800;
}

.section-toggle {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.section-toggle input {
    display: none;
}

.section-toggle-slider {
    position: relative;
    width: 52px;
    height: 28px;
    border-radius: 999px;
    background: #e5e7eb;
    transition: background-color 0.2s ease;
}

.section-toggle-slider::before {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    left: 3px;
    top: 3px;
    border-radius: 999px;
    background: white;
    box-shadow: 0 4px 10px rgba(17, 24, 39, 0.18);
    transition: transform 0.2s ease;
}

.section-toggle input:checked + .section-toggle-slider {
    background: var(--gold);
}

.section-toggle input:checked + .section-toggle-slider::before {
    transform: translateX(24px);
}

.section-toggle-text {
    color: var(--navy);
    font-weight: 800;
    font-size: 13px;
}

@media (max-width: 760px) {
    .section-manager-item {
        align-items: flex-start;
        flex-direction: column;
    }

    .section-manager-controls {
        width: 100%;
        justify-content: space-between;
    }
}

.story-crop-preview-box {
    border: 1px solid #e6dccb;
    border-radius: 18px;
    padding: 10px;
    background: #fffaf3;
}

.story-crop-preview-frame {
    width: 100%;
    aspect-ratio: 3 / 4;
    border-radius: 14px;
    overflow: hidden;
    background: #f3eee5;
    position: relative;
}

.story-crop-preview-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}

.story-crop-guide {
    margin-top: 10px;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
}

.story-crop-guide strong {
    color: var(--gold-dark);
}

/* ADMIN INVITATION TOOLBAR */

html {
    scroll-behavior: smooth;
}

.admin-toolbar-link.gold {
    background: var(--gold);
    color: white;
    border-color: var(--gold);
}

.admin-panel-index-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    border-radius: 999px;
    background: #fffdf7;
    border: 1px solid var(--border);
    padding: 0 13px;
    text-decoration: none;
    color: var(--navy);
    font-size: 12px;
    font-weight: 800;
}

.admin-panel-index-link:hover {
    background: var(--gold-soft);
}

.invitation-form-panel {
    scroll-margin-top: 30px;
}

.invitation-form-panel.admin-panel-highlight {
    outline: 3px solid rgba(207, 160, 79, 0.28);
    outline-offset: 4px;
}

.admin-panel-collapse {
    border: 1px solid var(--border);
    background: #fffdf7;
    color: var(--navy);
    border-radius: 999px;
    height: 38px;
    padding: 0 14px;
    font-weight: 800;
    font-size: 12px;
    cursor: pointer;
    white-space: nowrap;
}

.admin-panel-collapse:hover {
    background: var(--gold-soft);
}

.invitation-form-panel.is-collapsed .invitation-form-body {
    display: none;
}

.invitation-form-panel.is-collapsed .admin-panel-collapse {
    background: var(--gold-soft);
    color: var(--gold-dark);
}

@media (max-width: 760px) {
    .admin-toolbar-head {
        flex-direction: column;
    }

    .admin-toolbar-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .admin-toolbar-link {
        flex: 1;
    }
}

.admin-toolbar-button:hover {
    background: var(--gold-soft);
}

.admin-panel-index-link.active {
    background: var(--gold);
    border-color: var(--gold);
    color: white;
    box-shadow: 0 10px 22px rgba(216, 181, 50, 0.25);
}

/* REAL TEMPLATE MOBILE PREVIEW */

.invitation-edit-grid {
    grid-template-columns: 440px 1fr;
}

.invitation-template-preview-card {
    padding: 22px;
}

.template-preview-toolbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}

.template-preview-title {
    color: var(--navy);
    font-weight: 900;
    font-size: 16px;
    margin-bottom: 4px;
}

.template-preview-subtitle {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.5;
}

.template-preview-refresh {
    border: 1px solid var(--border);
    background: white;
    color: var(--gold-dark);
    border-radius: 999px;
    height: 36px;
    padding: 0 14px;
    font-weight: 800;
    font-size: 12px;
    cursor: pointer;
    white-space: nowrap;
}

.template-preview-refresh:hover {
    background: var(--gold-soft);
}

.template-phone-frame {
    width: 390px;
    height: 760px;
    max-width: 100%;
    margin: 0 auto;
    background: #111827;
    border-radius: 38px;
    padding: 14px;
    box-shadow: 0 24px 55px rgba(17, 24, 39, 0.22);
    position: relative;
}

.template-phone-speaker {
    width: 72px;
    height: 5px;
    background: rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    margin: 0 auto 10px;
}

.template-mobile-iframe {
    width: 100%;
    height: calc(100% - 15px);
    border: 0;
    border-radius: 28px;
    background: white;
    display: block;
}

.template-preview-actions {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    margin-top: 18px;
}

.template-preview-actions .btn-soft-inline,
.template-preview-actions .btn-gold-inline {
    width: 100%;
    min-width: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

@media (max-width: 1180px) {
    .invitation-edit-grid {
        grid-template-columns: 1fr;
    }

    .template-phone-frame {
        width: 390px;
    }
}

@media (max-width: 520px) {
    .invitation-template-preview-card {
        padding: 16px;
    }

    .template-phone-frame {
        width: 100%;
        height: 720px;
        border-radius: 30px;
        padding: 10px;
    }

    .template-mobile-iframe {
        border-radius: 22px;
    }
}

/* ACTIVE SECTION EDITOR */

.invitation-edit-grid {
    grid-template-columns: 440px minmax(0, 1fr);
    gap: 34px;
    align-items: start;
}

.editor-right-column {
    min-width: 0;
}

.editor-section-box {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 28px;
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 24px;
}

.editor-section-box-header {
    padding: 26px 30px;
    border-bottom: 1px solid var(--soft-border);
}

.editor-section-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--gold-dark);
    background: #fffdf7;
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 7px 12px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.editor-section-title {
    margin: 0;
    color: var(--navy);
    font-size: 26px;
    font-weight: 900;
}

.editor-section-desc {
    margin: 8px 0 0;
    color: var(--muted);
    line-height: 1.6;
}

.editor-section-box-body {
    padding: 26px 30px 30px;
}

.editor-section-search {
    margin-bottom: 18px;
}

.editor-section-switcher {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 26px;
}

.editor-section-button {
    border: 1px solid var(--border);
    background: #fffdf7;
    color: var(--navy);
    border-radius: 999px;
    min-height: 42px;
    padding: 0 15px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
}

.editor-section-button:hover {
    background: var(--gold-soft);
}

.editor-section-button.is-active {
    background: var(--gold);
    border-color: var(--gold);
    color: white;
    box-shadow: 0 10px 22px rgba(216, 181, 50, 0.25);
}

.editor-section-settings {
    border: 1px solid var(--border);
    background: linear-gradient(180deg, #fffdf7 0%, #fffaf0 100%);
    border-radius: 22px;
    padding: 20px;
}

.editor-section-settings.is-hidden {
    display: none;
}

.editor-setting-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 18px;
}

.editor-setting-title {
    color: var(--navy);
    font-weight: 900;
    font-size: 18px;
    margin: 0 0 4px;
}

.editor-setting-desc {
    color: var(--muted);
    font-size: 13px;
    line-height: 1.6;
    margin: 0;
}

.editor-setting-grid {
    display: grid;
    grid-template-columns: 140px 1fr auto;
    gap: 16px;
    align-items: end;
}

.editor-panel-stack > .invitation-form-panel {
    display: none;
    margin-top: 0 !important;
}

.editor-panel-stack > .invitation-form-panel.is-active-editor-panel {
    display: block;
}

.editor-panel-stack .invitation-form-panel {
    scroll-margin-top: 24px;
}

.editor-panel-stack .admin-panel-collapse {
    display: none;
}

@media (max-width: 1180px) {
    .invitation-edit-grid {
        grid-template-columns: 1fr;
    }

    .invitation-preview-card {
        position: relative;
        top: 0;
    }
}

@media (max-width: 760px) {
    .editor-section-box-header,
    .editor-section-box-body {
        padding: 22px;
    }

    .editor-setting-grid {
        grid-template-columns: 1fr;
    }

    .editor-section-switcher {
        max-height: 230px;
        overflow-y: auto;
        padding-right: 4px;
    }
}

/* CLEAN ACTIVE EDITOR UX */

/* Sembunyikan tombol Preview Anselma di dalam form editor kanan */
.editor-panel-stack .invitation-submit-row a.btn-soft-inline[target="_blank"],
.editor-panel-stack .invitation-submit-row a.btn-soft-inline[href*="anselma-preview"],
.editor-panel-stack .invitation-submit-row a.btn-soft-inline[href*="/u/"] {
    display: none !important;
}

/* Supaya tombol simpan tetap rapi meskipun tombol preview dihapus */
.editor-panel-stack .invitation-submit-row {
    justify-content: flex-end;
}

/* Panel lama Pengaturan Section disembunyikan jika masih ada di DOM */
.editor-panel-stack .invitation-form-panel[data-editor-key="section_settings"] {
    display: none !important;
}

/* Section aktif lebih fokus */
.editor-panel-stack > .invitation-form-panel.is-active-editor-panel {
    display: block;
}

/* SECTION BUTTON STATUS BADGE */

.editor-section-button {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.editor-section-button-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.editor-section-button-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 22px;
    padding: 0 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .5px;
    text-transform: uppercase;
}

.editor-section-button-badge.is-visible {
    background: rgba(34, 197, 94, 0.12);
    color: #15803d;
}

.editor-section-button-badge.is-hidden-section {
    background: rgba(148, 163, 184, 0.16);
    color: #64748b;
}

.editor-section-button.is-active .editor-section-button-badge.is-visible,
.editor-section-button.is-active .editor-section-button-badge.is-hidden-section {
    background: rgba(255, 255, 255, 0.22);
    color: white;
}
</style>