<?php
/**
 * Gabarit des pages (La maison, Sur mesure, Contact, pages d'information…).
 */
get_header();

// Photo d'en-tête : image mise en avant de la page, sinon image du thème selon la page.
$jolya_hero_fallbacks = array(
	'la-maison'  => 'maison.webp',
	'sur-mesure' => 'majorelle-face.webp',
);

while ( have_posts() ) :
	the_post();

	$jolya_slug  = get_post_field( 'post_name', get_the_ID() );
	$jolya_image = '';
	if ( has_post_thumbnail() ) {
		$jolya_image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	} elseif ( isset( $jolya_hero_fallbacks[ $jolya_slug ] ) ) {
		$jolya_image = jolya_asset( $jolya_hero_fallbacks[ $jolya_slug ] );
	}
	$jolya_lead     = has_excerpt() ? get_the_excerpt() : '';
	$jolya_is_shop  = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
	$jolya_is_wide  = $jolya_is_shop || 'contact' === $jolya_slug;
	?>

	<article <?php post_class( 'page' . ( $jolya_image ? ' page--visual' : '' ) . ( $jolya_is_wide ? ' page--wide' : '' ) ); ?>>
		<header class="page-hero<?php echo $jolya_image ? ' page-hero--split' : ''; ?>">
			<div class="page-hero__text">
				<p class="eyebrow" data-reveal>JOLYA CAFTAN</p>
				<h1 class="page__title" data-reveal><?php the_title(); ?></h1>
				<?php if ( $jolya_lead ) : ?>
					<p class="page-hero__lead" data-reveal><?php echo esc_html( $jolya_lead ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $jolya_image ) : ?>
				<div class="page-hero__media" data-reveal="arch">
					<img src="<?php echo esc_url( $jolya_image ); ?>" alt="" data-parallax="0.08">
				</div>
			<?php endif; ?>
		</header>

		<div class="prose<?php echo $jolya_is_wide ? ' prose--wide' : ''; ?><?php echo $jolya_is_shop ? '' : ' prose--rich'; ?>">
			<?php the_content(); ?>
		</div>
	</article>

	<?php if ( ! $jolya_is_shop && 'contact' !== $jolya_slug && 'sur-mesure' !== $jolya_slug ) : ?>
		<section class="cta-band">
			<div class="cta-band__ornament" style="background-image: url('<?php echo esc_url( jolya_asset( 'ornement.webp' ) ); ?>')"></div>
			<div class="cta-band__inner">
				<p class="eyebrow" data-reveal>À votre écoute</p>
				<h2 class="section-title" data-reveal>Une question ?<br><em>Écrivez-nous.</em></h2>
				<div class="contact__links" data-reveal>
					<a class="btn btn--dark" href="<?php echo esc_url( jolya_page_url( 'contact', home_url( '/#contact' ) ) ); ?>"><span>Nous contacter</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
					<a class="btn btn--line" href="<?php echo esc_url( jolya_shop_url() ); ?>"><span>Voir la collection</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
				</div>
			</div>
		</section>
	<?php endif; ?>

<?php
endwhile;

get_footer();
