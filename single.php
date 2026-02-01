<?php
/**
 * The template for displaying all single posts.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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

			while ( have_posts() ) :
				the_post();

				/**
				 * Hook: nosfir_single_post_before
				 *
				 * @hooked nosfir_single_post_header - 10
				 */
				do_action( 'nosfir_single_post_before' );

				get_template_part( 'content', 'single' );

				/**
				 * Hook: nosfir_single_post_after
				 *
				 * @hooked nosfir_post_author_box    - 10
				 * @hooked nosfir_post_navigation    - 20
				 * @hooked nosfir_related_posts      - 30
				 * @hooked nosfir_display_comments   - 40
				 */
				do_action( 'nosfir_single_post_after' );

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
/**
 * Hook: nosfir_sidebar
 *
 * @hooked nosfir_get_sidebar - 10
 */
do_action( 'nosfir_sidebar' );

get_footer();