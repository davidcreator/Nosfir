<?php
/**
 * The header for our theme
 *
 * @package Nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    
    <a class="skip-link screen-reader-text" href="#primary">
        <?php esc_html_e( 'Skip to content', 'nosfir' ); ?>
    </a>

    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-inner">
                
                <!-- Site Branding -->
                <div class="site-branding">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php endif; ?>
                    
                    <div class="site-branding-text">
                        <?php if ( is_front_page() && is_home() ) : ?>
                            <h1 class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                    <?php bloginfo( 'name' ); ?>
                                </a>
                            </h1>
                        <?php else : ?>
                            <p class="site-title">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                    <?php bloginfo( 'name' ); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        
                        <?php
                        $nosfir_description = get_bloginfo( 'description', 'display' );
                        if ( $nosfir_description || is_customize_preview() ) :
                        ?>
                            <p class="site-description"><?php echo esc_html( $nosfir_description ); ?></p>
                        <?php endif; ?>
                    </div>
                </div><!-- .site-branding -->

                <!-- Main Navigation -->
                <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'nosfir' ); ?>">
                    <?php
                    if ( has_nav_menu( 'primary' ) ) {
                        wp_nav_menu(
                            array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'primary-menu',
                                'menu_class'     => 'nav-menu',
                                'container'      => false,
                            )
                        );
                    } else {
                        // Fallback menu
                        echo '<ul class="nav-menu">';
                        wp_list_pages( array(
                            'title_li' => '',
                            'depth'    => 2,
                        ) );
                        echo '</ul>';
                    }
                    ?>
                </nav><!-- #site-navigation -->

                <!-- Header Actions -->
                <div class="header-actions">
                    <?php
                    // Header search
                    if ( function_exists( 'nosfir_header_search' ) ) {
                        nosfir_header_search();
                    }
                    
                    // WooCommerce cart
                    if ( function_exists( 'nosfir_wc_header_cart' ) ) {
                        nosfir_wc_header_cart();
                    }
                    ?>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'nosfir' ); ?>">
                        <svg class="icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                        <svg class="icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div><!-- .header-actions -->
                
            </div><!-- .header-inner -->
        </div><!-- .container -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">