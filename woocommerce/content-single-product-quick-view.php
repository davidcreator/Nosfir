<?php
/**
 * The template for displaying product content in the quick view modal
 *
 * @package Nosfir
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product', $product ); ?>>
    <div class="nosfir-quick-view-images">
        <?php echo $product->get_image( 'woocommerce_single' ); ?>
    </div>
    <div class="nosfir-quick-view-summary entry-summary">
        <h1 class="product_title entry-title"><?php the_title(); ?></h1>
        <p class="price"><?php echo $product->get_price_html(); ?></p>
        <div class="woocommerce-product-details__short-description">
            <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); ?>
        </div>
        
        <?php if ( $product->is_in_stock() ) : ?>
            <?php woocommerce_template_single_add_to_cart(); ?>
        <?php endif; ?>
        
        <div class="product_meta">
            <?php do_action( 'woocommerce_product_meta_start' ); ?>
            <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
            <?php endif; ?>
            <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
            <?php do_action( 'woocommerce_product_meta_end' ); ?>
        </div>
    </div>
</div>
