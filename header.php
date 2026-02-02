<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
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

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php // Preconnect to external resources ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
// WordPress 5.2+ wp_body_open hook
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
} else {
    do_action( 'wp_body_open' );
}
?>

<?php do_action( 'nosfir_before_site' ); ?>

<div id="page" class="site">

    <a class="skip-link screen-reader-text" href="#content">
        <?php esc_html_e( 'Skip to content', 'nosfir' ); ?>
    </a>

    <?php do_action( 'nosfir_before_header' ); ?>

    <header id="masthead" class="site-header" role="banner">
        
        <?php
        /**
         * Top bar (optional)
         * 
         * @hooked nosfir_top_bar_container - 0
         * @hooked nosfir_top_bar_left - 10
         * @hooked nosfir_top_bar_right - 20
         * @hooked nosfir_top_bar_container_close - 100
         */
        do_action( 'nosfir_top_bar' );
        ?>
        
        <div class="header-inner container">
            
            <?php
            /**
             * Site branding (logo/title)
             */
            nosfir_site_branding();
            ?>
            
            <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'nosfir' ); ?>">
                
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="hamburger" aria-hidden="true">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                    <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'nosfir' ); ?></span>
                </button>
                
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu(
                        array(
                            'theme_location'  => 'primary',
                            'menu_id'         => 'primary-menu',
                            'menu_class'      => 'nav-menu primary-menu',
                            'container'       => false,
                            'depth'           => 3,
                            'fallback_cb'     => 'nosfir_primary_menu_fallback',
                        )
                    );
                } else {
                    nosfir_primary_menu_fallback();
                }
                ?>
                
            </nav><!-- #site-navigation -->
            
            <div class="header-actions">
                <?php
                /**
                 * Header actions
                 * 
                 * @hooked nosfir_header_search - 10
                 * @hooked nosfir_header_account - 20
                 * @hooked nosfir_header_cart - 30
                 */
                do_action( 'nosfir_header_actions' );
                
                // Default header search if no hooks
                if ( ! has_action( 'nosfir_header_actions' ) ) {
                    nosfir_header_search();
                }
                ?>
            </div><!-- .header-actions -->
            
        </div><!-- .header-inner -->
        
    </header><!-- #masthead -->

    <?php do_action( 'nosfir_after_header' ); ?>

    <?php
    /**
     * Before content area
     * 
     * @hooked nosfir_breadcrumb - 10
     * @hooked nosfir_hero_section - 20
     */
    do_action( 'nosfir_before_content' );
    ?>

    <div id="content" class="site-content">
        <div class="container">
            
            <?php do_action( 'nosfir_content_top' ); ?>