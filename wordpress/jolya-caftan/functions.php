<?php
/**
 * Jolya Caftan — fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JOLYA_VERSION', '1.0.0' );

require get_template_directory() . '/inc/demo.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/woocommerce.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 300, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 720,
		'single_image_width'    => 1100,
		'product_grid'          => array( 'default_columns' => 3, 'min_columns' => 2, 'max_columns' => 4 ),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

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
	$items = array(
		'Collection'   => $shop,
		'Savoir-faire' => $home . '#savoir-faire',
		'La maison'    => $home . '#maison',
		'Contact'      => $home . '#contact',
	);
	echo '<ul class="menu">';
	foreach ( $items as $label => $url ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
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
