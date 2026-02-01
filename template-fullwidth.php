<?php
/**
 * The template for displaying full width pages.
 *
 * Template Name: Full Width
 * Template Post Type: page, post
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

	<div id="primary" class="content-area full-width-content">
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * Hook: nosfir_before_main_content
			 *
			 * @hooked nosfir_main_content_wrapper_start - 10
			 */
			do_action( 'nosfir_before_main_content' );

			while ( have_posts() ) :
				the_post();

				/**
				 * Hook: nosfir_page_before
				 *
				 * @hooked nosfir_page_header - 10
				 */
				do_action( 'nosfir_page_before' );

				get_template_part( 'content', 'page' );

				/**
				 * Hook: nosfir_page_after
				 *
				 * @hooked nosfir_display_comments - 10
				 */
				do_action( 'nosfir_page_after' );

			endwhile; // End of the loop.

			/**
			 * Hook: nosfir_after_main_content
			 *
			 * @hooked nosfir_main_content_wrapper_close - 10
			 */
			do_action( 'nosfir_after_main_content' );
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
// Não inclui sidebar em páginas full width
get_footer();