<?php
/**
 * Nosfir hooks
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------
| FUNÇÕES DE SUPORTE (Devem vir ANTES dos hooks)
|--------------------------------------------------------------------------
*/

if ( ! function_exists( 'nosfir_is_woocommerce_activated' ) ) {
    /**
     * Check if WooCommerce is activated
     *
     * @return bool
     */
    function nosfir_is_woocommerce_activated() {
        return class_exists( 'WooCommerce' );
    }
}

/*
|--------------------------------------------------------------------------
| GENERAL HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_before_content', 'nosfir_header_widget_region', 10 );
add_action( 'nosfir_before_content', 'nosfir_breadcrumb', 20 );
add_action( 'nosfir_sidebar', 'nosfir_get_sidebar', 10 );

/*
|--------------------------------------------------------------------------
| HEADER TOP BAR HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_top_bar', 'nosfir_top_bar_container', 0 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_left', 10 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_right', 20 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_container_close', 100 );

/*
|--------------------------------------------------------------------------
| HEADER HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_header', 'nosfir_header_container', 0 );
add_action( 'nosfir_header', 'nosfir_skip_links', 5 );
add_action( 'nosfir_header', 'nosfir_site_branding', 20 );
add_action( 'nosfir_header', 'nosfir_primary_navigation', 30 );
add_action( 'nosfir_header', 'nosfir_header_search', 40 );
add_action( 'nosfir_header', 'nosfir_header_account', 50 );

// WooCommerce cart - conditional (função já definida acima)
if ( nosfir_is_woocommerce_activated() ) {
    add_action( 'nosfir_header', 'nosfir_header_cart', 60 );
}

add_action( 'nosfir_header', 'nosfir_header_container_close', 100 );

/*
|--------------------------------------------------------------------------
| HEADER AFTER HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_after_header', 'nosfir_secondary_navigation', 10 );
add_action( 'nosfir_after_header', 'nosfir_mobile_navigation', 20 );

/*
|--------------------------------------------------------------------------
| HERO SECTION HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_before_content', 'nosfir_hero_section', 5 );

/*
|--------------------------------------------------------------------------
| FOOTER HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_before_footer', 'nosfir_footer_cta', 10 );

add_action( 'nosfir_footer', 'nosfir_footer_container', 0 );
add_action( 'nosfir_footer', 'nosfir_footer_widgets', 10 );
add_action( 'nosfir_footer', 'nosfir_footer_navigation', 20 );
add_action( 'nosfir_footer', 'nosfir_footer_social', 30 );
add_action( 'nosfir_footer', 'nosfir_footer_container_close', 40 );

add_action( 'nosfir_after_footer', 'nosfir_footer_bottom_container', 0 );
add_action( 'nosfir_after_footer', 'nosfir_credit', 10 );
add_action( 'nosfir_after_footer', 'nosfir_footer_bottom_container_close', 100 );

/*
|--------------------------------------------------------------------------
| HOMEPAGE HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_homepage', 'nosfir_homepage_content', 10 );
add_action( 'nosfir_homepage', 'nosfir_homepage_sections', 20 );

add_action( 'nosfir_homepage_sections', 'nosfir_homepage_hero', 10 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_featured', 20 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_services', 30 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_about', 40 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_portfolio', 50 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_testimonials', 60 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_blog', 70 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_cta', 80 );

/*
|--------------------------------------------------------------------------
| POST HOOKS
|--------------------------------------------------------------------------
*/

// Archive/Blog
add_action( 'nosfir_loop_post', 'nosfir_post_thumbnail', 10 );
add_action( 'nosfir_loop_post', 'nosfir_post_header', 20 );
add_action( 'nosfir_loop_post', 'nosfir_post_content', 30 );
add_action( 'nosfir_loop_post', 'nosfir_post_footer_meta', 40 );

add_action( 'nosfir_loop_after', 'nosfir_paging_nav', 10 );

// Single Post
add_action( 'nosfir_single_post_top', 'nosfir_post_thumbnail', 10 );

add_action( 'nosfir_single_post', 'nosfir_post_header', 10 );
add_action( 'nosfir_single_post', 'nosfir_post_content', 20 );
add_action( 'nosfir_single_post', 'nosfir_post_footer', 30 );

