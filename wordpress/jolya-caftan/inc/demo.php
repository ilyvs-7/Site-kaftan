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
		// Ordre d'affichage : les caftans sombres, puis les couleurs vives, puis les clairs.
		array( 'name' => 'Caftan Nuit Royale', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'nuit-face.webp' ),
			'short' => "Velours bleu nuit et broderies d'or.",
			'desc'  => "Un velours bleu nuit profond, entièrement rehaussé de broderies dorées réalisées à la main. Ceinture ouvragée assortie. Une pièce de cérémonie au tombé majestueux." ),
		array( 'name' => 'Caftan Émeraude', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'emeraude-face.webp', 'emeraude-dos.webp' ),
			'short' => 'Vert émeraude, perlage noir scintillant.',
			'desc'  => "Un vert émeraude intense, travaillé d'un perlage noir scintillant sur le devant, le dos et les manches. Ceinture dorée ajourée." ),
		array( 'name' => 'Caftan Majorelle', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'majorelle-face.webp' ),
			'short' => 'Bleu Majorelle, motifs floraux dorés.',
			'desc'  => "Inspiré des jardins de Marrakech, ce caftan bleu Majorelle se pare de motifs floraux et de perles dorées. Traîne légère et ceinture bijou." ),
		array( 'name' => 'Caftan Jade', 'cat' => 'Intemporel', 'price' => 360, 'images' => array( 'jade-face.webp' ),
			'short' => 'Vert jade et broderies argentées.',
			'desc'  => "Vert jade et fils d'argent : des broderies baroques, des manches frangées et une ceinture métallisée pour une allure résolument raffinée." ),
		array( 'name' => 'Caftan Grenat', 'cat' => 'Cérémonie', 'price' => 360, 'images' => array( 'grenat-face.webp' ),
			'short' => "Rouge grenat et arabesques d'or.",
			'desc'  => "Un rouge grenat éclatant, couvert d'arabesques dorées brodées main. La pièce des grandes célébrations, pensée pour la mariée et ses invitées d'honneur." ),
		array( 'name' => 'Caftan Fuchsia Impérial', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'fuchsia-face.webp', 'hero-fuchsia.webp' ),
			'short' => 'Fuchsia profond, galons dorés.',
			'desc'  => "La pièce emblématique de la maison : un fuchsia profond structuré de larges galons dorés aux motifs géométriques. Traîne et ceinture bijou." ),
		array( 'name' => 'Caftan Safran', 'cat' => 'Signature', 'price' => 360, 'images' => array( 'safran-face.webp', 'safran-dos.webp' ),
			'short' => 'Orange safran, broderies dorées et pierres.',
			'desc'  => "La chaleur du safran, sublimée de broderies dorées en relief et de pierres vert profond. Un dos brodé spectaculaire." ),
		array( 'name' => 'Caftan Lilas', 'cat' => 'Intemporel', 'price' => 360, 'images' => array( 'lilas-face.webp', 'lilas-dos.webp' ),
			'short' => 'Crêpe lilas, broderies ton sur ton.',
			'desc'  => "Une silhouette épurée en crêpe lilas, soulignée de broderies ton sur ton et d'une ceinture en satin prune nouée au dos. L'élégance en toute délicatesse." ),
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
	update_post_meta( $id, '_jolya_asset', $file );
	update_post_meta( $id, '_jolya_asset_hash', md5_file( $upload['file'] ) );
	return $id;
}

/* ---------------------------------------------------------------
 * Mise à jour des photos importées quand le thème en fournit de meilleures.
 * Seules les photos créées par l'import du thème, et jamais retouchées dans
 * WordPress, sont remplacées. Les photos ajoutées par la cliente ne sont pas touchées.
 * ------------------------------------------------------------- */

/** Photos importées par le thème, avec le fichier du thème correspondant. */
function jolya_imported_photos() {
	$found = array();
	$ids   = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 200,
		'fields'         => 'ids',
		'meta_query'     => array( array( 'key' => '_wp_attached_file', 'value' => 'jolya-', 'compare' => 'LIKE' ) ),
	) );
	foreach ( $ids as $id ) {
		$path  = get_attached_file( $id );
		$asset = get_post_meta( $id, '_jolya_asset', true );
		if ( ! $asset && $path && preg_match( '/^jolya-([a-z-]+?)(?:-\d+)?\.webp$/', wp_basename( $path ), $m ) ) {
			$asset = $m[1] . '.webp';
		}
		$source = get_template_directory() . '/assets/images/' . $asset;
		if ( $asset && $path && file_exists( $path ) && file_exists( $source ) ) {
			$found[ $id ] = array( 'path' => $path, 'source' => $source );
		}
	}
	return $found;
}

