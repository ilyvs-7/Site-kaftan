<?php
/**
 * Jolya Caftan — fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JOLYA_VERSION', '2.0.2' );

require get_template_directory() . '/inc/demo.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/pages.php';
require get_template_directory() . '/inc/contact.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 300, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 720,
		'single_image_width'    => 1100,
		'product_grid'          => array( 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 4 ),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );

	// L'extrait d'une page sert de phrase d'introduction sous son titre.
	add_post_type_support( 'page', 'excerpt' );

	register_nav_menus( array( 'primary' => 'Menu principal' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'jolya-main', $uri . '/assets/css/main.css', array(), JOLYA_VERSION );
	wp_enqueue_script( 'jolya-main', $uri . '/assets/js/main.js', array(), JOLYA_VERSION, true );
}, 20 );

// Précharge la police des titres pour éviter un saut à l'affichage.
add_action( 'wp_head', function () {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_template_directory_uri() . '/assets/fonts/cormorant-garamond-latin-400-normal.woff2' )
	);
	echo '<meta name="theme-color" content="#f6f1e8">' . "\n";
}, 2 );

// Sans JavaScript, tout le contenu reste visible.
add_action( 'wp_head', function () {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}, 1 );

/** Adresse d'une image fournie avec le thème. */
function jolya_asset( $file ) {
	return get_template_directory_uri() . '/assets/images/' . $file;
}

/** Valeur d'un réglage du thème (Apparence → Personnaliser). */
function jolya_opt( $key ) {
	$defaults = jolya_defaults();
	$value    = get_theme_mod( 'jolya_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
	return is_string( $value ) ? trim( $value ) : $value;
}

/** Image réglable : photo choisie dans le Customizer, sinon image du thème. */
function jolya_image_opt( $key, $fallback ) {
	$id = absint( get_theme_mod( 'jolya_' . $key ) );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return jolya_asset( $fallback );
}

/** Liens du menu par défaut (si aucun menu n'est créé dans WordPress). */
function jolya_default_menu() {
	$home = home_url( '/' );
	$shop = function_exists( 'wc_get_page_permalink' ) && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : $home . '#collection';
	// Vente et Location : deux boutons bien distincts.
	$in_shop = function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() || is_product() );
	$items   = array(
		array( 'Vente', $shop, 'menu-item--pill', $in_shop ),
		array( 'Location', jolya_page_url( 'location', $home . '#contact' ), 'menu-item--pill', is_page( 'location' ) ),
		array( 'Contact', jolya_page_url( 'contact', $home . '#contact' ), '', is_page( 'contact' ) ),
	);
	echo '<ul class="menu">';
	foreach ( $items as $item ) {
		printf(
			'<li class="%s"><a href="%s"%s>%s</a></li>',
			esc_attr( trim( $item[2] . ( $item[3] ? ' is-current' : '' ) ) ),
			esc_url( $item[1] ),
			$item[3] ? ' aria-current="page"' : '',
			esc_html( $item[0] )
		);
	}
	echo '</ul>';
}

function jolya_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu', 'depth' => 1 ) );
	} else {
		jolya_default_menu();
	}
}

/** Liens de contact (e-mail, WhatsApp, Instagram). */
function jolya_contact_links( $class = 'btn btn--line' ) {
	$email = sanitize_email( jolya_opt( 'email' ) );
	$phone = preg_replace( '/\D+/', '', (string) jolya_opt( 'whatsapp' ) );
	$insta = esc_url( jolya_opt( 'instagram' ) );
	$arrow = '<span class="btn__arrow" aria-hidden="true">↗</span>';

	if ( $email ) {
		printf( '<a class="%s" href="mailto:%s"><span>Nous écrire</span>%s</a>', esc_attr( $class ), esc_attr( antispambot( $email ) ), $arrow );
	}
	if ( $phone ) {
		printf( '<a class="%s" href="https://wa.me/%s" target="_blank" rel="noopener"><span>WhatsApp</span>%s</a>', esc_attr( $class ), esc_attr( $phone ), $arrow );
	}
	if ( $insta ) {
		printf( '<a class="%s" href="%s" target="_blank" rel="noopener"><span>Instagram</span>%s</a>', esc_attr( $class ), $insta, $arrow );
	}
}

