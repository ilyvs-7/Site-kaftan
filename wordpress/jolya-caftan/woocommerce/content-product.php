<?php
/**
 * Carte produit dans la boutique (remplace le modèle WooCommerce).
 *
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $woocommerce_loop;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$jolya_index = isset( $GLOBALS['jolya_loop_index'] ) ? (int) $GLOBALS['jolya_loop_index'] : 0;
$GLOBALS['jolya_loop_index'] = $jolya_index + 1;
?>
<li <?php wc_product_class( 'card-item', $product ); ?>>
	<?php get_template_part( 'template-parts/product-card', null, array( 'card' => jolya_card_from_product( $product ), 'index' => $jolya_index ) ); ?>
</li>