add_action( 'nosfir_single_post_bottom', 'nosfir_post_tags', 10 );
add_action( 'nosfir_single_post_bottom', 'nosfir_post_share', 20 );
add_action( 'nosfir_single_post_bottom', 'nosfir_author_box', 30 );
add_action( 'nosfir_single_post_bottom', 'nosfir_related_posts', 40 );
add_action( 'nosfir_single_post_bottom', 'nosfir_post_navigation', 50 );
add_action( 'nosfir_single_post_bottom', 'nosfir_display_comments', 60 );

/*
|--------------------------------------------------------------------------
| PAGE HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_page', 'nosfir_page_header', 10 );
add_action( 'nosfir_page', 'nosfir_page_content', 20 );
add_action( 'nosfir_page_after', 'nosfir_display_comments', 10 );

/*
|--------------------------------------------------------------------------
| SEARCH HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_search_before', 'nosfir_search_header', 10 );
add_action( 'nosfir_search_before', 'nosfir_search_form_display', 20 );

/*
|--------------------------------------------------------------------------
| 404 HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'nosfir_404', 'nosfir_404_header', 10 );
add_action( 'nosfir_404', 'nosfir_404_content', 20 );
add_action( 'nosfir_404', 'nosfir_404_search_form', 30 );
add_action( 'nosfir_404', 'nosfir_404_recent_posts', 40 );

/*
|--------------------------------------------------------------------------
| EXTRA HOOKS
|--------------------------------------------------------------------------
*/

add_action( 'wp_body_open', 'nosfir_body_open', 10 );
add_action( 'nosfir_before_site', 'nosfir_preloader', 10 );
add_action( 'nosfir_after_site', 'nosfir_scroll_to_top', 10 );

if ( wp_is_mobile() ) {
    add_action( 'nosfir_after_site', 'nosfir_mobile_menu_overlay', 20 );
}

/*
|==========================================================================
| FUNÇÕES DO HEADER
|==========================================================================
*/

if ( ! function_exists( 'nosfir_header_container' ) ) {
    /**
     * Header container opening
     */
    function nosfir_header_container() {
        echo '<div class="header-inner container">';
    }
}

if ( ! function_exists( 'nosfir_header_container_close' ) ) {
    /**
     * Header container closing
     */
    function nosfir_header_container_close() {
        echo '</div><!-- .header-inner -->';
    }
}

if ( ! function_exists( 'nosfir_skip_links' ) ) {
    /**
     * Skip links for accessibility
     */
    function nosfir_skip_links() {
        ?>
        <a class="skip-link screen-reader-text" href="#content">
            <?php esc_html_e( 'Skip to content', 'nosfir' ); ?>
        </a>
        <?php
    }
}

if ( ! function_exists( 'nosfir_site_branding' ) ) {
    /**
     * Site branding - logo and site title
     */
    function nosfir_site_branding() {
        ?>
        <div class="site-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            }
            ?>
            
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
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) :
                    ?>
                    <p class="site-description"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div><!-- .site-branding-text -->
        </div><!-- .site-branding -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_primary_navigation' ) ) {
    /**
     * Primary navigation menu
     */
    function nosfir_primary_navigation() {
        ?>
        <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'nosfir' ); ?>">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="menu-toggle-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="screen-reader-text"><?php esc_html_e( 'Menu', 'nosfir' ); ?></span>
            </button>
            
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu(
                    array(
                        'theme_location'  => 'primary',
                        'menu_id'         => 'primary-menu',
                        'menu_class'      => 'primary-menu nav-menu',
                        'container'       => false,
                        'fallback_cb'     => 'nosfir_primary_menu_fallback',
                    )
                );
            } else {
                nosfir_primary_menu_fallback();
            }
            ?>
        </nav><!-- #site-navigation -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_primary_menu_fallback' ) ) {
    /**
     * Fallback for primary menu
     */
    function nosfir_primary_menu_fallback() {
        echo '<ul id="primary-menu" class="primary-menu nav-menu">';
        wp_list_pages(
            array(
                'title_li' => '',
                'depth'    => 2,
            )
        );
        echo '</ul>';
    }
}

if ( ! function_exists( 'nosfir_secondary_navigation' ) ) {
    /**
     * Secondary navigation menu
     */
    function nosfir_secondary_navigation() {
        if ( ! has_nav_menu( 'secondary' ) ) {
            return;
        }
        ?>
        <nav class="secondary-navigation" aria-label="<?php esc_attr_e( 'Secondary Menu', 'nosfir' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'secondary',
                    'menu_class'     => 'secondary-menu nav-menu',
                    'container'      => false,
                    'depth'          => 1,
                )
            );
            ?>
        </nav>
        <?php
    }
}

