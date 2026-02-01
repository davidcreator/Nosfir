<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `nosfir_homepage` action.
 * By default this includes a variety of content sections that can be controlled via the Customizer.
 *
 * Template Name: Homepage
 * Template Post Type: page
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

	<div id="primary" class="content-area homepage-content">
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * Functions hooked in to nosfir_homepage action
			 *
			 * @hooked nosfir_homepage_hero             - 10
			 * @hooked nosfir_homepage_features         - 20
			 * @hooked nosfir_homepage_about            - 30
			 * @hooked nosfir_homepage_services         - 40
			 * @hooked nosfir_homepage_portfolio        - 50
			 * @hooked nosfir_homepage_stats            - 60
			 * @hooked nosfir_homepage_testimonials     - 70
			 * @hooked nosfir_homepage_blog             - 80
			 * @hooked nosfir_homepage_clients          - 90
			 * @hooked nosfir_homepage_cta              - 100
			 * @hooked nosfir_homepage_content          - 110
			 */
			do_action( 'nosfir_homepage' );
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();