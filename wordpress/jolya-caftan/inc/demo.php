<?php
/**
 * Collection fournie avec le thème : sert d'aperçu tant que la boutique est vide,
 * et peut être importée en un clic comme vrais produits WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jolya_demo_products() {
	return array(
		array( 'name' => 'Caftan Nuit Royale', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'nuit-face.webp' ),
			'short' => "Velours bleu nuit et broderies d'or.",
			'desc'  => "Un velours bleu nuit profond, entièrement rehaussé de broderies dorées réalisées à la main. Ceinture ouvragée assortie. Une pièce de cérémonie au tombé majestueux." ),
		array( 'name' => 'Caftan Majorelle', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'majorelle-face.webp' ),
			'short' => 'Bleu Majorelle, motifs floraux dorés.',
			'desc'  => "Inspiré des jardins de Marrakech, ce caftan bleu Majorelle se pare de motifs floraux et de perles dorées. Traîne légère et ceinture bijou." ),
		array( 'name' => 'Caftan Lilas', 'cat' => 'Intemporel', 'price' => 360, 'images' => array( 'lilas-face.webp', 'lilas-dos.webp' ),
			'short' => 'Crêpe lilas, broderies ton sur ton.',
			'desc'  => "Une silhouette épurée en crêpe lilas, soulignée de broderies ton sur ton et d'une ceinture en satin prune nouée au dos. L'élégance en toute délicatesse." ),
		array( 'name' => 'Caftan Grenat', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'grenat-face.webp' ),
			'short' => "Rouge grenat et arabesques d'or.",
			'desc'  => "Un rouge grenat éclatant, couvert d'arabesques dorées brodées main. La pièce des grandes célébrations, pensée pour la mariée et ses invitées d'honneur." ),
		array( 'name' => 'Caftan Émeraude', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'emeraude-face.webp', 'emeraude-dos.webp' ),
			'short' => 'Vert émeraude, perlage noir scintillant.',
			'desc'  => "Un vert émeraude intense, travaillé d'un perlage noir scintillant sur le devant, le dos et les manches. Ceinture dorée ajourée." ),
		array( 'name' => 'Caftan Safran', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'safran-face.webp', 'safran-dos.webp' ),
			'short' => 'Orange safran, broderies dorées et pierres.',
			'desc'  => "La chaleur du safran, sublimée de broderies dorées en relief et de pierres vert profond. Un dos brodé spectaculaire." ),
		array( 'name' => 'Caftan Jade', 'cat' => 'Intemporel', 'price' => 360, 'images' => array( 'jade-face.webp' ),
			'short' => 'Vert jade et broderies argentées.',
			'desc'  => "Vert jade et fils d'argent : des broderies baroques, des manches frangées et une ceinture métallisée pour une allure résolument raffinée." ),
		array( 'name' => 'Caftan Fuchsia Impérial', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'fuchsia-face.webp', 'hero-fuchsia.webp' ),
			'short' => 'Fuchsia profond, galons dorés.',
			'desc'  => "La pièce emblématique de la maison : un fuchsia profond structuré de larges galons dorés aux motifs géométriques. Traîne et ceinture bijou." ),
	);
}

/** Cartes d'aperçu (sans WooCommerce ou boutique vide). */
function jolya_demo_cards() {
	$cards = array();
	foreach ( jolya_demo_products() as $p ) {
		$cards[] = array(
			'name'     => $p['name'],
			'cat'      => $p['cat'],
			'short'    => $p['short'],
			'price'    => number_format_i18n( $p['price'] ) . ' €',
			'url'      => home_url( '/#contact' ),
			'image'    => jolya_asset( $p['images'][0] ),
			'hover'    => isset( $p['images'][1] ) && 'hero-fuchsia.webp' !== $p['images'][1] ? jolya_asset( $p['images'][1] ) : '',
			'sold_out' => false,
		);
	}
	return $cards;
}

/** Copie une image du thème dans la médiathèque. */
function jolya_import_image( $file, $title ) {
	$source = get_template_directory() . '/assets/images/' . $file;
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	$upload = wp_upload_bits( 'jolya-' . $file, null, file_get_contents( $source ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment( array(
		'post_mime_type' => 'image/webp',
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return $id;
}

function jolya_import_demo() {
	if ( ! class_exists( 'WC_Product_Simple' ) ) {
		return 0;
	}
	$count = 0;
	foreach ( jolya_demo_products() as $order => $p ) {
		$product = new WC_Product_Simple();
		$product->set_name( $p['name'] );
		$product->set_status( 'publish' );
		$product->set_regular_price( (string) $p['price'] );
		$product->set_short_description( $p['short'] );
		$product->set_description( $p['desc'] );
		$product->set_menu_order( $order );
		$product->set_sold_individually( true );

		$term = term_exists( $p['cat'], 'product_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $p['cat'], 'product_cat' );
		}
		if ( ! is_wp_error( $term ) ) {
			$product->set_category_ids( array( (int) $term['term_id'] ) );
		}

		$ids = array();
		foreach ( $p['images'] as $file ) {
			$id = jolya_import_image( $file, $p['name'] );
			if ( $id ) {
				$ids[] = $id;
			}
		}
		if ( $ids ) {
			$product->set_image_id( array_shift( $ids ) );
			$product->set_gallery_image_ids( $ids );
		}
		$product->save();
		$count++;
	}
	update_option( 'jolya_demo_done', 1 );
	return $count;
}

// Bandeau d'aide dans l'administration.
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		$url = admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' );
		echo '<div class="notice notice-warning"><p><strong>JOLYA CAFTAN :</strong> pour activer la boutique (panier, paiement, commandes), installez et activez l\'extension gratuite <strong>WooCommerce</strong>. <a class="button button-primary" style="margin-left:8px" href="' . esc_url( $url ) . '">Installer WooCommerce</a></p></div>';
		return;
	}
	if ( get_option( 'jolya_demo_done' ) ) {
		return;
	}
	if ( isset( $_GET['jolya_imported'] ) ) { // phpcs:ignore
		return;
	}
	$import  = wp_nonce_url( admin_url( 'admin-post.php?action=jolya_import_demo' ), 'jolya_demo' );
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=jolya_dismiss_demo' ), 'jolya_demo' );
	echo '<div class="notice notice-info"><p><strong>JOLYA CAFTAN :</strong> voulez-vous ajouter automatiquement les 8 caftans de la collection (photos, descriptions, prix à ajuster) dans la boutique ?</p><p><a class="button button-primary" href="' . esc_url( $import ) . '">Importer la collection</a> <a class="button" href="' . esc_url( $dismiss ) . '">Non merci</a></p></div>';
} );

add_action( 'admin_post_jolya_import_demo', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'jolya_demo' ) ) {
		wp_die( 'Action non autorisée.' );
	}
	if ( ! get_option( 'jolya_demo_done' ) ) {
		jolya_import_demo();
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=product&jolya_imported=1' ) );
	exit;
} );

add_action( 'admin_post_jolya_dismiss_demo', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'jolya_demo' ) ) {
		wp_die( 'Action non autorisée.' );
	}
	update_option( 'jolya_demo_done', 1 );
	wp_safe_redirect( admin_url() );
	exit;
} );