if ( ! function_exists( 'nosfir_mobile_navigation' ) ) {
    /**
     * Mobile navigation
     */
    function nosfir_mobile_navigation() {
        ?>
        <div id="mobile-navigation" class="mobile-navigation" aria-hidden="true">
            <div class="mobile-navigation-inner">
                <button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'nosfir' ); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                
                <?php
                if ( has_nav_menu( 'mobile' ) ) {
                    wp_nav_menu(
                        array(
                            'theme_location' => 'mobile',
                            'menu_class'     => 'mobile-menu',
                            'container'      => false,
                        )
                    );
                } elseif ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_class'     => 'mobile-menu',
                            'container'      => false,
                        )
                    );
                }
                ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_header_search' ) ) {
    /**
     * Header search
     */
    function nosfir_header_search() {
        if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
            return;
        }
        ?>
        <div class="header-search">
            <button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'nosfir' ); ?>" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="7"/>
                    <path d="M14 14l4 4"/>
                </svg>
            </button>
            <div class="search-dropdown" aria-hidden="true">
                <?php get_search_form(); ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_header_account' ) ) {
    /**
     * Header account link
     */
    function nosfir_header_account() {
        if ( ! get_theme_mod( 'nosfir_header_account', true ) ) {
            return;
        }
        
        // Get account URL
        if ( nosfir_is_woocommerce_activated() ) {
            $account_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
        } else {
            $account_url = wp_login_url( get_permalink() );
        }
        ?>
        <div class="header-account">
            <a href="<?php echo esc_url( $account_url ); ?>" class="account-link" aria-label="<?php esc_attr_e( 'My Account', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="10" cy="6" r="4"/>
                    <path d="M2 20c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
                </svg>
            </a>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_header_cart' ) ) {
    /**
     * Header cart for WooCommerce
     */
    function nosfir_header_cart() {
        if ( ! nosfir_is_woocommerce_activated() ) {
            return;
        }
        ?>
        <div class="header-cart">
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link" aria-label="<?php esc_attr_e( 'View cart', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h12l-1.5 9h-9z"/>
                    <circle cx="9" cy="18" r="1"/>
                    <circle cx="15" cy="18" r="1"/>
                    <path d="M6 6L5 2H2"/>
                </svg>
                <span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
            </a>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_header_styles' ) ) {
    /**
     * Output header inline styles (chamada no header.php)
     */
    function nosfir_header_styles() {
        $styles = array();
        
        // Background color
        $bg_color = get_theme_mod( 'nosfir_header_bg_color' );
        if ( $bg_color ) {
            $styles[] = 'background-color:' . esc_attr( $bg_color );
        }
        
        // Background image
        $bg_image = get_theme_mod( 'nosfir_header_bg_image' );
        if ( $bg_image ) {
            $styles[] = 'background-image:url(' . esc_url( $bg_image ) . ')';
        }
        
        if ( ! empty( $styles ) ) {
            echo 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
        }
    }
}

/*
|==========================================================================
| FUNÇÕES DO CONTENT
|==========================================================================
*/

if ( ! function_exists( 'nosfir_header_widget_region' ) ) {
    /**
     * Header widget region
     */
    function nosfir_header_widget_region() {
        if ( ! is_active_sidebar( 'header-widget' ) ) {
            return;
        }
        ?>
        <div class="header-widget-region">
            <div class="container">
                <?php dynamic_sidebar( 'header-widget' ); ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_breadcrumb' ) ) {
    /**
     * Breadcrumb navigation
     */
    function nosfir_breadcrumb() {
        if ( is_front_page() || ! get_theme_mod( 'nosfir_breadcrumb', true ) ) {
            return;
        }
        
        // Use WooCommerce breadcrumb if available
        if ( nosfir_is_woocommerce_activated() && is_woocommerce() ) {
            woocommerce_breadcrumb();
            return;
        }
        
        // Use Yoast breadcrumb if available
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '">', '</nav>' );
            return;
        }
        
        // Simple fallback breadcrumb
        ?>
        <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nosfir' ); ?>">
            <div class="container">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nosfir' ); ?></a>
                <span class="separator">/</span>
                <?php
                if ( is_category() || is_single() ) {
                    the_category( ' <span class="separator">/</span> ' );
                    if ( is_single() ) {
                        echo ' <span class="separator">/</span> ';
                        the_title();
                    }
                } elseif ( is_page() ) {
                    the_title();
                } elseif ( is_search() ) {
                    esc_html_e( 'Search Results', 'nosfir' );
                } elseif ( is_archive() ) {
                    the_archive_title();
                }
                ?>
            </div>
        </nav>
        <?php
    }
}

if ( ! function_exists( 'nosfir_get_sidebar' ) ) {
    /**
     * Get sidebar
     */
    function nosfir_get_sidebar() {
        get_sidebar();
    }
}

if ( ! function_exists( 'nosfir_search_form' ) ) {
    /**
     * Custom search form
     *
     * @param string $id Optional ID for the form
     */
    function nosfir_search_form( $id = '' ) {
        $unique_id = $id ? $id : 'search-' . wp_rand( 1000, 9999 );
        ?>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label for="<?php echo esc_attr( $unique_id ); ?>" class="screen-reader-text">
                <?php esc_html_e( 'Search for:', 'nosfir' ); ?>
            </label>
            <input type="search" 
                   id="<?php echo esc_attr( $unique_id ); ?>" 
                   class="search-field" 
                   placeholder="<?php esc_attr_e( 'Search...', 'nosfir' ); ?>" 
                   value="<?php echo get_search_query(); ?>" 
                   name="s" />
            <button type="submit" class="search-submit">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="7"/>
                    <path d="M14 14l4 4"/>
                </svg>
                <span class="screen-reader-text"><?php esc_html_e( 'Search', 'nosfir' ); ?></span>
            </button>
        </form>
        <?php
    }
}

if ( ! function_exists( 'nosfir_search_form_display' ) ) {
    /**
     * Display search form (for hooks)
     */
    function nosfir_search_form_display() {
        nosfir_search_form( 'archive-search' );
    }
}

/*
|==========================================================================
| FUNÇÕES DE POSTS
|==========================================================================
*/

if ( ! function_exists( 'nosfir_post_thumbnail' ) ) {
    /**
     * Post thumbnail
     */
    function nosfir_post_thumbnail() {
        if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
            return;
        }
        
        $size = is_singular() ? 'large' : 'medium_large';
        ?>
        <div class="post-thumbnail">
            <?php if ( ! is_singular() ) : ?>
                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php endif; ?>
            
            <?php
            the_post_thumbnail(
                $size,
                array(
                    'alt' => the_title_attribute( array( 'echo' => false ) ),
                    'loading' => 'lazy',
                )
            );
            ?>
            
            <?php if ( ! is_singular() ) : ?>
                </a>
            <?php endif; ?>
        </div><!-- .post-thumbnail -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_header' ) ) {
    /**
     * Post header with title and meta
     */
    function nosfir_post_header() {
        ?>
        <header class="entry-header">
            <?php
            if ( is_singular() ) {
                the_title( '<h1 class="entry-title">', '</h1>' );
            } else {
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            }
            
            if ( 'post' === get_post_type() ) :
                ?>
                <div class="entry-meta">
                    <?php
                    nosfir_posted_on();
                    nosfir_posted_by();
                    ?>
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_content' ) ) {
    /**
     * Post content
     */
    function nosfir_post_content() {
        ?>
        <div class="entry-content">
            <?php
            if ( is_singular() ) {
                the_content();
                
                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
                        'after'  => '</div>',
                    )
                );
            } else {
                the_excerpt();
            }
            ?>
        </div><!-- .entry-content -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_footer' ) ) {
    /**
     * Post footer
     */
    function nosfir_post_footer() {
        ?>
        <footer class="entry-footer">
            <?php
            // Categories
            $categories_list = get_the_category_list( ', ' );
            if ( $categories_list ) {
                printf(
                    '<span class="cat-links">%1$s %2$s</span>',
                    esc_html__( 'Posted in:', 'nosfir' ),
                    $categories_list
                );
            }
            
            // Edit link
            edit_post_link(
                sprintf(
                    wp_kses(
                        __( 'Edit <span class="screen-reader-text">%s</span>', 'nosfir' ),
                        array( 'span' => array( 'class' => array() ) )
                    ),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer><!-- .entry-footer -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_posted_on' ) ) {
    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    function nosfir_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        printf(
            '<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
            esc_html__( 'Posted on', 'nosfir' ),
            esc_url( get_permalink() ),
            $time_string
        );
    }
}

if ( ! function_exists( 'nosfir_posted_by' ) ) {
    /**
     * Prints HTML with meta information for the current author.
     */
    function nosfir_posted_by() {
        printf(
            '<span class="byline"> %1$s <span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
            esc_html__( 'by', 'nosfir' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        );
    }
}

if ( ! function_exists( 'nosfir_paging_nav' ) ) {
    /**
     * Pagination navigation
     */
    function nosfir_paging_nav() {
        if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
            return;
        }
        ?>
        <nav class="navigation pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'nosfir' ); ?>">
            <h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'nosfir' ); ?></h2>
            <div class="nav-links">
                <?php
                echo paginate_links(
                    array(
                        'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'nosfir' ) . '</span><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>',
                        'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'nosfir' ) . '</span><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>',
                    )
                );
                ?>
            </div>
        </nav>
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_navigation' ) ) {
    /**
     * Post navigation (previous/next post)
     */
    function nosfir_post_navigation() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
            )
        );
    }
}

