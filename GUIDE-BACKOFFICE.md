# Guide du backoffice — JOLYA CAFTAN

Ce guide explique comment modifier la boutique **sans toucher au code**.

---

## 1. Se connecter au backoffice

1. Aller sur **https://app.pagescms.org**
2. Cliquer sur **Sign in with GitHub** et se connecter avec le compte GitHub.
3. Choisir le dépôt **Site-kaftan**, puis la branche **main**.

Deux rubriques apparaissent à gauche :
- **Collection (produits)** : les caftans affichés sur le site
- **Infos de contact** : e-mail, WhatsApp, Instagram

> Chaque clic sur **Save** publie le site automatiquement.
> Les changements sont visibles en ligne **1 à 2 minutes** plus tard
> (recharger la page du site si besoin).

---

## 2. Ajouter, modifier ou retirer un caftan

Dans **Collection (produits)** :

| Champ | Ce qu'il faut mettre |
|---|---|
| Nom du caftan | ex. « Caftan Émeraude » |
| Catégorie | ex. « Cérémonie », « Signature » |
| Description courte | une phrase |
| Prix en euros | un nombre, ex. `360` |
| Photo | cliquer pour envoyer une photo depuis le téléphone ou l'ordinateur |
| Lien de paiement | le lien Stripe du produit (voir partie 3) |
| Disponible à la vente | décocher quand le caftan est **épuisé** |

- **Ajouter** : bouton **Add an entry** en bas de la liste.
- **Changer l'ordre** : faire glisser les produits.
- **Supprimer** : icône corbeille du produit.
- Puis **Save**.

**Ce que voit la cliente :**
- Lien de paiement rempli → bouton **Commander** (paiement en ligne)
- Lien vide → bouton **Réserver** (ouvre un e-mail de demande)
- « Disponible » décoché → badge **Épuisé**

Astuce photo : format portrait (vertical), moins de 1 Mo si possible.

---

## 3. Créer un lien de paiement (Stripe)

1. Créer un compte gratuit sur **https://dashboard.stripe.com/register**
   et renseigner les informations de l'entreprise + le RIB pour recevoir l'argent.
2. Menu **Liens de paiement** → **+ Nouveau**.
3. **Ajouter un produit** : nom, photo, prix (ex. 360 €), paiement unique.
4. Options conseillées :
   - ✅ **Collecter l'adresse de livraison** (pays : France…)
   - ✅ **Ajouter un champ personnalisé** → « Taille » (liste déroulante S / M / L / XL)
     ou « Mesures / remarques » (texte)
   - ✅ **Demander le numéro de téléphone**
   - Onglet **Après le paiement** → « Ne pas afficher de page de confirmation, rediriger
     vers votre site » → coller l'adresse du site suivie de `/merci.html`
5. Cliquer sur **Créer le lien**, puis **Copier**.
6. Coller ce lien dans le champ **Lien de paiement** du produit dans le backoffice → **Save**.

À chaque vente, Stripe envoie un e-mail avec le détail de la commande
(adresse, taille…). L'argent arrive sur le compte bancaire sous quelques jours.

> Autres solutions possibles (le champ accepte n'importe quel lien `https://`) :
> PayPal (bouton « Lien de paiement »), SumUp, Lydia Pro.

---

## 4. Modifier les contacts

Rubrique **Infos de contact** :
- **E-mail** : utilisé par les boutons « Nous écrire » et « Réserver »
- **WhatsApp** : numéro au format international sans le `+`, ex. `33612345678`
- **Instagram** : lien complet, ex. `https://instagram.com/jolyacaftan`

Un champ laissé vide = le bouton correspondant n'apparaît pas.
