# JOLYA CAFTAN — Guide WordPress

Tout se fait dans Safari, sur iPad.

---

## Partie 1 — Installation (une seule fois, 20 à 30 minutes)

### 1. Hébergement et WordPress
1. Commander un hébergement avec **installation de WordPress en un clic** (o2switch, Hostinger, OVH…) et un nom de domaine (ex. `jolyacaftan.fr`).
2. Dans l'espace client de l'hébergeur, lancer l'installation de WordPress. Noter l'identifiant et le mot de passe.
3. Se connecter à l'administration : `https://votre-domaine.fr/wp-admin`

### 2. Installer le thème
1. Télécharger le fichier **`jolya-caftan.zip`** (il s'enregistre dans l'app Fichiers).
   Ne pas l'ouvrir ni le décompresser.
2. Dans WordPress : **Apparence → Thèmes → Ajouter un thème → Téléverser un thème**.
3. Choisir `jolya-caftan.zip` → **Installer** → **Activer**.

> Si un message indique que le fichier est trop lourd, demander au support de
> l'hébergeur d'augmenter la « taille maximale d'envoi » (upload_max_filesize) à 8 Mo. Le fichier pèse moins de 2 Mo.

### 3. Installer la boutique
1. Un bandeau « Installer WooCommerce » apparaît en haut de l'administration : cliquer dessus,
   puis **Installer** et **Activer** l'extension **WooCommerce**.
2. Suivre l'assistant WooCommerce (pays : France, devise : euro).
3. Un nouveau bandeau propose **« Importer la collection »** : cliquer dessus.
   Les 8 caftans sont créés avec leurs photos et descriptions (prix à ajuster).

### 4. Activer le paiement
1. **WooCommerce → Réglages → Paiements**.
2. Activer **Stripe** (carte bancaire, Apple Pay) et/ou **PayPal** : WordPress propose
   d'installer l'extension officielle, puis de créer ou connecter le compte.
3. Renseigner le RIB dans Stripe/PayPal pour recevoir l'argent.
4. Faire une commande test avant d'annoncer le site.

### 5. Les pages du site
À l'activation du thème, ces pages sont créées automatiquement (Pages → Toutes les pages) :
**La maison**, **Sur mesure**, **Contact** (avec formulaire), **Guide des tailles**,
**Livraison & retours**, **Questions fréquentes**.

Leurs textes sont des propositions : relisez-les et adaptez-les (délais, conditions de retour,
tailles) à la réalité de la boutique.

Trois pages légales sont créées **en brouillon**, donc invisibles sur le site :
**Mentions légales**, **Conditions générales de vente**, **Politique de confidentialité**.
Complétez les passages **[À COMPLÉTER]**, faites-les relire, puis cliquez sur **Publier**.
Elles apparaissent alors automatiquement en bas du site.

### 6. Livraison
**WooCommerce → Réglages → Expédition** : créer une zone « France » avec un tarif (ou gratuit).

---

## Partie 2 — Utilisation au quotidien

### Ajouter ou modifier un caftan
**Produits → Tous les produits** (ou **Ajouter**).
- **Nom**, **description**, **description courte** (la phrase sous le nom)
- **Prix** : bloc « Données produit » → Tarif normal
- **Photo principale** : « Image du produit » (format vertical conseillé)
- **Autres photos** : « Galerie produit ». Une 2ᵉ photo verticale (ex. le dos)
  apparaît au survol sur ordinateur.
- **Catégorie** : Cérémonie, Signature, Intemporel… (affichée au-dessus du nom)
- **Épuisé** : Données produit → Inventaire → État du stock → « Rupture de stock »
- Cliquer sur **Mettre à jour** / **Publier**.

**Pièce mise en avant sur l'accueil** : dans la liste des produits, cliquer sur l'étoile ☆
du caftan choisi.

**Proposer des tailles** : Données produit → type « Produit variable » → Attributs → ajouter
« Taille » (S | M | L | XL) → Variations.

### Suivre les commandes
**WooCommerce → Commandes**. Un e-mail arrive à chaque vente. Après l'envoi du colis,
passer la commande en « Terminée ».

### Modifier les textes et photos de l'accueil
**Apparence → Personnaliser → JOLYA — Contenu du site**
- *Accueil — grande image* : photos, titre, bandeau du haut
- *La maison* : texte et photo
- *Fiche caftan — informations* : textes « Tailles », « Livraison », « Entretien » affichés sur chaque caftan
- *Contact* : e-mail, WhatsApp, Instagram

### Modifier une page (La maison, Sur mesure, FAQ…)
**Pages → Toutes les pages** → cliquer sur la page → modifier le texte directement → **Mettre à jour**.
- La phrase sous le titre se règle dans le panneau de droite : **Extrait**.
- La photo en haut de page : **Image mise en avant**.

### Messages du formulaire de contact
Ils arrivent par e-mail à l'adresse réglée dans *Personnaliser → Contact*. Pensez à vérifier
le dossier « indésirables » lors des premiers messages.

### Entretien (5 minutes par mois)
**Tableau de bord → Mises à jour** : tout mettre à jour. Activer les sauvegardes
automatiques proposées par l'hébergeur.
