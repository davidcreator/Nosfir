<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
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
				 * @hooked nosfir_paging_nav                - 20
				 */
				do_action( 'nosfir_after_loop' );

			else :

				/**
				 * Hook: nosfir_no_posts
				 */
				do_action( 'nosfir_no_posts' );

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