if ( ! function_exists( 'nosfir_display_comments' ) ) {
    /**
     * Display comments
     */
    function nosfir_display_comments() {
        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
    }
}

if ( ! function_exists( 'nosfir_author_box' ) ) {
    /**
     * Author box for single posts
     */
    function nosfir_author_box() {
        if ( ! is_singular( 'post' ) || ! get_theme_mod( 'nosfir_author_box', true ) ) {
            return;
        }
        ?>
        <div class="author-box">
            <div class="author-avatar">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
            </div>
            <div class="author-info">
                <h3 class="author-name">
                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                        <?php the_author(); ?>
                    </a>
                </h3>
                <?php if ( get_the_author_meta( 'description' ) ) : ?>
                    <div class="author-bio">
                        <?php the_author_meta( 'description' ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_related_posts' ) ) {
    /**
     * Related posts
     */
    function nosfir_related_posts() {
        if ( ! is_singular( 'post' ) || ! get_theme_mod( 'nosfir_related_posts', true ) ) {
            return;
        }
        
        $categories = get_the_category();
        if ( empty( $categories ) ) {
            return;
        }
        
        $related = new WP_Query(
            array(
                'category__in'        => wp_list_pluck( $categories, 'term_id' ),
                'post__not_in'        => array( get_the_ID() ),
                'posts_per_page'      => 3,
                'ignore_sticky_posts' => true,
            )
        );
        
        if ( ! $related->have_posts() ) {
            return;
        }
        ?>
        <div class="related-posts">
            <h3 class="related-posts-title"><?php esc_html_e( 'Related Posts', 'nosfir' ); ?></h3>
            <div class="related-posts-grid">
                <?php
                while ( $related->have_posts() ) :
                    $related->the_post();
                    ?>
                    <article class="related-post">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="related-post-thumbnail">
                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                            </a>
                        <?php endif; ?>
                        <h4 class="related-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    }
}

if ( ! function_exists( 'nosfir_social_share' ) ) {
    /**
     * Social share buttons
     */
    function nosfir_social_share() {
        $url   = urlencode( get_permalink() );
        $title = urlencode( get_the_title() );
        ?>
        <div class="social-share">
            <span class="share-label"><?php esc_html_e( 'Share:', 'nosfir' ); ?></span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on Facebook', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                </svg>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on Twitter', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/>
                </svg>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z"/>
                </svg>
            </a>
        </div>
        <?php
    }
}

/*
|==========================================================================
| FUNÇÕES DE PÁGINAS
|==========================================================================
*/

if ( ! function_exists( 'nosfir_page_header' ) ) {
    /**
     * Page header
     */
    function nosfir_page_header() {
        ?>
        <header class="entry-header">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </header><!-- .entry-header -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_page_content' ) ) {
    /**
     * Page content
     */
    function nosfir_page_content() {
        ?>
        <div class="entry-content">
            <?php
            the_content();
            
            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
                    'after'  => '</div>',
                )
            );
            ?>
        </div><!-- .entry-content -->
        <?php
    }
}

/*
|==========================================================================
| FUNÇÕES DO FOOTER
|==========================================================================
*/

if ( ! function_exists( 'nosfir_footer_container' ) ) {
    /**
     * Footer container opening
     */
    function nosfir_footer_container() {
        echo '<div class="footer-main"><div class="container">';
    }
}

if ( ! function_exists( 'nosfir_footer_container_close' ) ) {
    /**
     * Footer container closing
     */
    function nosfir_footer_container_close() {
        echo '</div></div><!-- .footer-main -->';
    }
}

if ( ! function_exists( 'nosfir_footer_widgets' ) ) {
    /**
     * Footer widgets
     */
    function nosfir_footer_widgets() {
        if ( ! is_active_sidebar( 'footer-1' ) && 
             ! is_active_sidebar( 'footer-2' ) && 
             ! is_active_sidebar( 'footer-3' ) && 
             ! is_active_sidebar( 'footer-4' ) ) {
            return;
        }
        
        $columns = 0;
        for ( $i = 1; $i <= 4; $i++ ) {
            if ( is_active_sidebar( 'footer-' . $i ) ) {
                $columns++;
            }
        }
        ?>
        <div class="footer-widgets columns-<?php echo esc_attr( $columns ); ?>">
            <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                <?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
                    <div class="footer-widget-area footer-widget-<?php echo esc_attr( $i ); ?>">
                        <?php dynamic_sidebar( 'footer-' . $i ); ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div><!-- .footer-widgets -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_footer_navigation' ) ) {
    /**
     * Footer navigation
     */
    function nosfir_footer_navigation() {
        if ( ! has_nav_menu( 'footer' ) ) {
            return;
        }
        ?>
        <nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'nosfir' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'container'      => false,
                    'depth'          => 1,
                )
            );
            ?>
        </nav>
        <?php
    }
}

if ( ! function_exists( 'nosfir_footer_social' ) ) {
    /**
     * Footer social links
     */
    function nosfir_footer_social() {
        if ( ! has_nav_menu( 'social' ) ) {
            return;
        }
        ?>
        <div class="footer-social">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'social',
                    'menu_class'     => 'social-menu',
                    'container'      => false,
                    'depth'          => 1,
                    'link_before'    => '<span class="screen-reader-text">',
                    'link_after'     => '</span>',
                )
            );
            ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_footer_bottom_container' ) ) {
    /**
     * Footer bottom container opening
     */
    function nosfir_footer_bottom_container() {
        echo '<div class="footer-bottom"><div class="container">';
    }
}

