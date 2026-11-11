/* JOLYA CAFTAN — animations et interactions */
(function () {
  "use strict";

  var root = document.documentElement;
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- Ouverture ---------- */

  var started = Date.now();
  var loaded = false;

  function markLoaded() {
    if (loaded) return;
    loaded = true;
    var hasPreloader = !!document.querySelector(".preloader");
    var wait = hasPreloader && !reduced ? Math.max(0, 900 - (Date.now() - started)) : 0;
    setTimeout(function () {
      root.classList.add("is-loaded");
    }, wait);
  }

  if (document.readyState === "complete") {
    markLoaded();
  } else {
    window.addEventListener("load", markLoaded);
    // Sécurité : le site s'affiche même si une image tarde à charger.
    setTimeout(markLoaded, 3500);
  }

  /* ---------- En-tête ---------- */

  var header = document.querySelector(".site-header");
  var lastY = window.scrollY;

  function onHeaderScroll() {
    if (!header) return;
    var y = window.scrollY;
    header.classList.toggle("is-scrolled", y > 40);
    var goingDown = y > lastY && y > 500;
    if (!root.classList.contains("menu-open")) {
      header.classList.toggle("is-hidden", goingDown);
    }
    lastY = y;
  }

  /* ---------- Menu mobile ---------- */

  var toggle = document.querySelector(".menu-toggle");
  var mobileMenu = document.getElementById("menu-mobile");

  function setMenu(open) {
    if (!toggle || !mobileMenu) return;
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    if (open) {
      mobileMenu.hidden = false;
      // Laisse le navigateur prendre en compte l'affichage avant l'animation.
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          root.classList.add("menu-open");
        });
      });
      document.body.style.overflow = "hidden";
    } else {
      root.classList.remove("menu-open");
      document.body.style.overflow = "";
      setTimeout(function () {
        if (!root.classList.contains("menu-open")) mobileMenu.hidden = true;
      }, 800);
    }
  }

  if (toggle && mobileMenu) {
    toggle.addEventListener("click", function () {
      setMenu(!root.classList.contains("menu-open"));
    });
    mobileMenu.addEventListener("click", function (e) {
      if (e.target.closest("a")) setMenu(false);
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") setMenu(false);
    });
  }

  /* ---------- Apparitions au défilement ---------- */

  var revealEls = document.querySelectorAll("[data-reveal]");

  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });

    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add("is-in"); });
  }

  /* ---------- Texte révélé mot à mot ---------- */

  var wordBlocks = [];

  document.querySelectorAll("[data-words]").forEach(function (el) {
    var words = el.textContent.trim().split(/\s+/);
    el.textContent = "";
    var spans = words.map(function (word, i) {
      var span = document.createElement("span");
      span.className = "w";
      span.textContent = word;
      el.appendChild(span);
      if (i < words.length - 1) el.appendChild(document.createTextNode(" "));
      return span;
    });
    wordBlocks.push({ el: el, spans: spans });
  });

  function updateWords() {
    var vh = window.innerHeight;
    wordBlocks.forEach(function (block) {
      var rect = block.el.getBoundingClientRect();
      var start = vh * 0.88;
      var end = vh * 0.42;
      var progress = (start - rect.top) / (start - end + rect.height);
      progress = reduced ? 1 : Math.max(0, Math.min(1, progress));
      var lit = Math.round(progress * block.spans.length);
      block.spans.forEach(function (span, i) {
        span.classList.toggle("is-lit", i < lit);
      });
    });
  }

  /* ---------- Parallaxe ---------- */

  var parallaxEls = reduced ? [] : Array.prototype.slice.call(document.querySelectorAll("[data-parallax]"));

  function updateParallax() {
    var vh = window.innerHeight;
    parallaxEls.forEach(function (el) {
      var holder = el.parentElement || el;
      var rect = holder.getBoundingClientRect();
      if (rect.bottom < -200 || rect.top > vh + 200) return;
      var speed = parseFloat(el.getAttribute("data-parallax")) || 0.1;
      var offset = (rect.top + rect.height / 2 - vh / 2) * -speed;
      el.style.transform = "translate3d(0," + offset.toFixed(1) + "px,0)";
    });
  }

  /* ---------- Boucle de défilement ---------- */

  var ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      onHeaderScroll();
      updateWords();
      updateParallax();
      ticking = false;
    });
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
  onScroll();

  /* ---------- Photo au survol (souris uniquement) ---------- */

  if (window.matchMedia("(hover: hover)").matches) {
    document.querySelectorAll(".card__img--hover[data-src]").forEach(function (img) {
      var media = img.closest(".card__media");
      if (!media) return;
      media.addEventListener("pointerenter", function () {
        if (img.dataset.src) {
          img.src = img.dataset.src;
          delete img.dataset.src;
        }
      }, { once: true });
    });
  }

  /* ---------- Poussière d'or (fond animé) ---------- */

  var canvas = document.querySelector(".ambient__dust");

  if (canvas && canvas.getContext && !reduced) {
    var ctx = canvas.getContext("2d");
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var width = 0;
    var height = 0;
    var motes = [];
    var running = true;

    // Le grain d'or est dessiné une seule fois, puis recopié : bien plus léger qu'un dégradé par image.
    var sprite = document.createElement("canvas");
    sprite.width = sprite.height = 32;
    var sctx = sprite.getContext("2d");
    var grad = sctx.createRadialGradient(16, 16, 0, 16, 16, 16);
    grad.addColorStop(0, "rgba(190,150,85,1)");
    grad.addColorStop(1, "rgba(190,150,85,0)");
    sctx.fillStyle = grad;
    sctx.fillRect(0, 0, 32, 32);

    function resize() {
      width = window.innerWidth;
      height = window.innerHeight;
      canvas.width = width * dpr;
      canvas.height = height * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

      var count = Math.round(Math.min(46, Math.max(16, width / 30)));
      motes = [];
      for (var i = 0; i < count; i++) {
        motes.push({
          x: Math.random() * width,
          y: Math.random() * height,
          r: 0.6 + Math.random() * 1.9,
          vy: 0.12 + Math.random() * 0.44,
          sway: 0.2 + Math.random() * 0.6,
          phase: Math.random() * Math.PI * 2,
          twinkle: 0.4 + Math.random() * 1.2,
          alpha: 0.25 + Math.random() * 0.55
        });
      }
    }

    var lastFrame = 0;

    function draw(time) {
      if (!running) return;
      requestAnimationFrame(draw);
      // 30 images par seconde suffisent pour un mouvement aussi lent.
      if (time - lastFrame < 32) return;
      lastFrame = time;

      var t = time / 1000;
      ctx.clearRect(0, 0, width, height);

      for (var i = 0; i < motes.length; i++) {
        var m = motes[i];
        m.y -= m.vy;
        if (m.y < -10) {
          m.y = height + 10;
          m.x = Math.random() * width;
        }
        var x = m.x + Math.sin(t * m.sway + m.phase) * 18;
        var size = m.r * 8;
        ctx.globalAlpha = m.alpha * (0.55 + 0.45 * Math.sin(t * m.twinkle + m.phase));
        ctx.drawImage(sprite, x - size / 2, m.y - size / 2, size, size);
      }
    }

    document.addEventListener("visibilitychange", function () {
      var wasRunning = running;
      running = !document.hidden;
      if (running && !wasRunning) requestAnimationFrame(draw);
    });

    var resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        // Sur mobile, la barre d'adresse change la hauteur : on ignore ces micro-variations.
        if (Math.abs(window.innerWidth - width) > 2 || Math.abs(window.innerHeight - height) > 160) resize();
      }, 200);
    });

    resize();
    requestAnimationFrame(draw);
  }
})();
