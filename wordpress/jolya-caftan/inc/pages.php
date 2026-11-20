<?php
/**
 * Pages du site, créées automatiquement à l'activation du thème.
 * Leur contenu reste entièrement modifiable dans Pages → Toutes les pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Blocs de contenu : raccourcis pour écrire les pages. */
function jolya_b_h( $text, $level = 2 ) {
	$attr = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>\n<!-- /wp:heading -->\n\n";
}

function jolya_b_p( $text ) {
	return "<!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->\n\n";
}

function jolya_b_list( $items ) {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
}

function jolya_b_details( $question, $answer ) {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>{$question}</summary>" . trim( jolya_b_p( $answer ) ) . "</details>\n<!-- /wp:details -->\n\n";
}

function jolya_b_columns( $columns, $class ) {
	$out = '<!-- wp:columns {"className":"' . $class . '"} -->' . "\n<div class=\"wp-block-columns {$class}\">";
	foreach ( $columns as $column ) {
		$out .= "<!-- wp:column -->\n<div class=\"wp-block-column\">" . trim( jolya_b_h( $column[0], 3 ) ) . "\n\n" . trim( jolya_b_p( $column[1] ) ) . "</div>\n<!-- /wp:column -->\n\n";
	}
	return $out . "</div>\n<!-- /wp:columns -->\n\n";
}

function jolya_b_shortcode( $shortcode ) {
	return "<!-- wp:shortcode -->\n{$shortcode}\n<!-- /wp:shortcode -->\n\n";
}

function jolya_b_table( $head, $rows ) {
	$out = "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table class=\"has-fixed-layout\"><thead><tr>";
	foreach ( $head as $cell ) {
		$out .= "<th>{$cell}</th>";
	}
	$out .= '</tr></thead><tbody>';
	foreach ( $rows as $row ) {
		$out .= '<tr>';
		foreach ( $row as $cell ) {
			$out .= "<td>{$cell}</td>";
		}
		$out .= '</tr>';
	}
	return $out . "</tbody></table></figure>\n<!-- /wp:table -->\n\n";
}

/**
 * Définition des pages. « lead » = phrase d'introduction (extrait de la page).
 * Les pages légales sont créées en brouillon : elles contiennent des mentions à compléter.
 */
