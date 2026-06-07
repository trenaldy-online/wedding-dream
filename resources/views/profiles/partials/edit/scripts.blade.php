@php
    $editorSectionData = [];

    foreach ($sections as $section) {
        $editorSectionData[$section->section_key] = [
            'id' => (int) $section->id,
            'label' => $section->section_label,
            'sort_order' => (int) $section->sort_order,
            'is_active' => (bool) $section->is_active,
        ];
    }
@endphp

<script>
document.addEventListener("DOMContentLoaded", function () {
    const panelStack = document.getElementById("editorPanelStack");
    const switcher = document.getElementById("editorSectionSwitcher");
    const searchInput = document.getElementById("editorSectionSearch");

    const settingBox = document.getElementById("editorSectionSettings");
    const settingTitle = document.getElementById("editorSectionSettingTitle");
    const settingId = document.getElementById("editorSectionId");
    const settingSortOrder = document.getElementById("editorSectionSortOrder");
    const settingActive = document.getElementById("editorSectionActive");

    if (!panelStack || !switcher) {
        return;
    }

    const sectionData = @json($editorSectionData);

    function slugify(text) {
        return String(text || "")
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    function detectSectionKey(title) {
        const text = title.toLowerCase();

        if (text.includes("detail undangan")) {
            return "profile";
        }

        if (text.includes("pengaturan section")) {
            return "section_settings";
        }

        if (text.includes("live streaming")) {
            return "live_streaming";
        }

        if (text.includes("wedding gift")) {
            return "wedding_gift";
        }

        if (text.includes("wedding wish")) {
            return "wedding_wish";
        }

        if (text.includes("quote")) {
            return "quote";
        }

        if (text.includes("our story") || text.includes("story")) {
            return "story";
        }

        if (text.includes("gallery")) {
            return "gallery";
        }

        if (text.includes("video")) {
            return "video";
        }

        if (text.includes("save the date")) {
            return "save_the_date";
        }

        if (text.includes("agenda")) {
            return "agenda";
        }

        if (text.includes("dresscode") || text.includes("dress code")) {
            return "agenda";
        }

        if (text.includes("rsvp")) {
            return "rsvp";
        }

        if (text.includes("cover")) {
            return "cover";
        }

        if (text.includes("couple")) {
            return "couple";
        }

        if (text.includes("footer") || text.includes("closing")) {
            return "footer";
        }

        return "";
    }

    const allPanels = [...document.querySelectorAll(".invitation-form-panel")];

    allPanels.forEach(function (panel) {
        panelStack.appendChild(panel);
        panel.classList.remove("is-collapsed");
        panel.classList.remove("admin-panel-hidden");
        panel.classList.remove("is-active-editor-panel");
    });

    const rawPanels = [...panelStack.querySelectorAll(".invitation-form-panel")];

    let panelItems = rawPanels
        .map(function (panel, index) {
            const titleEl = panel.querySelector(".invitation-form-title");

            if (!titleEl) {
                return null;
            }

            const title = titleEl.textContent.trim();
            const key = detectSectionKey(title);

            panel.dataset.editorKey = key;
            panel.dataset.editorTitle = title.toLowerCase();

            if (key === "section_settings") {
                panel.remove();
                return null;
            }

            const id = "editor-panel-" + slugify(title || ("panel-" + index));

            panel.id = panel.id || id;

            panel.querySelectorAll('.invitation-submit-row a.btn-soft-inline[target="_blank"]').forEach(function (link) {
                link.remove();
            });

            panel.querySelectorAll('.invitation-submit-row a.btn-soft-inline[href*="anselma-preview"]').forEach(function (link) {
                link.remove();
            });

            panel.querySelectorAll('.invitation-submit-row a.btn-soft-inline[href*="/u/"]').forEach(function (link) {
                link.remove();
            });

            return {
                panel: panel,
                title: title,
                key: key,
                id: id,
                index: index,
            };
        })
        .filter(Boolean);

    panelItems = panelItems
    .filter(function (item) {
        return item.key !== "profile";
    })
    .sort(function (a, b) {
        const configA = sectionData[a.key];
        const configB = sectionData[b.key];

        const orderA = configA ? Number(configA.sort_order || 999) : 999;
        const orderB = configB ? Number(configB.sort_order || 999) : 999;

        return orderA - orderB;
    });

    function updateSectionSetting(item) {
        const config = sectionData[item.key];

        if (!config || item.key === "profile") {
            settingBox.classList.add("is-hidden");
            settingId.value = "";
            settingSortOrder.value = "";
            settingActive.checked = false;
            return;
        }

        settingBox.classList.remove("is-hidden");
        settingTitle.textContent = "Pengaturan Section: " + config.label;
        settingId.value = config.id;
        settingSortOrder.value = config.sort_order;
        settingActive.checked = !!config.is_active;
    }

    function setActiveButton(activeButton) {
        const buttons = [...switcher.querySelectorAll(".editor-section-button")];

        buttons.forEach(function (button) {
            button.classList.remove("is-active");
        });

        if (activeButton) {
            activeButton.classList.add("is-active");
        }
    }

    function activatePanel(item, activeButton, shouldScroll = true) {
        panelItems.forEach(function (panelItem) {
            panelItem.panel.classList.remove("is-active-editor-panel");
        });

        item.panel.classList.add("is-active-editor-panel");
        updateSectionSetting(item);
        setActiveButton(activeButton);

        localStorage.setItem("active_editor_section_key", item.key);

        if (shouldScroll) {
            item.panel.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    }

    switcher.innerHTML = "";

    panelItems.forEach(function (item, index) {
        const button = document.createElement("button");
        const config = sectionData[item.key];

        button.type = "button";
        button.className = "editor-section-button";
        button.dataset.editorTitle = item.title.toLowerCase();
        button.dataset.editorKey = item.key;

        const label = document.createElement("span");
        label.className = "editor-section-button-label";
        label.textContent = String(index + 1).padStart(2, "0") + " · " + item.title;

        button.appendChild(label);

        if (config && item.key !== "profile") {
            const badge = document.createElement("span");
            const isVisible = !!config.is_active;

            badge.className = "editor-section-button-badge " + (isVisible ? "is-visible" : "is-hidden-section");
            badge.textContent = isVisible ? "Tampil" : "Sembunyi";

            button.appendChild(badge);
        }

        button.addEventListener("click", function () {
            activatePanel(item, button);
        });

        switcher.appendChild(button);

        item.panel.querySelectorAll("form").forEach(function (form) {
            form.addEventListener("submit", function () {
                localStorage.setItem("active_editor_section_key", item.key);
                localStorage.setItem("should_refresh_template_preview", "1");
            });
        });
    });

    const settingForm = settingBox ? settingBox.querySelector("form") : null;

    if (settingForm) {
        settingForm.addEventListener("submit", function () {
            const activeButton = switcher.querySelector(".editor-section-button.is-active");

            if (activeButton) {
                localStorage.setItem("active_editor_section_key", activeButton.dataset.editorKey || "cover");
            }

            localStorage.setItem("should_refresh_template_preview", "1");
        });
    }

    const savedKey = localStorage.getItem("active_editor_section_key");
    const preferredKey = savedKey || "cover";

    let defaultItem = panelItems.find(function (item) {
        return item.key === preferredKey;
    });

    if (!defaultItem) {
        defaultItem = panelItems.find(function (item) {
            return item.key === "cover";
        });
    }

    if (!defaultItem) {
        defaultItem = panelItems[0];
    }

    if (defaultItem) {
        const defaultButton = switcher.querySelector(
            '.editor-section-button[data-editor-key="' + defaultItem.key + '"]'
        );

        activatePanel(defaultItem, defaultButton, false);
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const keyword = searchInput.value.trim().toLowerCase();
            const buttons = [...switcher.querySelectorAll(".editor-section-button")];

            buttons.forEach(function (button) {
                const title = button.dataset.editorTitle || "";
                button.style.display = keyword === "" || title.includes(keyword)
                    ? "inline-flex"
                    : "none";
            });
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const refreshButton = document.getElementById("refreshTemplatePreview");
    const previewFrame = document.getElementById("templatePreviewFrame");

    if (!previewFrame) {
        return;
    }

    function refreshTemplatePreview() {
        const baseUrl = "{{ route('templates.anselma.preview') }}";
        previewFrame.src = baseUrl + "?preview_t=" + Date.now();
    }

    if (refreshButton) {
        refreshButton.addEventListener("click", function () {
            refreshTemplatePreview();
        });
    }

    window.refreshTemplatePreview = refreshTemplatePreview;
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const shouldRefresh = localStorage.getItem("should_refresh_template_preview");

    if (shouldRefresh === "1") {
        localStorage.removeItem("should_refresh_template_preview");

        setTimeout(function () {
            if (typeof window.refreshTemplatePreview === "function") {
                window.refreshTemplatePreview();
            }
        }, 300);
    }
});
</script>