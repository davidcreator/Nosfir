<?php
/**
 * The template for displaying search results pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * Hook: nosfir_before_main_content
			 *
			 * @hooked nosfir_main_content_wrapper_start - 10
			 */
			do_action( 'nosfir_before_main_content' );

			if ( have_posts() ) :
				?>

				<?php
				/**
				 * Hook: nosfir_search_before
				 *
				 * @hooked nosfir_search_header - 10
				 * @hooked nosfir_search_filters - 20
				 */
				do_action( 'nosfir_search_before' );
				?>

				<?php
				/**
				 * Hook: nosfir_before_loop
				 *
				 * @hooked nosfir_posts_loop_wrapper_start - 10
				 */
				do_action( 'nosfir_before_loop' );

				get_template_part( 'loop' );

				/**
				 * Hook: nosfir_after_loop
				 *
				 * @hooked nosfir_posts_loop_wrapper_close - 10
				 * @hooked nosfir_paging_nav - 20
				 */
				do_action( 'nosfir_after_loop' );
				?>

				<?php
				/**
				 * Hook: nosfir_search_after
				 */
				do_action( 'nosfir_search_after' );

			else :

				/**
				 * Hook: nosfir_search_no_results
				 *
				 * @hooked nosfir_search_no_results_content - 10
				 */
				do_action( 'nosfir_search_no_results' );

				get_template_part( 'content', 'none' );

			endif;

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
/**
 * Hook: nosfir_sidebar
 *
 * @hooked nosfir_get_sidebar - 10
 */
do_action( 'nosfir_sidebar' );

get_footer();