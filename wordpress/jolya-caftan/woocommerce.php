<?php
/**
 * Gabarit des pages boutique (catalogue, fiche produit).
 */
get_header();
$GLOBALS['jolya_loop_index'] = 0;
?>
<div class="shop<?php echo is_product() ? ' shop--single' : ' shop--archive'; ?>">
	<?php woocommerce_content(); ?>
</div>
<?php
get_footer();
