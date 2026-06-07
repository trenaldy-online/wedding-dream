(function () {
    function ready(fn) {
        if (document.readyState !== "loading") {
            fn();
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    ready(function () {
        const body = document.body;
        const loader = document.getElementById("anselmaLoader");
        const openingGate = document.getElementById("openingGate");
        const openButton = document.getElementById("openInvitation");
        const musicToggle = document.getElementById("musicToggle");
        const music = document.getElementById("weddingMusic");

        // Jangan biarkan loader menutup halaman terus
        if (loader) {
            setTimeout(function () {
                loader.classList.add("is-hidden");
            }, 500);
        }

        // Open invitation
        if (openButton) {
            openButton.addEventListener("click", function () {
                body.classList.remove("template-locked");

                if (openingGate) {
                    openingGate.classList.add("is-hidden");
                }

                if (music) {
                    music.play().catch(function () {
                        // Browser bisa block autoplay, aman diabaikan
                    });
                }

                if (musicToggle) {
                    musicToggle.classList.add("is-playing");
                }

                if (window.AOS) {
                    window.AOS.refresh();
                }
            });
        } else {
            // Kalau tombol tidak ada, jangan lock halaman
            body.classList.remove("template-locked");
        }

        // Music toggle
        if (musicToggle && music) {
            musicToggle.addEventListener("click", function () {
                if (music.paused) {
                    music.play().catch(function () {});
                    musicToggle.classList.add("is-playing");
                } else {
                    music.pause();
                    musicToggle.classList.remove("is-playing");
                }
            });
        }

        // Fallback kalau AOS belum auto jalan
        if (window.AOS) {
            window.AOS.init({
                offset: 10,
                duration: 400,
                easing: "ease",
                once: true,
                mirror: false
            });
        }
    });
})();