function jolya_site_pages() {
	$todo = '<strong>[À COMPLÉTER]</strong>';

	return array(
		'a-propos' => array(
			'title'   => 'À propos',
			'lead'    => "Une maison de caftans née d'une passion : faire rayonner un savoir-faire séculaire dans des pièces d'aujourd'hui.",
			'content' =>
				jolya_b_h( "Une histoire de transmission" ) .
				jolya_b_p( "JOLYA CAFTAN est née d'un attachement profond au caftan, ce vêtement d'apparat qui accompagne depuis des siècles les plus beaux moments de la vie. Mariages, fiançailles, fêtes de famille : chaque pièce raconte une histoire et se transmet souvent d'une génération à l'autre." ) .
				jolya_b_p( "Notre maison sélectionne et imagine des caftans où la tradition rencontre une allure contemporaine. Des lignes épurées, des couleurs profondes, et toujours ce travail du détail qui distingue une belle pièce d'une pièce d'exception." ) .
				jolya_b_h( "Nos engagements" ) .
				jolya_b_columns( array(
					array( "L'exigence", "Chaque caftan est contrôlé avant de vous être confié : tombé du tissu, régularité des broderies, finitions des coutures." ),
					array( "L'authenticité", "Sfifa, aâkad, broderies au fil d'or : nous restons fidèles aux techniques traditionnelles de la couture marocaine." ),
					array( "L'écoute", "Une cérémonie se prépare. Nous prenons le temps de comprendre votre occasion pour vous orienter vers la pièce juste." ),
				), 'jolya-values' ) .
				jolya_b_h( "Le temps du geste" ) .
				jolya_b_p( "Un caftan brodé demande de longues heures de travail. Le motif est d'abord tracé, puis chaque point est posé à la main. C'est ce temps, invisible et pourtant partout présent, qui donne à la pièce sa profondeur et son éclat." ),
		),

		'location' => array(
			'title'   => 'Location',
			'lead'    => "Sublimez votre événement le temps d'une journée : choisissez votre caftan, indiquez la date, nous nous occupons du reste.",
			'content' => jolya_b_shortcode( '[jolya_location]' ),
		),

		'contact' => array(
			'title'   => 'Contact',
			'lead'    => "Une question sur un modèle, une taille ou une commande ? Écrivez-nous, nous vous répondons personnellement.",
			'content' =>
				jolya_b_shortcode( '[jolya_contact]' ),
		),

		'guide-des-tailles' => array(
			'title'   => 'Guide des tailles',
			'lead'    => "Le caftan se porte ample et se ceinture à la taille. Voici nos repères pour choisir sereinement.",
			'content' =>
				jolya_b_h( "Correspondances" ) .
				jolya_b_table(
					array( 'Taille', 'Équivalent', 'Poitrine', 'Taille', 'Hanches' ),
					array(
						array( 'S', '36 – 38', '84 – 88 cm', '66 – 70 cm', '92 – 96 cm' ),
						array( 'M', '40 – 42', '92 – 96 cm', '74 – 78 cm', '100 – 104 cm' ),
						array( 'L', '44 – 46', '100 – 106 cm', '82 – 88 cm', '108 – 114 cm' ),
						array( 'XL', '48 – 50', '112 – 118 cm', '94 – 100 cm', '120 – 126 cm' ),
					)
				) .
				jolya_b_p( "Ces mesures sont indicatives et peuvent varier légèrement d'un modèle à l'autre. En cas de doute entre deux tailles, écrivez-nous." ) .
				jolya_b_h( "Prendre ses mesures" ) .
				jolya_b_list( array(
					"<strong>Poitrine</strong> : mesurez à l'endroit le plus fort, le mètre bien à l'horizontale.",
					"<strong>Taille</strong> : mesurez au creux de la taille, sans serrer.",
					"<strong>Hanches</strong> : mesurez à l'endroit le plus large du bassin.",
					"<strong>Longueur</strong> : de l'épaule jusqu'au sol, en portant les chaussures prévues pour l'événement.",
				) ) .
				jolya_b_h( "Un doute ?" ) .
				jolya_b_p( "Écrivez-nous avant de commander ou de réserver : nous vous aidons à choisir la bonne taille." ),
		),

		'livraison-retours' => array(
			'title'   => 'Livraison & retours',
			'lead'    => "Chaque caftan est préparé avec soin et expédié dans un emballage qui le protège.",
			'content' =>
				jolya_b_h( "Livraison" ) .
				jolya_b_p( "Les pièces disponibles sont préparées puis expédiées avec un numéro de suivi, que vous recevez par e-mail. Les délais et les frais de livraison sont indiqués avant le paiement." ) .
				jolya_b_p( "Pour une livraison hors de France métropolitaine, contactez-nous avant de commander : nous vous indiquerons les conditions." ) .
				jolya_b_h( "Retours" ) .
				jolya_b_p( "Conformément à la loi, vous disposez de 14 jours à compter de la réception de votre commande pour changer d'avis. La pièce doit nous être retournée dans son état d'origine, non portée, avec son emballage." ) .
				jolya_b_h( "Un problème avec votre commande ?" ) .
				jolya_b_p( "Écrivez-nous dès réception, avec une photo si possible. Nous trouverons une solution ensemble." ),
		),

		'questions-frequentes' => array(
			'title'   => 'Questions fréquentes',
			'lead'    => "Les réponses aux questions que l'on nous pose le plus souvent.",
			'content' =>
				jolya_b_h( "Choisir son caftan" ) .
				jolya_b_details( "Comment choisir ma taille ?", "Consultez notre guide des tailles. Le caftan se porte ample et se resserre à la taille grâce à la ceinture : en cas d'hésitation entre deux tailles, écrivez-nous." ) .
				jolya_b_details( "La ceinture est-elle incluse ?", "La ceinture présentée sur les photos accompagne le caftan, sauf mention contraire sur la fiche du modèle." ) .
				jolya_b_details( "Les couleurs sont-elles fidèles aux photos ?", "Nous veillons à ce que les photos soient les plus fidèles possible. Une légère différence de teinte peut toutefois exister selon votre écran." ) .
				jolya_b_h( "Commande et paiement" ) .
				jolya_b_details( "Quels moyens de paiement acceptez-vous ?", "Le paiement s'effectue en ligne par carte bancaire, sur une page sécurisée. Vos données bancaires ne nous sont jamais transmises." ) .
				jolya_b_details( "Puis-je louer un caftan ?", "Oui. Choisissez votre modèle et la date de votre événement sur la page Location : nous vous confirmons la disponibilité, le tarif et la caution." ) .
				jolya_b_h( "Entretien" ) .
				jolya_b_details( "Comment entretenir mon caftan ?", "Nous recommandons un nettoyage à sec chez un professionnel. Conservez votre caftan suspendu, dans une housse, à l'abri de la lumière et de l'humidité." ) .
				jolya_b_details( "Comment le repasser ?", "Repassez sur l'envers, à basse température, en évitant les broderies et les perles. Un défroisseur vapeur, tenu à distance, convient très bien." ),
		),

		'mentions-legales' => array(
			'title'   => 'Mentions légales',
			'status'  => 'draft',
			'lead'    => '',
			'content' =>
				jolya_b_h( "Éditeur du site" ) .
				jolya_b_p( "Le présent site est édité par : {$todo} nom et prénom ou raison sociale, forme juridique, adresse du siège, numéro SIRET, numéro de TVA le cas échéant." ) .
				jolya_b_p( "Contact : {$todo} adresse e-mail et numéro de téléphone." ) .
				jolya_b_p( "Directrice de la publication : {$todo} nom et prénom." ) .
				jolya_b_h( "Hébergement" ) .
				jolya_b_p( "Le site est hébergé par : {$todo} nom de l'hébergeur, adresse, numéro de téléphone." ) .
				jolya_b_h( "Propriété intellectuelle" ) .
				jolya_b_p( "L'ensemble des contenus de ce site (textes, photographies, logo, mise en page) est protégé. Toute reproduction sans autorisation écrite est interdite." ),
		),

		'cgv' => array(
			'title'   => 'Conditions générales de vente',
			'status'  => 'draft',
			'lead'    => '',
			'content' =>
				jolya_b_p( "<em>Modèle à faire relire et à adapter avant publication.</em>" ) .
				jolya_b_h( "1. Vendeur" ) .
				jolya_b_p( "Les présentes conditions s'appliquent aux ventes conclues sur ce site par : {$todo} identité du vendeur, adresse, SIRET, e-mail." ) .
				jolya_b_h( "2. Produits et prix" ) .
				jolya_b_p( "Les caftans sont décrits et photographiés avec le plus grand soin. Les prix sont indiqués en euros, {$todo} TTC ou « TVA non applicable, art. 293 B du CGI ». Les frais de livraison sont précisés avant la validation de la commande." ) .
				jolya_b_h( "3. Commande et paiement" ) .
				jolya_b_p( "La commande est validée après le paiement en ligne, effectué par carte bancaire via un prestataire sécurisé. Un e-mail de confirmation est envoyé au client." ) .
				jolya_b_h( "4. Livraison" ) .
				jolya_b_p( "Les produits sont livrés à l'adresse indiquée lors de la commande, dans un délai de {$todo}. En cas de retard, le client en est informé." ) .
				jolya_b_h( "5. Droit de rétractation" ) .
				jolya_b_p( "Le client dispose d'un délai de 14 jours à compter de la réception pour exercer son droit de rétractation, sans avoir à motiver sa décision. Les frais de retour sont {$todo} à la charge du client / du vendeur. Le remboursement intervient dans les 14 jours suivant la notification de la rétractation, sous réserve du retour du produit." ) .
				jolya_b_h( "6. Garanties" ) .
				jolya_b_p( "Les produits bénéficient de la garantie légale de conformité et de la garantie des vices cachés, dans les conditions prévues par la loi." ) .
				jolya_b_h( "7. Litiges" ) .
				jolya_b_p( "Les présentes conditions sont soumises au droit français. En cas de litige, le client peut recourir gratuitement à un médiateur de la consommation : {$todo} nom et coordonnées du médiateur." ),
		),

		'politique-de-confidentialite' => array(
			'title'   => 'Politique de confidentialité',
			'status'  => 'draft',
			'lead'    => '',
			'content' =>
				jolya_b_p( "<em>Modèle à faire relire et à adapter avant publication.</em>" ) .
				jolya_b_h( "Données collectées" ) .
				jolya_b_p( "Nous collectons uniquement les données nécessaires au traitement de vos commandes et de vos demandes : nom, adresse, e-mail, téléphone, contenu de vos messages. Les paiements sont traités par notre prestataire de paiement ; nous n'avons jamais accès à vos coordonnées bancaires." ) .
				jolya_b_h( "Utilisation" ) .
				jolya_b_p( "Ces données servent à préparer et livrer vos commandes, à vous répondre et à respecter nos obligations légales (facturation). Elles ne sont ni vendues ni cédées à des tiers." ) .
				jolya_b_h( "Durée de conservation" ) .
				jolya_b_p( "Les données de commande sont conservées pendant la durée exigée par la loi. Les messages de contact sont conservés {$todo} durée." ) .
				jolya_b_h( "Vos droits" ) .
				jolya_b_p( "Vous pouvez demander l'accès à vos données, leur rectification ou leur suppression en écrivant à : {$todo} adresse e-mail. Vous pouvez également adresser une réclamation à la CNIL." ),
		),
	);
}

