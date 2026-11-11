<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#contenu">Aller au contenu</a>

<?php if ( is_front_page() ) : ?>
<div class="preloader" aria-hidden="true">
	<div class="preloader__inner">
		<span class="preloader__brand">JOLYA</span>
		<span class="preloader__line"></span>
		<span class="preloader__sub">Maison de caftans</span>
	</div>
</div>
<?php endif; ?>

<!-- Fond animé : halos de lumière et poussière d'or -->
<div class="ambient" aria-hidden="true">
	<span class="ambient__halo ambient__halo--1"></span>
	<span class="ambient__halo ambient__halo--2"></span>
	<span class="ambient__halo ambient__halo--3"></span>
	<canvas class="ambient__dust"></canvas>
</div>

<header class="site-header<?php echo is_front_page() ? ' site-header--over' : ''; ?>">
	<?php if ( jolya_opt( 'announcement' ) ) : ?>
		<p class="announcement"><?php echo esc_html( jolya_opt( 'announcement' ) ); ?></p>
	<?php endif; ?>

	<div class="site-header__bar">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>, accueil">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'brand__logo' ) );
			} else {
				echo 'JOLYA <span>CAFTAN</span>';
			}
			?>
		</a>

		<nav class="site-nav" aria-label="Navigation principale">
			<?php jolya_menu(); ?>
		</nav>

		<div class="site-header__actions">
			<?php echo jolya_cart_link(); // phpcs:ignore ?>
			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-mobile">
				<span class="menu-toggle__label">Menu</span>
				<span class="menu-toggle__icon" aria-hidden="true"><i></i><i></i></span>
			</button>
		</div>
	</div>
</header>

<div class="mobile-menu" id="menu-mobile" hidden>
	<nav aria-label="Menu mobile">
		<?php jolya_menu(); ?>
	</nav>
	<p class="mobile-menu__note">L'élégance des instants précieux.</p>
</div>

<main id="contenu">
