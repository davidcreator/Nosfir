<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Don't show sidebar on full-width templates
if ( is_page_template( 'template-fullwidth.php' ) ) {
    return;
}

// Check if sidebar is active
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'nosfir' ); ?>">
    
    <?php do_action( 'nosfir_before_sidebar' ); ?>
    
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
    
    <?php do_action( 'nosfir_after_sidebar' ); ?>
    
</aside><!-- #secondary -->