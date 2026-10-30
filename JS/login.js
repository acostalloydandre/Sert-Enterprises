/* Login page: project showcase carousel + cross-page transition */
(() => {
    const slides = Array.from(document.querySelectorAll(".showcase-slide"));
    const dots = Array.from(document.querySelectorAll(".showcase-dot"));
    if (!slides.length) return;

    let current = 0;
    let intervalId;

    function showSlide(index) {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === current);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle("active", i === current);
            if (i === current) {
                dot.setAttribute("aria-current", "true");
            } else {
                dot.removeAttribute("aria-current");
            }
        });
    }

    function startCarousel() {
        stopCarousel();
        if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
            intervalId = window.setInterval(() => showSlide(current + 1), 2500);
        }
    }

    function stopCarousel() {
        if (intervalId) window.clearInterval(intervalId);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            showSlide(index);
            startCarousel();
        });
    });

    const gallery = document.querySelector(".login-gallery");
    if (gallery) {
        gallery.addEventListener("mouseenter", stopCarousel);
        gallery.addEventListener("mouseleave", startCarousel);
    }

    showSlide(0);
    startCarousel();
})();

/* Reuse the transition handler on login page */
document.addEventListener("click", (event) => {
    const link = event.target.closest("a[data-transition-to]");
    if (!link || event.defaultPrevented || event.button !== 0 ||
        event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) return;

    try {
        document.documentElement.dataset.transitionDirection = link.dataset.transitionTo;
        sessionStorage.setItem("sert-auth-transition", link.dataset.transitionTo);
    } catch (_) {}
});
