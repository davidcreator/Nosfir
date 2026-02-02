<?php
/**
 * Template part for displaying page content in template-homepage.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get current post ID
$post_id = get_the_ID();

/*
|--------------------------------------------------------------------------
| Homepage Settings from Customizer
|--------------------------------------------------------------------------
*/
$hero_enabled        = get_theme_mod( 'nosfir_homepage_hero_enable', true );
$parallax_enabled    = get_theme_mod( 'nosfir_homepage_parallax', false );
$overlay_enabled     = get_theme_mod( 'nosfir_homepage_overlay', true );
$overlay_opacity     = get_theme_mod( 'nosfir_homepage_overlay_opacity', '0.5' );
$scroll_indicator    = get_theme_mod( 'nosfir_homepage_hero_scroll_indicator', true );

/*
|--------------------------------------------------------------------------
| Page Meta Options
|--------------------------------------------------------------------------
*/
$featured_image      = get_the_post_thumbnail_url( $post_id, 'full' );
$hide_title          = get_post_meta( $post_id, '_nosfir_hide_title', true );
$custom_subtitle     = get_post_meta( $post_id, '_nosfir_page_subtitle', true );
$hero_content        = get_post_meta( $post_id, '_nosfir_hero_content', true );
$cta_button_text     = get_post_meta( $post_id, '_nosfir_cta_button_text', true );
$cta_button_url      = get_post_meta( $post_id, '_nosfir_cta_button_url', true );
$secondary_cta_text  = get_post_meta( $post_id, '_nosfir_secondary_cta_text', true );
$secondary_cta_url   = get_post_meta( $post_id, '_nosfir_secondary_cta_url', true );

/*
|--------------------------------------------------------------------------
| Build Data Attributes
|--------------------------------------------------------------------------
*/
$data_attrs = array();

if ( $parallax_enabled && $featured_image ) {
    $data_attrs['data-parallax']       = 'true';
    $data_attrs['data-parallax-speed'] = '0.5';
}

if ( $featured_image ) {
    $data_attrs['data-featured-image'] = esc_url( $featured_image );
}

// Convert to string
$data_attrs_string = '';
foreach ( $data_attrs as $key => $value ) {
    $data_attrs_string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
}

/*
|--------------------------------------------------------------------------
| Build Hero Inline Styles
|--------------------------------------------------------------------------
*/
$hero_styles = array();

if ( $featured_image && $hero_enabled ) {
    $hero_styles[] = 'background-image: url(' . esc_url( $featured_image ) . ')';
}

$hero_style_string = ! empty( $hero_styles ) ? 'style="' . esc_attr( implode( '; ', $hero_styles ) ) . '"' : '';

/*
|--------------------------------------------------------------------------
| Hero CSS Classes
|--------------------------------------------------------------------------
*/
$hero_classes = array( 'homepage-hero' );

if ( $parallax_enabled ) {
    $hero_classes[] = 'has-parallax';
}

if ( $featured_image ) {
    $hero_classes[] = 'has-background-image';
}

if ( $overlay_enabled && $featured_image ) {
    $hero_classes[] = 'has-overlay';
}