/**
 * Crée les pages du thème qui n'ont encore jamais été créées.
 * Une page supprimée volontairement n'est pas recréée ; une page ajoutée
 * dans une nouvelle version du thème (ex. Location) est créée automatiquement.
 */
function jolya_create_pages() {
	$done = get_option( 'jolya_pages_created', null );
	if ( null === $done ) {
		// Sites installés avant cette version : les pages d'origine existent déjà.
		$done = get_option( 'jolya_pages_done' ) ? array( 'la-maison', 'a-propos', 'sur-mesure', 'contact', 'guide-des-tailles', 'livraison-retours', 'questions-frequentes', 'mentions-legales', 'cgv', 'politique-de-confidentialite' ) : array();
	}
	$done    = (array) $done;
	$created = 0;

	foreach ( jolya_site_pages() as $slug => $page ) {
		if ( in_array( $slug, $done, true ) ) {
			continue;
		}
		$done[] = $slug;
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => $page['title'],
			'post_excerpt' => $page['lead'],
			'post_content' => $page['content'],
			'post_status'  => isset( $page['status'] ) ? $page['status'] : 'publish',
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			jolya_remember_content( $id );
			$created++;
		}
	}

	update_option( 'jolya_pages_created', $done );
	update_option( 'jolya_pages_done', 1 );
	return $created;
}

