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
		// Avec srcset : le téléphone reçoit une photo plus petite que l'ordinateur.
		'image_html' => $product->get_image_id() ? wp_get_attachment_image( $product->get_image_id(), 'woocommerce_thumbnail', false, array(
			'class'    => 'card__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => $product->get_name(),
			'sizes'    => '(max-width: 1100px) 50vw, 25vw',
		) ) : '',
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

// Vignettes verticales (2:3) : sans cela WooCommerce recadre les photos en carré.
add_filter( 'woocommerce_get_image_size_thumbnail', function () {
	return array( 'width' => 720, 'height' => 1080, 'crop' => 1 );
} );

add_filter( 'loop_shop_columns', function () {
	return 4;
} );
add_filter( 'loop_shop_per_page', function () {
	return 12;
} );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

// ---------- Catalogue ----------

add_filter( 'woocommerce_page_title', function ( $title ) {
	return function_exists( 'is_shop' ) && is_shop() && ! is_search() ? 'La collection' : $title;
} );

add_filter( 'woocommerce_breadcrumb_defaults', function ( $args ) {
	$args['delimiter'] = ' <span aria-hidden="true">/</span> ';
	$args['home']      = 'Accueil';
	return $args;
} );

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

/** Filtres par catégorie au-dessus de la grille. */
function jolya_shop_filters() {
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	) );
	if ( is_wp_error( $terms ) || count( $terms ) < 2 ) {
		return;
	}
	$current = is_product_category() ? get_queried_object_id() : 0;
	echo '<nav class="shop-filters" aria-label="Catégories">';
	printf( '<a class="chip%s" href="%s">Tout voir</a>', $current ? '' : ' is-active', esc_url( jolya_shop_url() ) );
	foreach ( $terms as $term ) {
		printf(
			'<a class="chip%s" href="%s">%s</a>',
			$current === (int) $term->term_id ? ' is-active' : '',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}
	echo '</nav>';
}
add_action( 'woocommerce_before_shop_loop', 'jolya_shop_filters', 20 );

// ---------- Fiche caftan ----------

// Les onglets WooCommerce sont remplacés par des volets dépliables dans la colonne de droite.
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

/** Volets : description, tailles, livraison, entretien. */
function jolya_product_accordions( $description = null ) {
	if ( null === $description ) {
		$description = apply_filters( 'the_content', get_post_field( 'post_content', get_the_ID() ) );
	}
	$panels = array(
		'Description'          => $description,
		'Tailles & sur-mesure' => wpautop( esc_html( jolya_opt( 'product_sizes' ) ) ),
		'Livraison & retours'  => wpautop( esc_html( jolya_opt( 'product_shipping' ) ) ),
		'Entretien'            => wpautop( esc_html( jolya_opt( 'product_care' ) ) ),
	);
	$links = array(
		'Tailles & sur-mesure' => array( 'guide-des-tailles', 'Voir le guide des tailles' ),
		'Livraison & retours'  => array( 'livraison-retours', 'En savoir plus' ),
	);

	echo '<div class="accordions">';
	$first = true;
	foreach ( $panels as $title => $html ) {
		if ( ! trim( wp_strip_all_tags( (string) $html ) ) ) {
			continue;
		}
		$more = '';
		if ( isset( $links[ $title ] ) ) {
			$url = jolya_page_url( $links[ $title ][0] );
			if ( $url ) {
				$more = sprintf( '<p><a class="accordion__link" href="%s">%s</a></p>', esc_url( $url ), esc_html( $links[ $title ][1] ) );
			}
		}
		printf(
			'<details class="accordion"%s><summary>%s</summary><div class="accordion__body">%s%s</div></details>',
			$first ? ' open' : '',
			esc_html( $title ),
			wp_kses_post( $html ),
			$more // phpcs:ignore
		);
		$first = false;
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'jolya_product_accordions', 45 );

add_filter( 'woocommerce_product_related_products_heading', function () {
	return 'Vous aimerez aussi';
} );

// Message de réassurance sous le bouton d'achat.
function jolya_product_assurances() {
	$custom = jolya_page_url( 'sur-mesure' );
	echo '<ul class="product-assurances"><li>Broderies réalisées à la main</li><li>Paiement sécurisé</li><li>' . ( $custom ? '<a href="' . esc_url( $custom ) . '">Retouches et sur-mesure sur demande</a>' : 'Retouches et sur-mesure sur demande' ) . '</li></ul>';
}
add_action( 'woocommerce_single_product_summary', 'jolya_product_assurances', 35 );