/** Photos importées dont le thème fournit une version plus récente. */
function jolya_photos_to_update() {
	$todo = array();
	foreach ( jolya_imported_photos() as $id => $photo ) {
		$current = md5_file( $photo['path'] );
		$known   = get_post_meta( $id, '_jolya_asset_hash', true );
		// Fichier modifié depuis l'import (remplacé ou retouché) : on n'y touche pas.
		if ( $known && $known !== $current ) {
			continue;
		}
		if ( md5_file( $photo['source'] ) !== $current ) {
			$todo[ $id ] = $photo;
		}
	}
	return $todo;
}

function jolya_update_imported_photos() {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$count = 0;
	foreach ( jolya_photos_to_update() as $id => $photo ) {
		// Les anciennes miniatures sont supprimées avant d'être recréées.
		$meta = wp_get_attachment_metadata( $id );
		if ( ! empty( $meta['sizes'] ) ) {
			$dir = dirname( $photo['path'] );
			foreach ( $meta['sizes'] as $size ) {
				if ( ! empty( $size['file'] ) && file_exists( $dir . '/' . $size['file'] ) ) {
					wp_delete_file( $dir . '/' . $size['file'] );
				}
			}
		}
		if ( ! copy( $photo['source'], $photo['path'] ) ) {
			continue;
		}
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $photo['path'] ) );
		update_post_meta( $id, '_jolya_asset_hash', md5_file( $photo['path'] ) );
		$count++;
	}
	return $count;
}

add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['jolya_photos'] ) ) { // phpcs:ignore
		$n = absint( $_GET['jolya_photos'] ); // phpcs:ignore
		echo '<div class="notice notice-success is-dismissible"><p><strong>JOLYA CAFTAN :</strong> ' . esc_html( $n ) . ' photo(s) de la collection mise(s) à jour. Rechargez le site avec Ctrl + F5.</p></div>';
		return;
	}
	if ( get_option( 'jolya_photos_version' ) === JOLYA_VERSION ) {
		return;
	}
	$count = count( jolya_photos_to_update() );
	if ( ! $count ) {
		update_option( 'jolya_photos_version', JOLYA_VERSION );
		return;
	}
	$go   = wp_nonce_url( admin_url( 'admin-post.php?action=jolya_update_photos' ), 'jolya_photos' );
	$skip = wp_nonce_url( admin_url( 'admin-post.php?action=jolya_skip_photos' ), 'jolya_photos' );
	echo '<div class="notice notice-info"><p><strong>JOLYA CAFTAN :</strong> cette version du thème contient des photos de caftans plus nettes et plus naturelles. Mettre à jour les ' . esc_html( $count ) . ' photo(s) importée(s) avec la collection ? Les photos que vous avez ajoutées vous-même ne sont pas modifiées.</p><p><a class="button button-primary" href="' . esc_url( $go ) . '">Mettre à jour les photos</a> <a class="button" href="' . esc_url( $skip ) . '">Non merci</a></p></div>';
} );

add_action( 'admin_post_jolya_update_photos', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'jolya_photos' ) ) {
		wp_die( 'Action non autorisée.' );
	}
	$count = jolya_update_imported_photos();
	update_option( 'jolya_photos_version', JOLYA_VERSION );
	wp_safe_redirect( add_query_arg( 'jolya_photos', $count, admin_url() ) );
	exit;
} );

add_action( 'admin_post_jolya_skip_photos', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'jolya_photos' ) ) {
		wp_die( 'Action non autorisée.' );
	}
	update_option( 'jolya_photos_version', JOLYA_VERSION );
	wp_safe_redirect( admin_url() );
	exit;
} );

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

/* ---------------------------------------------------------------
 * Ordre de la collection : sombres ensemble, puis vifs, puis clairs.
 * Appliqué une fois aux caftans importés dont l'ordre n'a pas été modifié.
 * ------------------------------------------------------------- */
function jolya_reorder_collection() {
	if ( get_option( 'jolya_order_done' ) || ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	update_option( 'jolya_order_done', 1 );

	// Ordre à l'import d'origine (versions précédentes du thème).
	$old = array( 'Caftan Nuit Royale', 'Caftan Majorelle', 'Caftan Lilas', 'Caftan Grenat', 'Caftan Émeraude', 'Caftan Safran', 'Caftan Jade', 'Caftan Fuchsia Impérial' );
	$new = wp_list_pluck( jolya_demo_products(), 'name' );

	foreach ( $new as $position => $name ) {
		$found = get_posts( array(
			'post_type'   => 'product',
			'post_status' => 'any',
			'title'       => $name,
			'numberposts' => 1,
		) );
		if ( ! $found ) {
			continue;
		}
		$post  = $found[0];
		$index = array_search( $name, $old, true );
		// Ordre modifié à la main dans WordPress : on le respecte.
		if ( false === $index || (int) $post->menu_order !== (int) $index ) {
			continue;
		}
		wp_update_post( array( 'ID' => $post->ID, 'menu_order' => $position ) );
	}
}
add_action( 'admin_init', 'jolya_reorder_collection' );
