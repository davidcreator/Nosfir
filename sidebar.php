<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Não exibe se não houver widgets ativos
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

/**
 * Hook: nosfir_before_sidebar
 *
 * @hooked nosfir_sidebar_wrapper_start - 10
 */
do_action( 'nosfir_before_sidebar' );
?>

<aside id="secondary" class="widget-area sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Primary Sidebar', 'nosfir' ); ?>">

	<?php
	/**
	 * Hook: nosfir_sidebar_top
	 *
	 * @hooked nosfir_sidebar_search - 10
	 */
	do_action( 'nosfir_sidebar_top' );

	/**
	 * Main sidebar widgets
	 */
	dynamic_sidebar( 'sidebar-1' );

	/**
	 * Hook: nosfir_sidebar_bottom
	 *
	 * @hooked nosfir_sidebar_cta - 10
	 */
	do_action( 'nosfir_sidebar_bottom' );
	?>

</aside><!-- #secondary -->

<?php
/**
 * Hook: nosfir_after_sidebar
 *
 * @hooked nosfir_sidebar_wrapper_close - 10
 */
do_action( 'nosfir_after_sidebar' );