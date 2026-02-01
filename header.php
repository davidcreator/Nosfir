<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php if ( is_singular() && pings_open() ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'nosfir_before_site' ); ?>

<div id="page" class="hfeed site">

	<?php do_action( 'nosfir_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner" <?php nosfir_header_styles(); ?>>

		<?php
		/**
		 * Functions hooked into nosfir_header action
		 *
		 * @hooked nosfir_header_container          - 0
		 * @hooked nosfir_skip_links                - 5
		 * @hooked nosfir_header_top_bar            - 10
		 * @hooked nosfir_site_branding             - 20
		 * @hooked nosfir_primary_navigation        - 30
		 * @hooked nosfir_header_search             - 40
		 * @hooked nosfir_header_cart               - 50
		 * @hooked nosfir_mobile_menu_toggle        - 55
		 * @hooked nosfir_header_container_close    - 60
		 */
		do_action( 'nosfir_header' );
		?>

	</header><!-- #masthead -->

	<?php do_action( 'nosfir_after_header' ); ?>

	<?php
	/**
	 * Functions hooked in to nosfir_before_content
	 *
	 * @hooked nosfir_header_widget_region - 10
	 * @hooked nosfir_breadcrumbs          - 20
	 * @hooked nosfir_page_header           - 30
	 */
	do_action( 'nosfir_before_content' );
	?>

	<div id="content" class="site-content" tabindex="-1">
		<div class="container">

			<?php do_action( 'nosfir_content_top' ); ?>