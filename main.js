/* JOLYA CAFTAN — affiche la collection et les contacts à partir
   des fichiers data/*.json modifiés depuis le backoffice. */

(function () {
  "use strict";

  function escapeHtml(value) {
    return String(value == null ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  // Le backoffice enregistre "/images/x.jpg" : on le rend relatif
  // pour fonctionner aussi sous une adresse github.io/nom-du-projet/.
  function imagePath(path) {
    if (!path) return "";
    if (/^https?:\/\//i.test(path)) return path;
    return String(path).replace(/^\/+/, "");
  }

  function safeUrl(url) {
    return /^https:\/\//i.test(String(url || "").trim()) ? String(url).trim() : "";
  }

  function formatPrice(prix) {
    var n = Number(prix);
    if (!isFinite(n) || prix === "" || prix == null) return "";
    return n.toLocaleString("fr-FR", {
      style: "currency",
      currency: "EUR",
      minimumFractionDigits: Number.isInteger(n) ? 0 : 2
    });
  }

  function loadJson(url) {
    return fetch(url, { cache: "no-cache" }).then(function (r) {
      if (!r.ok) throw new Error(url + " : " + r.status);
      return r.json();
    });
  }

  function renderProducts(produits, site) {
    var grid = document.querySelector("[data-collection]");
    if (!grid) return;

    if (!produits.length) {
      grid.innerHTML = '<p class="collection-empty">La nouvelle collection arrive très bientôt.</p>';
      return;
    }

    grid.innerHTML = produits.map(function (p, i) {
      var number = String(i + 1).padStart(2, "0");
      var available = p.disponible !== false;
      var payUrl = safeUrl(p.lien_paiement);
      var subject = encodeURIComponent("Demande : " + (p.nom || "caftan"));
      var contactHref = site.email
        ? "mailto:" + site.email + "?subject=" + subject
        : "#contact";
      var img = imagePath(p.image);

      var action;
      if (!available) {
        action = '<span class="buy-button is-disabled" aria-disabled="true">Épuisé</span>';
      } else if (payUrl) {
        action = '<a class="buy-button" href="' + escapeHtml(payUrl) +
          '" target="_blank" rel="noopener">Commander <span aria-hidden="true">↗</span></a>';
      } else {
        action = '<a class="buy-button" href="' + escapeHtml(contactHref) +
          '">Réserver <span aria-hidden="true">↗</span></a>';
      }

      return (
        '<article class="collection-item' + (available ? "" : " is-sold-out") + '">' +
          '<div class="collection-image">' +
            (img ? '<img src="' + escapeHtml(img) + '" alt="' + escapeHtml(p.nom) + '" loading="lazy">' : "") +
            (available ? "" : '<span class="sold-out-badge">Épuisé</span>') +
          "</div>" +
          '<div class="item-information">' +
            "<div>" +
              '<p class="item-category">' + number +
                (p.categorie ? " — " + escapeHtml(p.categorie) : "") + "</p>" +
              "<h3>" + escapeHtml(p.nom) + "</h3>" +
            "</div>" +
            '<p class="item-price">' + escapeHtml(formatPrice(p.prix)) + "</p>" +
          "</div>" +
          (p.description ? '<p class="item-description">' + escapeHtml(p.description) + "</p>" : "") +
          '<div class="item-actions">' + action +
            (available && payUrl
              ? '<a class="item-question" href="' + escapeHtml(contactHref) + '">Une question ?</a>'
              : "") +
          "</div>" +
        "</article>"
      );
    }).join("");
  }

  function renderContact(site) {
    var text = document.querySelector("[data-contact-text]");
    if (text && site.texte_contact) text.textContent = site.texte_contact;

    var links = document.querySelector("[data-contact-links]");
    if (!links) return;

    var html = "";
    if (site.email) {
      html += '<a class="discover-button" href="mailto:' + escapeHtml(site.email) +
        '">Nous écrire <span aria-hidden="true">↗</span></a>';
    }
    var phone = String(site.whatsapp || "").replace(/[^\d]/g, "");
    if (phone) {
      html += '<a class="discover-button" href="https://wa.me/' + phone +
        '" target="_blank" rel="noopener">WhatsApp <span aria-hidden="true">↗</span></a>';
    }
    var insta = safeUrl(site.instagram);
    if (insta) {
      html += '<a class="discover-button" href="' + escapeHtml(insta) +
        '" target="_blank" rel="noopener">Instagram <span aria-hidden="true">↗</span></a>';
    }
    if (html) links.innerHTML = html;
  }

  var year = document.querySelector("[data-year]");
  if (year) year.textContent = new Date().getFullYear();

  Promise.all([
    loadJson("data/produits.json"),
    loadJson("data/site.json").catch(function () { return {}; })
  ]).then(function (res) {
    var produits = (res[0] && res[0].produits) || [];
    var site = res[1] || {};
    renderProducts(produits, site);
    renderContact(site);
  }).catch(function (err) {
    console.error(err);
    var grid = document.querySelector("[data-collection]");
    if (grid) {
      grid.innerHTML = '<p class="collection-empty">La collection est momentanément indisponible. Contactez-nous pour toute demande.</p>';
    }
  });
})();
