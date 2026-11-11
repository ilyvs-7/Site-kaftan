</main>

<footer class="site-footer">
	<div class="site-footer__top">
		<div class="site-footer__intro">
			<p class="eyebrow">JOLYA CAFTAN</p>
			<p class="site-footer__tagline">L'élégance<br><em>des instants précieux.</em></p>
		</div>

		<nav class="site-footer__nav" aria-label="Navigation de pied de page">
			<p class="eyebrow">Explorer</p>
			<?php jolya_menu(); ?>
		</nav>

		<?php ob_start(); ?>
				<?php if ( jolya_has_shop() ) : ?>
					<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>">Panier</a></li>
					<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Mon compte</a></li>
				<?php endif; ?>
				<?php
				foreach ( array( 'guide-des-tailles', 'livraison-retours', 'questions-frequentes', 'cgv', 'mentions-legales', 'politique-de-confidentialite' ) as $slug ) {
					$page = get_page_by_path( $slug );
					if ( $page && 'publish' === $page->post_status ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( get_the_title( $page ) ) );
					}
				}
				?>
			<?php $jolya_info = trim( ob_get_clean() ); ?>
		<?php if ( $jolya_info ) : ?>
		<div class="site-footer__nav">
			<p class="eyebrow">Informations</p>
			<ul class="menu"><?php echo $jolya_info; // phpcs:ignore ?></ul>
		</div>
		<?php endif; ?>

		<div class="site-footer__nav">
			<p class="eyebrow">Contact</p>
			<ul class="menu">
				<?php if ( sanitize_email( jolya_opt( 'email' ) ) ) : ?>
					<li><a href="mailto:<?php echo esc_attr( antispambot( sanitize_email( jolya_opt( 'email' ) ) ) ); ?>"><?php echo esc_html( antispambot( sanitize_email( jolya_opt( 'email' ) ) ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( jolya_opt( 'instagram' ) ) : ?>
					<li><a href="<?php echo esc_url( jolya_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener">Instagram</a></li>
				<?php endif; ?>
				<?php $jolya_phone = preg_replace( '/\D+/', '', (string) jolya_opt( 'whatsapp' ) ); if ( $jolya_phone ) : ?>
					<li><a href="https://wa.me/<?php echo esc_attr( $jolya_phone ); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<p class="site-footer__wordmark" aria-hidden="true">JOLYA</p>

	<div class="site-footer__bottom">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> JOLYA CAFTAN — Tous droits réservés</span>
		<a href="#contenu" data-scroll-top>Retour en haut ↑</a>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
