<?php
/**
 * Intégration WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jolya_has_shop() {
	return class_exists( 'WooCommerce' );
}

/** Transforme un produit WooCommerce en carte. */
function jolya_card_from_product( $product ) {
	$cat   = '';
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$default = (int) get_option( 'default_product_cat' );
		foreach ( $terms as $term ) {
			if ( (int) $term->term_id !== $default ) {
				$cat = $term->name;
				break;
			}
		}
	}
	$gallery = $product->get_gallery_image_ids();
	$hover   = '';
	foreach ( $gallery as $gid ) {
		$meta = wp_get_attachment_metadata( $gid );
		// Seules les photos verticales servent d'image au survol.
		if ( $meta && ! empty( $meta['width'] ) && $meta['height'] > $meta['width'] ) {
			$hover = wp_get_attachment_image_url( $gid, 'woocommerce_thumbnail' );
			break;
		}
	}
	return array(
		'name'     => $product->get_name(),
		'cat'      => $cat,
		'short'    => wp_strip_all_tags( $product->get_short_description() ),
		'price'    => $product->get_price_html(),
		'url'      => $product->get_permalink(),
		'image'    => $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' ),
		'hover'    => $hover,
		'sold_out' => ! $product->is_in_stock(),
	);
}

/** Cartes de la page d'accueil : produits réels, sinon aperçu du thème. */
function jolya_home_cards( $limit = 8 ) {
	if ( jolya_has_shop() ) {
		$products = wc_get_products( array(
			'status'     => 'publish',
			'limit'      => $limit,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
			'visibility' => 'catalog',
		) );
		if ( $products ) {
			return array_map( 'jolya_card_from_product', $products );
		}
	}
	return jolya_demo_cards();
}

/** Pièce mise en avant : produit « en vedette » (étoile), sinon le premier. */
function jolya_featured_piece() {
	if ( jolya_has_shop() ) {
		$found = wc_get_products( array( 'status' => 'publish', 'limit' => 1, 'featured' => true ) );
		if ( ! $found ) {
			$found = wc_get_products( array( 'status' => 'publish', 'limit' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
		}
		if ( $found ) {
			$p = $found[0];
			return array(
				'name'  => $p->get_name(),
				'text'  => wp_strip_all_tags( $p->get_description() ? $p->get_description() : $p->get_short_description() ),
				'price' => $p->get_price_html(),
				'url'   => $p->get_permalink(),
				'image' => $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'large' ) : jolya_asset( 'nuit-face.webp' ),
			);
		}
	}
	$demo = jolya_demo_products();
	return array(
		'name'  => $demo[0]['name'],
		'text'  => $demo[0]['desc'],
		'price' => number_format_i18n( $demo[0]['price'] ) . ' €',
		'url'   => home_url( '/#contact' ),
		'image' => jolya_asset( $demo[0]['images'][0] ),
	);
}

function jolya_shop_url() {
	if ( jolya_has_shop() && wc_get_page_id( 'shop' ) > 0 ) {
		return wc_get_page_permalink( 'shop' );
	}
	return home_url( '/#collection' );
}

/** Lien panier de l'en-tête (mis à jour sans recharger la page). */
function jolya_cart_link() {
	if ( ! jolya_has_shop() || ! WC()->cart ) {
		return '';
	}
	$count = WC()->cart->get_cart_contents_count();
	return sprintf(
		'<a class="header-cart" href="%s" aria-label="Panier, %d article(s)">Panier <span class="header-cart__count">%d</span></a>',
		esc_url( wc_get_cart_url() ),
		$count,
		$count
	);
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['a.header-cart'] = jolya_cart_link();
	return $fragments;
} );

// Mise en page boutique : conteneur du thème, pas de colonne latérale.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_filter( 'loop_shop_columns', function () {
	return 3;
} );
add_filter( 'loop_shop_per_page', function () {
	return 12;
} );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
} );

// Les avis ne sont pas affichés par défaut sur une boutique de pièces uniques.
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	unset( $tabs['additional_information'] );
	return $tabs;
}, 98 );

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );

// Message de réassurance sous le bouton d'achat.
add_action( 'woocommerce_single_product_summary', function () {
	echo '<ul class="product-assurances"><li>Broderies réalisées à la main</li><li>Retouches et sur-mesure sur demande</li><li>Paiement sécurisé</li></ul>';
}, 35 );