add_action( 'after_switch_theme', 'jolya_create_pages' );

/** Empreinte du contenu écrit par le thème : permet de savoir si la page a été modifiée depuis. */
function jolya_remember_content( $id ) {
	$post = get_post( $id );
	if ( $post ) {
		update_post_meta( $id, '_jolya_hash', md5( $post->post_content ) );
	}
}

/** La page a-t-elle été modifiée dans WordPress depuis que le thème l'a écrite ? */
function jolya_page_edited( $post ) {
	$hash = get_post_meta( $post->ID, '_jolya_hash', true );
	if ( $hash ) {
		return md5( $post->post_content ) !== $hash;
	}
	return $post->post_modified_gmt !== $post->post_date_gmt;
}

/**
 * Mises à jour des pages lors d'une nouvelle version du thème.
 * Une page modifiée par la cliente n'est jamais réécrite.
 */
function jolya_upgrade_pages() {
	// « La maison » devient « À propos » (lien en bas de page).
	$maison = get_page_by_path( 'la-maison' );
	if ( $maison && ! get_page_by_path( 'a-propos' ) ) {
		$untouched = ! jolya_page_edited( $maison );
		wp_update_post( array(
			'ID'         => $maison->ID,
			'post_title' => 'La maison' === $maison->post_title ? 'À propos' : $maison->post_title,
			'post_name'  => 'a-propos',
		) );
		if ( $untouched ) {
			jolya_remember_content( $maison->ID );
		}
	}

	// La maison ne propose pas de sur-mesure : la page passe en brouillon (récupérable).
	$custom = get_page_by_path( 'sur-mesure' );
	if ( $custom && 'publish' === $custom->post_status && ! jolya_page_edited( $custom ) ) {
		wp_update_post( array( 'ID' => $custom->ID, 'post_status' => 'draft' ) );
	}

	// Pages jamais modifiées : on applique les textes de la nouvelle version.
	foreach ( jolya_site_pages() as $slug => $page ) {
		$post = get_page_by_path( $slug );
		if ( ! $post || jolya_page_edited( $post ) ) {
			continue;
		}
		if ( $post->post_content !== $page['content'] || $post->post_excerpt !== $page['lead'] ) {
			wp_update_post( array(
				'ID'           => $post->ID,
				'post_excerpt' => $page['lead'],
				'post_content' => $page['content'],
			) );
		}
		jolya_remember_content( $post->ID );
	}
}