/*
|--------------------------------------------------------------------------
| Check if hero should be displayed
|--------------------------------------------------------------------------
*/
$show_hero = $hero_enabled && ( $featured_image || $hero_content || ! $hide_title );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'homepage-content' ); ?><?php echo $data_attrs_string; ?>>

    <?php
    /**
     * Hook: nosfir_homepage_before
     *
     * @hooked nosfir_homepage_preloader - 5
     */
    do_action( 'nosfir_homepage_before' );
    ?>

    <?php if ( $show_hero ) : ?>
        <!-- ====== Hero Section ====== -->
        <section class="<?php echo esc_attr( implode( ' ', $hero_classes ) ); ?>" <?php echo $hero_style_string; ?> role="banner">
            
            <?php if ( $overlay_enabled && $featured_image ) : ?>
                <div class="hero-overlay" style="opacity: <?php echo esc_attr( $overlay_opacity ); ?>" aria-hidden="true"></div>
            <?php endif; ?>
            
            <div class="hero-content">
                <div class="container">
                    <div class="hero-inner">
                        
                        <?php if ( ! $hide_title ) : ?>
                            <h1 class="hero-title animate-fade-up">
                                <?php the_title(); ?>
                            </h1>
                        <?php endif; ?>
                        
                        <?php if ( $custom_subtitle ) : ?>
                            <p class="hero-subtitle animate-fade-up animate-delay-1">
                                <?php echo wp_kses_post( $custom_subtitle ); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ( $hero_content ) : ?>
                            <div class="hero-description animate-fade-up animate-delay-2">
                                <?php echo wp_kses_post( $hero_content ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $cta_button_text && $cta_button_url ) : ?>
                            <div class="hero-buttons animate-fade-up animate-delay-3">
                                <a href="<?php echo esc_url( $cta_button_url ); ?>" class="button button-primary button-large">
                                    <?php echo esc_html( $cta_button_text ); ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                                        <path d="M7 10h6m0 0l-3-3m3 3l-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                
                                <?php if ( $secondary_cta_text && $secondary_cta_url ) : ?>
                                    <a href="<?php echo esc_url( $secondary_cta_url ); ?>" class="button button-outline button-large">
                                        <?php echo esc_html( $secondary_cta_text ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( $scroll_indicator ) : ?>
                            <a href="#homepage-main" class="scroll-indicator animate-bounce" aria-label="<?php esc_attr_e( 'Scroll to content', 'nosfir' ); ?>">
                                <svg width="30" height="30" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M10 5v10m0 0l-3-3m3 3l3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                    </div><!-- .hero-inner -->
                </div><!-- .container -->
            </div><!-- .hero-content -->
            
        </section><!-- .homepage-hero -->
    <?php endif; ?>

    <!-- ====== Main Content ====== -->
    <div id="homepage-main" class="homepage-main-content">
        <div class="container">
            
            <?php
            /**
             * Hook: nosfir_homepage
             *
             * @hooked nosfir_homepage_content - 10
             * @hooked nosfir_homepage_sections - 20
             */
            do_action( 'nosfir_homepage' );
            ?>
            
            <?php if ( get_the_content() ) : ?>
                <div class="homepage-page-content">
                    <div class="entry-content">
                        <?php
                        the_content();
                        
                        wp_link_pages(
                            array(
                                'before'      => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'nosfir' ) . '">' . esc_html__( 'Pages:', 'nosfir' ),
                                'after'       => '</nav>',
                                'link_before' => '<span class="page-number">',
                                'link_after'  => '</span>',
                            )
                        );
                        ?>
                    </div><!-- .entry-content -->
                </div><!-- .homepage-page-content -->
            <?php endif; ?>
            
        </div><!-- .container -->
    </div><!-- .homepage-main-content -->

    <?php
    /*
    |--------------------------------------------------------------------------
    | Homepage Sections
    |--------------------------------------------------------------------------
    */
    $sections = array(
        'features'     => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_features_enable', true ),
            'priority' => 10,
        ),
        'about'        => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_about_enable', true ),
            'priority' => 20,
        ),
        'services'     => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_services_enable', true ),
            'priority' => 30,
        ),
        'portfolio'    => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_portfolio_enable', false ),
            'priority' => 40,
        ),
        'testimonials' => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_testimonials_enable', true ),
            'priority' => 50,
        ),
        'team'         => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_team_enable', false ),
            'priority' => 60,
        ),
        'blog'         => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_blog_enable', true ),
            'priority' => 70,
        ),
        'cta'          => array(
            'enabled'  => get_theme_mod( 'nosfir_homepage_cta_enable', true ),
            'priority' => 80,
        ),
    );

    // Allow filtering of sections
    $sections = apply_filters( 'nosfir_homepage_sections', $sections );

    // Sort by priority
    uasort( $sections, function( $a, $b ) {
        return $a['priority'] - $b['priority'];
    });

    // Render enabled sections
    foreach ( $sections as $section_name => $section_data ) {
        if ( $section_data['enabled'] ) {
            /**
             * Hook before each section
             */
            do_action( 'nosfir_homepage_before_section_' . $section_name );
            
            // Try to load section template
            $template_found = get_template_part( 'template-parts/homepage/section', $section_name );
            
            /**
             * Hook after each section
             */
            do_action( 'nosfir_homepage_after_section_' . $section_name );
        }
    }
    ?>

    <?php
    /*
    |--------------------------------------------------------------------------
    | WooCommerce Sections
    |--------------------------------------------------------------------------
    */
    if ( nosfir_is_woocommerce_active() ) :
    ?>
        <div class="homepage-woocommerce-sections">
            
            <?php
            /**
             * Hook: nosfir_homepage_woocommerce_before
             */
            do_action( 'nosfir_homepage_woocommerce_before' );
            ?>
            
            <?php
            // Featured Products Section
            if ( get_theme_mod( 'nosfir_homepage_featured_products_enable', true ) ) :
                nosfir_homepage_wc_section(
                    'featured-products',
                    get_theme_mod( 'nosfir_homepage_featured_products_title', __( 'Featured Products', 'nosfir' ) ),
                    get_theme_mod( 'nosfir_homepage_featured_products_desc', '' ),
                    '[featured_products per_page="' . absint( get_theme_mod( 'nosfir_homepage_featured_products_count', 4 ) ) . '" columns="4"]'
                );
            endif;
            ?>
            
            <?php
            // Product Categories Section
            if ( get_theme_mod( 'nosfir_homepage_product_categories_enable', true ) ) :
                nosfir_homepage_wc_section(
                    'product-categories',
                    get_theme_mod( 'nosfir_homepage_product_categories_title', __( 'Shop by Category', 'nosfir' ) ),
                    get_theme_mod( 'nosfir_homepage_product_categories_desc', '' ),
                    '[product_categories number="' . absint( get_theme_mod( 'nosfir_homepage_product_categories_count', 6 ) ) . '" parent="0" columns="3"]'
                );
            endif;
            ?>
            
            <?php
            // Recent Products Section
            if ( get_theme_mod( 'nosfir_homepage_recent_products_enable', true ) ) :
                nosfir_homepage_wc_section(
                    'recent-products',
                    get_theme_mod( 'nosfir_homepage_recent_products_title', __( 'New Arrivals', 'nosfir' ) ),
                    get_theme_mod( 'nosfir_homepage_recent_products_desc', '' ),
                    '[recent_products per_page="' . absint( get_theme_mod( 'nosfir_homepage_recent_products_count', 8 ) ) . '" columns="4"]'
                );
            endif;
            ?>
            
            <?php
            // Sale Products Section
            if ( get_theme_mod( 'nosfir_homepage_sale_products_enable', false ) ) :
                $sale_products = wc_get_product_ids_on_sale();
                if ( ! empty( $sale_products ) ) :
                    nosfir_homepage_wc_section(
                        'sale-products',
                        get_theme_mod( 'nosfir_homepage_sale_products_title', __( 'Special Offers', 'nosfir' ) ),
                        get_theme_mod( 'nosfir_homepage_sale_products_desc', '' ),
                        '[sale_products per_page="' . absint( get_theme_mod( 'nosfir_homepage_sale_products_count', 4 ) ) . '" columns="4"]'
                    );
                endif;
            endif;
            ?>
            
            <?php
            // Best Selling Products Section
            if ( get_theme_mod( 'nosfir_homepage_best_sellers_enable', false ) ) :
                nosfir_homepage_wc_section(
                    'best-sellers',
                    get_theme_mod( 'nosfir_homepage_best_sellers_title', __( 'Best Sellers', 'nosfir' ) ),
                    get_theme_mod( 'nosfir_homepage_best_sellers_desc', '' ),
                    '[best_selling_products per_page="' . absint( get_theme_mod( 'nosfir_homepage_best_sellers_count', 4 ) ) . '" columns="4"]'
                );
            endif;
            ?>
            
            <?php
            /**
             * Hook: nosfir_homepage_woocommerce_after
             */
            do_action( 'nosfir_homepage_woocommerce_after' );
            ?>
            
        </div><!-- .homepage-woocommerce-sections -->
    <?php endif; ?>

    <?php
    /**
     * Hook: nosfir_homepage_after_sections
     * 
     * Allows adding custom sections after all default sections
     */
    do_action( 'nosfir_homepage_after_sections' );
    ?>

    <?php
    /**
     * Hook: nosfir_homepage_after
     */
    do_action( 'nosfir_homepage_after' );
    ?>

    <?php if ( current_user_can( 'edit_pages' ) ) : ?>
        <div class="edit-link-wrapper">
            <?php
            edit_post_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __( 'Edit <span class="screen-reader-text">%s</span>', 'nosfir' ),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post( get_the_title() )
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </div>
    <?php endif; ?>

</article><!-- #post-<?php the_ID(); ?> -->