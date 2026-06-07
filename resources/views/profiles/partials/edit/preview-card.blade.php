<aside class="invitation-preview-card invitation-template-preview-card">
        <div class="preview-top-label">
            ✨ Mobile Template Preview
        </div>

        <div class="template-preview-toolbar">
            <div>
                <div class="template-preview-title">
                    Anselma Preview
                </div>

                <div class="template-preview-subtitle">
                    Tampilan mobile asli dari template yang sedang diedit.
                </div>
            </div>

            <button
                type="button"
                class="template-preview-refresh"
                id="refreshTemplatePreview"
            >
                Refresh
            </button>
        </div>

        <div class="template-phone-frame">
            <div class="template-phone-speaker"></div>

            <iframe
                id="templatePreviewFrame"
                src="{{ route('templates.anselma.preview') }}?preview_t={{ now()->timestamp }}"
                class="template-mobile-iframe"
                loading="lazy"
            ></iframe>
        </div>

        <div class="template-preview-actions">
            <a href="{{ route('templates.anselma.preview') }}" target="_blank" class="btn-gold-inline">
                Buka Preview Anselma
            </a>
        </div>
    </aside>