/* ---------------------------------------------------------------
 * Partage sur les réseaux (WhatsApp, Instagram, Facebook) et icône d'onglet.
 * Désactivé automatiquement si une extension de référencement s'en charge.
 * ------------------------------------------------------------- */

function jolya_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

add_action( 'wp_head', function () {
	if ( jolya_seo_plugin_active() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = '';
	$image       = '';
	$type        = 'website';

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product     = wc_get_product( get_queried_object_id() );
		$description = $product ? $product->get_short_description() : '';
		$image       = $product && $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'large' ) : '';
		$type        = 'product';
	} elseif ( is_singular() ) {
		$post        = get_queried_object();
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30 );
		$image       = get_the_post_thumbnail_url( $post, 'large' );
	}

	if ( is_front_page() || ! $description ) {
		$description = jolya_opt( 'hero_text' );
	}
	if ( ! $image ) {
		$image = jolya_image_opt( 'hero_image', 'hero-dore.webp' );
	}

	$description = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $description ) ) );
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="fr_FR">' . "\n" );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}, 3 );

// Icône d'onglet par défaut (un « J » doré), tant qu'aucune icône n'est choisie
// dans Apparence → Personnaliser → Identité du site.
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( jolya_asset( 'favicon.svg' ) ) );
	}
}, 4 );

/** Bouton WhatsApp flottant (affiché si un numéro est renseigné). */
add_action( 'wp_footer', function () {
	$phone = preg_replace( '/\D+/', '', (string) jolya_opt( 'whatsapp' ) );
	if ( ! $phone ) {
		return;
	}
	$text = rawurlencode( 'Bonjour, je vous contacte depuis le site JOLYA CAFTAN.' );
	printf(
		'<a class="wa-float" href="https://wa.me/%1$s?text=%2$s" target="_blank" rel="noopener" aria-label="Nous écrire sur WhatsApp"><svg viewBox="0 0 32 32" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 3C9 3 3.3 8.6 3.3 15.6c0 2.4.7 4.7 1.9 6.7L3.2 29l6.9-1.8c1.9 1 4 1.6 6 1.6 7 0 12.7-5.7 12.7-12.7S23 3 16 3zm0 23.4c-1.9 0-3.8-.5-5.4-1.5l-.4-.2-4.1 1.1 1.1-4-.3-.4c-1.1-1.7-1.6-3.6-1.6-5.6C5.3 9.8 10.1 5 16 5s10.7 4.8 10.7 10.7S21.9 26.4 16 26.4zm5.9-8c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-1 1.2-.2.2-.4.2-.7.1-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.2-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.3.3-.6.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.8s1.2 3.2 1.4 3.5c.2.2 2.4 3.6 5.7 5 .8.3 1.4.5 1.9.7.8.3 1.5.2 2.1.1.6-.1 1.9-.8 2.2-1.5.3-.7.3-1.4.2-1.5-.1-.2-.3-.3-.7-.5z"/></svg></a>',
		esc_attr( $phone ),
		esc_attr( $text )
	);
} );

/* ---------------------------------------------------------------
 * Signature visuelle : monogramme et filet orné.
 * Le monogramme : un « J » sous une arche marocaine.
 * ------------------------------------------------------------- */

function jolya_monogram( $class = '' ) {
	return '<svg class="monogram ' . esc_attr( $class ) . '" viewBox="0 0 100 100" aria-hidden="true" focusable="false">'
		. '<path class="monogram__arch" d="M24 92V50C18 36 26 16 50 6C74 16 82 36 76 50V92" pathLength="1"/>'
		. '<path class="monogram__arch monogram__arch--inner" d="M31 92V53C27 42 33 26 50 18C67 26 73 42 69 53V92" pathLength="1"/>'
		. '<path class="monogram__base" d="M18 92H82" pathLength="1"/>'
		. '<text class="monogram__letter" x="50" y="73" text-anchor="middle">J</text>'
		. '</svg>';
}

/** Filet doré avec une petite étoile au centre (séparateur de sections). */
function jolya_ornament( $class = '' ) {
	return '<div class="ornament ' . esc_attr( $class ) . '" aria-hidden="true"><span class="ornament__line"></span>'
		. '<svg class="ornament__star" viewBox="0 0 20 20"><rect x="6" y="6" width="8" height="8" transform="rotate(45 10 10)"/></svg>'
		. '<span class="ornament__line"></span></div>';
}
