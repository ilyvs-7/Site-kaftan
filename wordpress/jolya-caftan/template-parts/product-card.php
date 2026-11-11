<?php
/**
 * Carte produit. Attend $args['card'] et $args['index'].
 */
$card  = $args['card'];
$index = isset( $args['index'] ) ? (int) $args['index'] : 0;
?>
<article class="card<?php echo $card['sold_out'] ? ' card--sold-out' : ''; ?>" data-reveal style="--d: <?php echo esc_attr( ( $index % 3 ) * 0.12 ); ?>s">
	<a class="card__media" href="<?php echo esc_url( $card['url'] ); ?>" aria-label="<?php echo esc_attr( $card['name'] ); ?>">
		<?php if ( ! empty( $card['image_html'] ) ) : ?>
			<?php echo $card['image_html']; // phpcs:ignore ?>
		<?php else : ?>
			<img class="card__img" src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['name'] ); ?>" loading="lazy" decoding="async" width="720" height="1080">
		<?php endif; ?>
		<?php if ( $card['hover'] ) : ?>
			<img class="card__img card__img--hover" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="<?php echo esc_url( $card['hover'] ); ?>" alt="" loading="lazy" decoding="async" width="720" height="1080">
		<?php endif; ?>
		<?php if ( $card['sold_out'] ) : ?>
			<span class="card__badge">Épuisé</span>
		<?php endif; ?>
		<span class="card__cta">Découvrir <span aria-hidden="true">↗</span></span>
	</a>
	<div class="card__body">
		<div>
			<p class="card__cat"><?php echo esc_html( sprintf( '%02d', $index + 1 ) . ( $card['cat'] ? ' — ' . $card['cat'] : '' ) ); ?></p>
			<h3 class="card__title"><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['name'] ); ?></a></h3>
		</div>
		<p class="card__price"><?php echo wp_kses_post( $card['price'] ); ?></p>
	</div>
	<?php if ( $card['short'] ) : ?>
		<p class="card__text"><?php echo esc_html( $card['short'] ); ?></p>
	<?php endif; ?>
</article>
