<?php
/**
 * Gabarit générique (pages, articles, panier, commande, compte).
 */
get_header();
?>
<div class="page">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'page__article' ); ?>>
				<header class="page__head">
					<?php if ( is_singular() ) : ?>
						<h1 class="page__title"><?php the_title(); ?></h1>
					<?php else : ?>
						<h2 class="page__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php endif; ?>
				</header>
				<div class="page__content">
					<?php
					if ( is_singular() ) {
						the_content();
					} else {
						the_excerpt();
					}
					?>
				</div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<header class="page__head"><h1 class="page__title">Aucun contenu</h1></header>
	<?php endif; ?>
</div>
<?php
get_footer();
