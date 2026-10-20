
    const menuToggle = document.getElementById("menuToggle");
    const navMenu = document.getElementById("navMenu");
    const navLinks = document.querySelectorAll("#navLinks a");

    menuToggle.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("show");

        menuToggle.setAttribute(
            "aria-expanded",
            String(isOpen)
        );
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

    document.querySelector(".search-form").addEventListener("submit", (e) => { e.preventDefault(); }); /*remove after building real search function*/

    const track = document.getElementById('logoTrack');
    Array.from(track.children).forEach(item => {
        const clone = item.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.querySelector('img').alt = '';
        track.appendChild(clone);
    });

    lucide.createIcons();

    (function () {
        var track = document.querySelector('.fw-track');
        if (!track) return;
        document.querySelectorAll('.fw-arrow').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var card = track.querySelector('.fw-card');
                var step = card ? card.offsetWidth + 20 : 400;
                track.scrollBy({ left: step * Number(btn.dataset.dir), behavior: 'smooth' });
            });
        });
    })();

      // Featured Work: centered, looping carousel
  (function () {
    var carousel = document.querySelector('.fw-carousel');
    if (!carousel) return;

    var viewport  = carousel.querySelector('.fw-viewport');
    var track     = carousel.querySelector('.fw-track');
    var dotsWrap  = carousel.querySelector('.fw-dots');
    var originals = Array.prototype.slice.call(track.children);
    var n         = originals.length;
    var reduce    = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Build three sets of cards (clones before and after) so it can loop forever
    function makeClones() {
      return originals.map(function (card) {
        var clone = card.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.querySelectorAll('a').forEach(function (a) { a.setAttribute('tabindex', '-1'); });
        return clone;
      });
    }
    makeClones().forEach(function (c) { track.insertBefore(c, originals[0]); });
    makeClones().forEach(function (c) { track.appendChild(c); });

    var cards     = Array.prototype.slice.call(track.children);
    var index     = n;          // start on the first real card, centered
    var animating = false;
    var timer;

    // Dots
    var dots = originals.map(function (_, i) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'fw-dot';
      dot.setAttribute('aria-label', 'Go to project ' + (i + 1));
      dot.addEventListener('click', function () { go(n + i); });
      dotsWrap.appendChild(dot);
      return dot;
    });

    function updateState() {
      cards.forEach(function (card, i) { card.classList.toggle('is-active', i === index); });
      dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === index % n); });
    }

    // Slide the track so the card at "index" sits in the middle of the viewport
    function position(animate) {
      var card = cards[index];
      var x = viewport.clientWidth / 2 - (card.offsetLeft + card.offsetWidth / 2);
      track.style.transition = (animate && !reduce) ? '' : 'none';
      track.style.transform = 'translate3d(' + x + 'px, 0, 0)';
    }

    function go(target) {
      if (animating) return;
      animating = true;
      index = target;
      updateState();
      position(true);
      clearTimeout(timer);
      timer = setTimeout(settle, reduce ? 0 : 800);
    }

    // After sliding, silently jump back to the middle set so the loop never ends
    function settle() {
      if (!animating) return;
      clearTimeout(timer);
      animating = false;
      if (index < n || index >= 2 * n) {
        index = (index % n) + n;
        carousel.classList.add('is-jumping');
        updateState();
        position(false);
        void track.offsetWidth;
        carousel.classList.remove('is-jumping');
      }
    }

    track.addEventListener('transitionend', function (e) {
      if (e.target === track && e.propertyName === 'transform') settle();
    });

    carousel.querySelector('.fw-arrow--prev').addEventListener('click', function () { go(index - 1); });
    carousel.querySelector('.fw-arrow--next').addEventListener('click', function () { go(index + 1); });

    // Clicking a side card brings it to the middle
    var dragged = false;
    track.addEventListener('click', function (e) {
      var card = e.target.closest('.fw-card');
      if (!card) return;
      if (dragged) { e.preventDefault(); dragged = false; return; }
      var i = cards.indexOf(card);
      if (i !== index) { e.preventDefault(); go(i); }
    });

    // Keyboard: focusing a card, or pressing the arrow keys
    track.addEventListener('focusin', function (e) {
      var card = e.target.closest('.fw-card');
      var i = cards.indexOf(card);
      if (card && i !== index && !animating) go(i);
    });
    viewport.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft')  { e.preventDefault(); go(index - 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); go(index + 1); }
    });

    // Swipe / drag
    var startX = null;
    viewport.addEventListener('pointerdown', function (e) { startX = e.clientX; dragged = false; });
    viewport.addEventListener('pointermove', function (e) {
      if (startX !== null && Math.abs(e.clientX - startX) > 10) dragged = true;
    });
    viewport.addEventListener('pointerup', function (e) {
      if (startX === null) return;
      var dx = e.clientX - startX;
      startX = null;
      if (Math.abs(dx) > 50) go(index + (dx < 0 ? 1 : -1));
    });
    viewport.addEventListener('pointercancel', function () { startX = null; });

    window.addEventListener('resize', function () { position(false); });
    window.addEventListener('load', function () { position(false); });

    updateState();
    position(false);
  })();