if ( ! function_exists( 'nosfir_footer_bottom_container_close' ) ) {
    /**
     * Footer bottom container closing
     */
    function nosfir_footer_bottom_container_close() {
        echo '</div></div><!-- .footer-bottom -->';
    }
}

if ( ! function_exists( 'nosfir_credit' ) ) {
    /**
     * Site credits/copyright
     */
    function nosfir_credit() {
        $copyright = get_theme_mod( 'nosfir_footer_copyright' );
        ?>
        <div class="site-info">
            <?php if ( $copyright ) : ?>
                <?php echo wp_kses_post( $copyright ); ?>
            <?php else : ?>
                <span class="copyright">
                    &copy; <?php echo esc_html( date( 'Y' ) ); ?> 
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
                </span>
                <span class="theme-credit">
                    <?php
                    printf(
                        /* translators: %s: theme name */
                        esc_html__( 'Theme: %s', 'nosfir' ),
                        '<a href="https://github.com/davidcreator/Nosfir" rel="noopener">Nosfir</a>'
                    );
                    ?>
                </span>
            <?php endif; ?>
        </div><!-- .site-info -->
        <?php
    }
}

if ( ! function_exists( 'nosfir_footer_cta' ) ) {
    /**
     * Footer CTA section
     */
    function nosfir_footer_cta() {
        if ( ! get_theme_mod( 'nosfir_footer_cta_enable', false ) ) {
            return;
        }
        
        $title       = get_theme_mod( 'nosfir_footer_cta_title' );
        $text        = get_theme_mod( 'nosfir_footer_cta_text' );
        $button_text = get_theme_mod( 'nosfir_footer_cta_button_text' );
        $button_url  = get_theme_mod( 'nosfir_footer_cta_button_url' );
        
        if ( ! $title && ! $text ) {
            return;
        }
        ?>
        <div class="footer-cta">
            <div class="container">
                <div class="footer-cta-inner">
                    <?php if ( $title ) : ?>
                        <h2 class="footer-cta-title"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                    
                    <?php if ( $text ) : ?>
                        <div class="footer-cta-text"><?php echo wp_kses_post( $text ); ?></div>
                    <?php endif; ?>
                    
                    <?php if ( $button_text && $button_url ) : ?>
                        <a href="<?php echo esc_url( $button_url ); ?>" class="footer-cta-button button">
                            <?php echo esc_html( $button_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

/*
|==========================================================================
| FUNÇÕES EXTRAS
|==========================================================================
*/

if ( ! function_exists( 'nosfir_top_bar_container' ) ) {
    /**
     * Top bar container opening
     */
    function nosfir_top_bar_container() {
        if ( ! get_theme_mod( 'nosfir_top_bar_enable', false ) ) {
            return;
        }
        echo '<div class="top-bar"><div class="container">';
    }
}

if ( ! function_exists( 'nosfir_top_bar_container_close' ) ) {
    /**
     * Top bar container closing
     */
    function nosfir_top_bar_container_close() {
        if ( ! get_theme_mod( 'nosfir_top_bar_enable', false ) ) {
            return;
        }
        echo '</div></div><!-- .top-bar -->';
    }
}

if ( ! function_exists( 'nosfir_top_bar_left' ) ) {
    /**
     * Top bar left content
     */
    function nosfir_top_bar_left() {
        if ( ! get_theme_mod( 'nosfir_top_bar_enable', false ) ) {
            return;
        }
        
        $phone = get_theme_mod( 'nosfir_header_phone' );
        $email = get_theme_mod( 'nosfir_header_email' );
        
        if ( ! $phone && ! $email ) {
            return;
        }
        ?>
        <div class="top-bar-left">
            <?php if ( $phone ) : ?>
                <span class="top-bar-phone">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
                        <?php echo esc_html( $phone ); ?>
                    </a>
                </span>
            <?php endif; ?>
            
            <?php if ( $email ) : ?>
                <span class="top-bar-email">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>">
                        <?php echo esc_html( $email ); ?>
                    </a>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_top_bar_right' ) ) {
    /**
     * Top bar right content
     */
    function nosfir_top_bar_right() {
        if ( ! get_theme_mod( 'nosfir_top_bar_enable', false ) ) {
            return;
        }
        
        if ( ! has_nav_menu( 'social' ) ) {
            return;
        }
        ?>
        <div class="top-bar-right">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'social',
                    'menu_class'     => 'social-menu',
                    'container'      => false,
                    'depth'          => 1,
                    'link_before'    => '<span class="screen-reader-text">',
                    'link_after'     => '</span>',
                )
            );
            ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_hero_section' ) ) {
    /**
     * Hero section
     */
    function nosfir_hero_section() {
        if ( ! is_front_page() || ! get_theme_mod( 'nosfir_hero_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/hero' );
    }
}

if ( ! function_exists( 'nosfir_body_open' ) ) {
    /**
     * Body open hook
     */
    function nosfir_body_open() {
        do_action( 'nosfir_body_open' );
    }
}

if ( ! function_exists( 'nosfir_preloader' ) ) {
    /**
     * Preloader
     */
    function nosfir_preloader() {
        if ( ! get_theme_mod( 'nosfir_preloader', false ) ) {
            return;
        }
        ?>
        <div class="preloader" aria-hidden="true">
            <div class="preloader-inner">
                <div class="spinner"></div>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_scroll_to_top' ) ) {
    /**
     * Scroll to top button
     */
    function nosfir_scroll_to_top() {
        if ( ! get_theme_mod( 'nosfir_scroll_to_top', true ) ) {
            return;
        }
        ?>
        <button class="scroll-to-top" aria-label="<?php esc_attr_e( 'Scroll to top', 'nosfir' ); ?>">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
        <?php
    }
}

if ( ! function_exists( 'nosfir_mobile_menu_overlay' ) ) {
    /**
     * Mobile menu overlay
     */
    function nosfir_mobile_menu_overlay() {
        echo '<div class="mobile-menu-overlay" aria-hidden="true"></div>';
    }
}

if ( ! function_exists( 'nosfir_post_footer_meta' ) ) {
    /**
     * Post footer meta for archives
     */
    function nosfir_post_footer_meta() {
        if ( 'post' !== get_post_type() ) {
            return;
        }
        ?>
        <footer class="entry-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e( 'Read More', 'nosfir' ); ?>
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>
        </footer>
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_tags' ) ) {
    /**
     * Post tags
     */
    function nosfir_post_tags() {
        if ( ! has_tag() || ! get_theme_mod( 'nosfir_post_tags', true ) ) {
            return;
        }
        ?>
        <div class="post-tags">
            <span class="tags-label"><?php esc_html_e( 'Tags:', 'nosfir' ); ?></span>
            <?php the_tags( '', ', ', '' ); ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_post_share' ) ) {
    /**
     * Post share buttons wrapper
     */
    function nosfir_post_share() {
        if ( ! get_theme_mod( 'nosfir_post_share', true ) ) {
            return;
        }
        
        nosfir_social_share();
    }
}

if ( ! function_exists( 'nosfir_search_header' ) ) {
    /**
     * Search results header
     */
    function nosfir_search_header() {
        ?>
        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search Results for: %s', 'nosfir' ),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
        </header>
        <?php
    }
}

if ( ! function_exists( 'nosfir_404_header' ) ) {
    /**
     * 404 page header
     */
    function nosfir_404_header() {
        ?>
        <header class="page-header error-404-header">
            <h1 class="page-title"><?php esc_html_e( '404', 'nosfir' ); ?></h1>
            <p class="page-subtitle"><?php esc_html_e( 'Oops! That page can\'t be found.', 'nosfir' ); ?></p>
        </header>
        <?php
    }
}

if ( ! function_exists( 'nosfir_404_content' ) ) {
    /**
     * 404 page content
     */
    function nosfir_404_content() {
        ?>
        <div class="page-content">
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'nosfir' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'nosfir_404_search_form' ) ) {
    /**
     * 404 page search form
     */
    function nosfir_404_search_form() {
        get_search_form();
    }
}

if ( ! function_exists( 'nosfir_404_recent_posts' ) ) {
    /**
     * 404 page recent posts
     */
    function nosfir_404_recent_posts() {
        $recent_posts = wp_get_recent_posts(
            array(
                'numberposts' => 5,
                'post_status' => 'publish',
            )
        );
        
        if ( empty( $recent_posts ) ) {
            return;
        }
        ?>
        <div class="recent-posts">
            <h2><?php esc_html_e( 'Recent Posts', 'nosfir' ); ?></h2>
            <ul>
                <?php foreach ( $recent_posts as $post ) : ?>
                    <li>
                        <a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
                            <?php echo esc_html( $post['post_title'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        wp_reset_postdata();
    }
}

/*
|==========================================================================
| HOMEPAGE SECTIONS
|==========================================================================
*/

if ( ! function_exists( 'nosfir_homepage_content' ) ) {
    /**
     * Homepage content
     */
    function nosfir_homepage_content() {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    }
}

if ( ! function_exists( 'nosfir_homepage_sections' ) ) {
    /**
     * Homepage sections wrapper
     */
    function nosfir_homepage_sections() {
        // Sections are rendered via their individual hooks
    }
}

if ( ! function_exists( 'nosfir_homepage_hero' ) ) {
    /**
     * Homepage hero section
     */
    function nosfir_homepage_hero() {
        if ( ! get_theme_mod( 'nosfir_homepage_hero_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/hero' );
    }
}

if ( ! function_exists( 'nosfir_homepage_featured' ) ) {
    /**
     * Homepage featured section
     */
    function nosfir_homepage_featured() {
        if ( ! get_theme_mod( 'nosfir_homepage_featured_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/featured' );
    }
}

if ( ! function_exists( 'nosfir_homepage_services' ) ) {
    /**
     * Homepage services section
     */
    function nosfir_homepage_services() {
        if ( ! get_theme_mod( 'nosfir_homepage_services_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/services' );
    }
}

if ( ! function_exists( 'nosfir_homepage_about' ) ) {
    /**
     * Homepage about section
     */
    function nosfir_homepage_about() {
        if ( ! get_theme_mod( 'nosfir_homepage_about_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/about' );
    }
}

if ( ! function_exists( 'nosfir_homepage_portfolio' ) ) {
    /**
     * Homepage portfolio section
     */
    function nosfir_homepage_portfolio() {
        if ( ! get_theme_mod( 'nosfir_homepage_portfolio_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/portfolio' );
    }
}

if ( ! function_exists( 'nosfir_homepage_testimonials' ) ) {
    /**
     * Homepage testimonials section
     */
    function nosfir_homepage_testimonials() {
        if ( ! get_theme_mod( 'nosfir_homepage_testimonials_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/testimonials' );
    }
}

if ( ! function_exists( 'nosfir_homepage_blog' ) ) {
    /**
     * Homepage blog section
     */
    function nosfir_homepage_blog() {
        if ( ! get_theme_mod( 'nosfir_homepage_blog_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/blog' );
    }
}

if ( ! function_exists( 'nosfir_homepage_cta' ) ) {
    /**
     * Homepage CTA section
     */
    function nosfir_homepage_cta() {
        if ( ! get_theme_mod( 'nosfir_homepage_cta_enable', false ) ) {
            return;
        }
        
        get_template_part( 'template-parts/homepage/cta' );
    }
}

/*
|==========================================================================
| HOOK LOADED ACTION
|==========================================================================
*/

/**
 * Signal that all hooks are loaded
 * Allows child themes and plugins to modify
 */
do_action( 'nosfir_hooks_loaded' );