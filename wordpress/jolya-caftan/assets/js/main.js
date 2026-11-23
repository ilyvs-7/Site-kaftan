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

  // Téléphone + connecté à WordPress : la barre d'administration défile avec la page.
  // L'en-tête la suit, puis se colle en haut de l'écran (sinon un vide apparaît au-dessus).
  var adminBar = document.body.classList.contains("admin-bar");

  var heroSection = document.querySelector(".hero");

  function onHeaderScroll() {
    if (!header) return;
    var y = window.scrollY;
    // Accueil : le bouton WhatsApp apparaît une fois la grande photo passée.
    if (heroSection) {
      document.body.classList.toggle("is-past-hero", y > heroSection.offsetHeight * 0.6);
    }
    if (adminBar) {
      header.style.top = window.innerWidth <= 600 ? Math.max(0, 46 - y) + "px" : "";
    }
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
      // Décalage limité : l'image ne laisse jamais de vide dans son cadre.
      var limit = rect.height * 0.035;
      offset = Math.max(-limit, Math.min(limit, offset));
      el.style.transform = "translate3d(0," + offset.toFixed(1) + "px,0)";
    });
  }

  /* ---------- Barre de progression du défilement ---------- */

  var progress = document.querySelector("[data-scroll-progress]");

  function updateProgress() {
    if (!progress) return;
    var max = document.documentElement.scrollHeight - window.innerHeight;
    var ratio = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0;
    progress.style.transform = "scaleX(" + ratio.toFixed(4) + ")";
  }

  /* ---------- Boutons aimantés (souris uniquement) ---------- */

  if (!reduced && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    document.querySelectorAll(".btn, .site-nav .menu-item--pill a").forEach(function (el) {
      el.classList.add("is-magnetic");
      el.addEventListener("pointermove", function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - (r.left + r.width / 2)) / (r.width / 2);
        var y = (e.clientY - (r.top + r.height / 2)) / (r.height / 2);
        el.style.transform = "translate(" + (x * 6).toFixed(1) + "px," + (y * 5).toFixed(1) + "px)";
      });
      el.addEventListener("pointerleave", function () {
        el.style.transform = "";
      });
    });
  }

  /* ---------- Boucle de défilement ---------- */

  var ticking = false;

  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(function () {
      onHeaderScroll();
      updateProgress();
      updateWords();
      updateParallax();
      ticking = false;
    });
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
  onScroll();

  /* ---------- Fiche caftan : barre d'achat sur téléphone ---------- */

  var buyBar = document.querySelector("[data-buy-bar]");
  var cartForm = document.querySelector("form.cart");

  if (buyBar && cartForm && "IntersectionObserver" in window) {
    var buyButton = cartForm.querySelector(".single_add_to_cart_button");

    // La barre apparaît tant que le vrai bouton d'achat n'est pas à l'écran.
    new IntersectionObserver(function (entries) {
      var hidden = !entries[0].isIntersecting;
      buyBar.classList.toggle("is-visible", hidden);
      document.body.classList.toggle("has-buy-bar", hidden);
    }).observe(cartForm);

    buyBar.querySelector("[data-buy-bar-go]").addEventListener("click", function () {
      var variable = cartForm.classList.contains("variations_form");
      if (buyButton && !variable && !buyButton.disabled) {
        buyButton.click();
      } else {
        cartForm.scrollIntoView({ behavior: reduced ? "auto" : "smooth", block: "center" });
      }
    });
  }

  /* ---------- Location : récapitulatif en direct ---------- */

  document.querySelectorAll(".rent").forEach(function (form) {
    var out = form.querySelector("[data-rent-summary]");
    var date = form.querySelector('input[name="date_evenement"]');
    if (!out) return;

    function formatDate(value) {
      var d = new Date(value + "T12:00:00");
      if (isNaN(d.getTime())) return value;
      return d.toLocaleDateString("fr-FR", { day: "numeric", month: "long", year: "numeric" });
    }

    function update() {
      var caftan = form.querySelector('input[name="caftan"]:checked');
      var size = form.querySelector('input[name="taille"]:checked');
      var parts = [];
      if (caftan) parts.push(caftan.value === "Je souhaite être conseillée" ? "Conseil personnalisé" : caftan.value);
      if (date && date.value) parts.push(formatDate(date.value));
      if (size) parts.push(size.value === "Je ne sais pas" ? "Taille à définir" : "Taille " + size.value.split(" ")[0]);
      out.textContent = parts.length ? "Votre demande : " + parts.join(" · ") : "Choisissez un caftan et une date.";
      out.classList.toggle("is-empty", !parts.length);
    }

    form.addEventListener("change", update);
    form.addEventListener("input", update);
    update();

    // Caftan déjà choisi depuis sa fiche : on le montre dans la rangée qui défile (téléphone).
    var chosen = form.querySelector(".rent-card input:checked");
    if (chosen) {
      var card = chosen.closest(".rent-card");
      var row = card.parentElement;
      if (row.scrollWidth > row.clientWidth) row.scrollLeft = card.offsetLeft - row.offsetLeft;
    }
  });

  /* ---------- Formulaires : envoi sans quitter la page ---------- */

  document.querySelectorAll("form.form").forEach(function (form) {
    if (!window.fetch || !window.FormData) return;

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var button = form.querySelector('button[type="submit"]');
      var label = button ? button.querySelector("span") : null;
      var initial = label ? label.textContent : "";
      var old = form.querySelector(".form__notice");
      if (old) old.remove();

      if (button) {
        button.disabled = true;
        if (label) label.textContent = "Envoi en cours…";
      }

      var data = new FormData(form);
      data.append("ajax", "1");

      // getAttribute : form.action désignerait le champ caché nommé « action ».
      fetch(form.getAttribute("action"), { method: "POST", body: data, credentials: "same-origin" })
        .then(function (response) { return response.json(); })
        .then(function (result) {
          if (result && result.ok) {
            var done = document.createElement("div");
            done.className = "form__done";
            done.setAttribute("role", "status");
            done.setAttribute("tabindex", "-1");
            done.innerHTML =
              '<p class="eyebrow">Demande envoyée</p>' +
              "<h3>Merci, nous avons bien reçu votre demande.</h3>" +
              "<p>Nous revenons vers vous très vite, par e-mail ou par téléphone.</p>";
            form.replaceWith(done);
            done.focus({ preventScroll: true });
            done.scrollIntoView({ behavior: reduced ? "auto" : "smooth", block: "center" });
          } else {
            throw new Error("refus");
          }
        })
        .catch(function () {
          var notice = document.createElement("p");
          notice.className = "form__notice form__notice--error";
          notice.setAttribute("role", "alert");
          notice.textContent = "La demande n'a pas pu être envoyée. Vérifiez les champs obligatoires, ou écrivez-nous directement par e-mail.";
          form.insertBefore(notice, form.firstChild);
          if (button) {
            button.disabled = false;
            if (label) label.textContent = initial;
          }
        });
    });
  });

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