// Après une mise à jour du thème, les nouvelles pages sont créées à la première visite de l'administration.
add_action( 'admin_init', function () {
	if ( get_option( 'jolya_pages_version' ) !== JOLYA_VERSION && current_user_can( 'edit_pages' ) ) {
		jolya_upgrade_pages();
		jolya_create_pages();
		update_option( 'jolya_pages_version', JOLYA_VERSION );
	}
} );

// Rappel : les pages légales sont en brouillon tant qu'elles ne sont pas complétées.
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'jolya_legal_dismissed' ) ) {
		return;
	}
	$drafts = array();
	foreach ( array( 'mentions-legales', 'cgv', 'politique-de-confidentialite' ) as $slug ) {
		$found = get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_status' => 'draft', 'numberposts' => 1 ) );
		if ( $found ) {
			$drafts[] = '<a href="' . esc_url( get_edit_post_link( $found[0]->ID ) ) . '">' . esc_html( $found[0]->post_title ) . '</a>';
		}
	}
	if ( ! $drafts ) {
		return;
	}
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=jolya_dismiss_legal' ), 'jolya_legal' );
	echo '<div class="notice notice-warning"><p><strong>JOLYA CAFTAN :</strong> ces pages légales sont en brouillon. Complétez les mentions [À COMPLÉTER], faites-les relire, puis publiez-les : ' . implode( ', ', $drafts ) . '. <a href="' . esc_url( $dismiss ) . '">Ne plus afficher</a></p></div>'; // phpcs:ignore
} );

add_action( 'admin_post_jolya_dismiss_legal', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'jolya_legal' ) ) {
		wp_die( 'Action non autorisée.' );
	}
	update_option( 'jolya_legal_dismissed', 1 );
	wp_safe_redirect( admin_url() );
	exit;
} );

/** Adresse d'une page du site, ou adresse de repli si elle n'existe pas. */
function jolya_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return $fallback;
}
