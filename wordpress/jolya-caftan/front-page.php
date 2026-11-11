<?php
/**
 * Page d'accueil.
 */
get_header();

$cards    = jolya_home_cards( 8 );
$featured = jolya_featured_piece();
$shop_url = jolya_shop_url();
?>

<!-- COUVERTURE -->
<section class="hero" id="accueil">
	<div class="hero__media" data-parallax="0.18">
		<picture>
			<source media="(max-width: 760px)" srcset="<?php echo esc_url( jolya_image_opt( 'hero_image_mobile', 'fuchsia-face.webp' ) ); ?>">
			<img src="<?php echo esc_url( jolya_image_opt( 'hero_image', 'hero-fuchsia.webp' ) ); ?>" alt="Caftan brodé d'or présenté dans un décor de marbre baigné de lumière" fetchpriority="high">
		</picture>
	</div>
	<div class="hero__veil"></div>

	<div class="hero__content">
		<p class="eyebrow hero__eyebrow" data-hero="1"><?php echo esc_html( jolya_opt( 'hero_eyebrow' ) ); ?></p>
		<h1 class="hero__title">
			<span class="line"><span data-hero="2"><?php echo esc_html( jolya_opt( 'hero_title' ) ); ?></span></span>
			<span class="line line--italic"><span data-hero="3"><?php echo esc_html( jolya_opt( 'hero_title_2' ) ); ?></span></span>
		</h1>
		<p class="hero__text" data-hero="4"><?php echo esc_html( jolya_opt( 'hero_text' ) ); ?></p>
		<div class="hero__actions" data-hero="5">
			<a class="btn btn--light" href="#collection"><span>Découvrir la collection</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
		</div>
	</div>

	<a class="hero__scroll" href="#manifeste" aria-label="Faire défiler">
		<span>Défiler</span><i></i>
	</a>
</section>

<!-- BANDEAU DÉFILANT -->
<div class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<?php for ( $i = 0; $i < 4; $i++ ) : ?>
			<span>Fait main</span><b>✦</b><span>Broderies d'or</span><b>✦</b><span>Pièces d'exception</span><b>✦</b><span>Sur mesure</span><b>✦</b><span>Savoir-faire marocain</span><b>✦</b>
		<?php endfor; ?>
	</div>
</div>

<!-- MANIFESTE -->
<section class="manifesto" id="manifeste">
	<p class="eyebrow" data-reveal>L'art du caftan</p>
	<p class="manifesto__text" data-words><?php echo esc_html( jolya_opt( 'manifesto' ) ); ?></p>
</section>

<!-- COLLECTION -->
<section class="collection" id="collection">
	<header class="section-head">
		<div>
			<p class="eyebrow" data-reveal>Une sélection particulière</p>
			<h2 class="section-title" data-reveal>Des pièces uniques.<br><em>Une allure singulière.</em></h2>
		</div>
		<p class="section-head__text" data-reveal>Des créations raffinées, pensées pour accompagner les moments qui comptent.</p>
	</header>

	<div class="cards">
		<?php
		foreach ( $cards as $i => $card ) {
			get_template_part( 'template-parts/product-card', null, array( 'card' => $card, 'index' => $i ) );
		}
		?>
	</div>

	<div class="collection__more" data-reveal>
		<a class="btn btn--line" href="<?php echo esc_url( $shop_url ); ?>"><span>Voir toute la collection</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
	</div>
</section>

<!-- PIÈCE SIGNATURE -->
<section class="feature">
	<div class="feature__media" data-reveal="curtain">
		<img src="<?php echo esc_url( $featured['image'] ); ?>" alt="<?php echo esc_attr( $featured['name'] ); ?>" loading="lazy" data-parallax="0.08">
	</div>
	<div class="feature__content">
		<p class="eyebrow" data-reveal>La pièce signature</p>
		<h2 class="section-title" data-reveal><?php echo esc_html( $featured['name'] ); ?></h2>
		<p class="feature__text" data-reveal><?php echo esc_html( wp_trim_words( $featured['text'], 45 ) ); ?></p>
		<p class="feature__price" data-reveal><?php echo wp_kses_post( $featured['price'] ); ?></p>
		<div data-reveal>
			<a class="btn btn--gold" href="<?php echo esc_url( $featured['url'] ); ?>"><span>Découvrir cette pièce</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
		</div>
	</div>
</section>

<!-- SAVOIR-FAIRE -->
<section class="craft" id="savoir-faire">
	<header class="section-head section-head--center">
		<div>
			<p class="eyebrow" data-reveal>Le savoir-faire</p>
			<h2 class="section-title" data-reveal>Le geste, <em>la matière, le temps.</em></h2>
		</div>
	</header>

	<div class="craft__grid">
		<article class="craft__item" data-reveal style="--d: 0s">
			<span class="craft__num">01</span>
			<h3>Broderie à la main</h3>
			<p>Sfifa, aâkad et fils d'or : chaque motif est tracé puis brodé par des artisans, point après point.</p>
		</article>
		<article class="craft__item" data-reveal style="--d: .12s">
			<span class="craft__num">02</span>
			<h3>Étoffes nobles</h3>
			<p>Velours, crêpe et soieries sélectionnés pour leur tombé, leur éclat et leur confort au fil des heures.</p>
		</article>
		<article class="craft__item" data-reveal style="--d: .24s">
			<span class="craft__num">03</span>
			<h3>Sur mesure</h3>
			<p>Longueur, manches, ceinture : chaque pièce peut être ajustée à vos mesures et à votre occasion.</p>
		</article>
	</div>
</section>

<!-- LA MAISON -->
<section class="story" id="maison">
	<div class="story__media" data-reveal="arch">
		<img src="<?php echo esc_url( jolya_image_opt( 'story_image', 'maison.webp' ) ); ?>" alt="Architecture marocaine ciselée, inspiration de la maison JOLYA CAFTAN" loading="lazy" data-parallax="0.1">
	</div>
	<div class="story__content">
		<p class="eyebrow" data-reveal>L'esprit JOLYA</p>
		<h2 class="section-title" data-reveal><?php echo esc_html( jolya_opt( 'story_title' ) ); ?><br><em><?php echo esc_html( jolya_opt( 'story_title_2' ) ); ?></em></h2>
		<p class="story__text" data-reveal><?php echo esc_html( jolya_opt( 'story_text' ) ); ?></p>
		<div data-reveal>
			<a class="btn btn--line" href="#contact"><span>Nous rencontrer</span><span class="btn__arrow" aria-hidden="true">↗</span></a>
		</div>
	</div>
</section>

<!-- CONTACT -->
<section class="contact" id="contact">
	<div class="contact__ornament" data-parallax="0.12" style="background-image: url('<?php echo esc_url( jolya_asset( 'ornement.webp' ) ); ?>')"></div>
	<div class="contact__inner">
		<p class="eyebrow" data-reveal>Restons en contact</p>
		<h2 class="section-title" data-reveal><?php echo esc_html( jolya_opt( 'contact_title' ) ); ?><br><em>Parlons-en.</em></h2>
		<p class="contact__text" data-reveal><?php echo esc_html( jolya_opt( 'contact_text' ) ); ?></p>
		<div class="contact__links" data-reveal>
			<?php jolya_contact_links( 'btn btn--dark' ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
