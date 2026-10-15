
/* =========================
   MOBILE NAVIGATION
========================= */

const menuToggle = document.getElementById("menuToggle");
const navMenu = document.getElementById("navMenu");
const navLinks = document.querySelectorAll("#navLinks a");

menuToggle.addEventListener("click", () => {
    const isOpen = navMenu.classList.toggle("show");

    menuToggle.setAttribute("aria-expanded", String(isOpen));
});

navLinks.forEach(link => {
    link.addEventListener("click", () => {
        navMenu.classList.remove("show");
        menuToggle.setAttribute("aria-expanded", "false");
    });
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 768) {
        navMenu.classList.remove("show");
        menuToggle.setAttribute("aria-expanded", "false");
    }
});

/* =========================
   HERO IMAGE SLIDESHOW
========================= */

const slides = document.querySelectorAll(".hero-slide");
const dots = document.querySelectorAll(".slide-dot");
const prevButton = document.getElementById("prevSlide");
const nextButton = document.getElementById("nextSlide");

let currentSlide = 0;
let slideshowTimer;

const SLIDE_INTERVAL = 3000;

function showSlide(index) {
    // Wrap around when reaching either end
    currentSlide = (index + slides.length) % slides.length;

    slides.forEach((slide, i) => {
        slide.classList.toggle("active", i === currentSlide);
    });

    dots.forEach((dot, i) => {
        dot.classList.toggle("active", i === currentSlide);

        if (i === currentSlide) {
            dot.setAttribute("aria-current", "true");
        } else {
            dot.removeAttribute("aria-current");
        }
    });
}

function nextSlide() {
    showSlide(currentSlide + 1);
}

function previousSlide() {
    showSlide(currentSlide - 1);
}

function startSlideshow() {
    clearInterval(slideshowTimer);
    slideshowTimer = setInterval(nextSlide, SLIDE_INTERVAL);
}

function changeSlide(index) {
    showSlide(index);
    startSlideshow();
}

nextButton.addEventListener("click", () => {
    changeSlide(currentSlide + 1);
});

prevButton.addEventListener("click", () => {
    changeSlide(currentSlide - 1);
});

dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
        changeSlide(index);
    });
});

// Start the automatic slideshow
showSlide(0);
startSlideshow();

// Pause automatic changes when the browser tab is hidden
document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
        clearInterval(slideshowTimer);
    } else {
        startSlideshow();
    }
});

/* =========================
   PLACEHOLDER SEARCH
========================= */

const searchForm = document.querySelector(".search-form");

searchForm.addEventListener("submit", event => {
    event.preventDefault();

    const searchInput = searchForm.querySelector("input");
    const searchTerm = searchInput.value.trim();

    if (searchTerm) {
        alert("Search functionality is not implemented yet.");
    }
});

/* =========================
   AUTOMATIC FOOTER YEAR
========================= */

document.getElementById("currentYear").textContent =
    new Date().getFullYear();
