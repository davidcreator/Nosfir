<?php
/**
 * Homepage Section: Services
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Section Settings from Customizer
|--------------------------------------------------------------------------
*/
$subtitle     = get_theme_mod( 'nosfir_homepage_services_subtitle', __( 'What We Do', 'nosfir' ) );
$title        = get_theme_mod( 'nosfir_homepage_services_title', __( 'Our Services', 'nosfir' ) );
$description  = get_theme_mod( 'nosfir_homepage_services_description', '' );
$columns      = get_theme_mod( 'nosfir_homepage_services_columns', 3 );
$style        = get_theme_mod( 'nosfir_homepage_services_style', 'cards' ); // cards, minimal, boxed
$show_icons   = get_theme_mod( 'nosfir_homepage_services_show_icons', true );
$show_numbers = get_theme_mod( 'nosfir_homepage_services_show_numbers', false );
$button_text  = get_theme_mod( 'nosfir_homepage_services_button_text', __( 'View All Services', 'nosfir' ) );
$button_url   = get_theme_mod( 'nosfir_homepage_services_button_url', '' );

/*
|--------------------------------------------------------------------------
| Services Data
|--------------------------------------------------------------------------
*/
$services = apply_filters( 'nosfir_homepage_services', array(
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_1_icon', 'code' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_1_title', __( 'Web Development', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_1_desc', __( 'Custom websites built with the latest technologies and best practices.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_1_link', '' ),
    ),
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_2_icon', 'palette' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_2_title', __( 'UI/UX Design', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_2_desc', __( 'Beautiful and intuitive designs that enhance user experience.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_2_link', '' ),
    ),
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_3_icon', 'smartphone' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_3_title', __( 'Mobile Apps', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_3_desc', __( 'Native and cross-platform mobile applications.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_3_link', '' ),
    ),
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_4_icon', 'trending-up' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_4_title', __( 'Digital Marketing', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_4_desc', __( 'Strategic marketing solutions to grow your business.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_4_link', '' ),
    ),
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_5_icon', 'shield' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_5_title', __( 'Cyber Security', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_5_desc', __( 'Protect your digital assets with our security solutions.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_5_link', '' ),
    ),
    array(
        'icon'        => get_theme_mod( 'nosfir_homepage_service_6_icon', 'cloud' ),
        'title'       => get_theme_mod( 'nosfir_homepage_service_6_title', __( 'Cloud Solutions', 'nosfir' ) ),
        'description' => get_theme_mod( 'nosfir_homepage_service_6_desc', __( 'Scalable cloud infrastructure and migration services.', 'nosfir' ) ),
        'link'        => get_theme_mod( 'nosfir_homepage_service_6_link', '' ),
    ),
) );

// Remove empty services
$services = array_filter( $services, function( $service ) {
    return ! empty( $service['title'] );
});

// Limit to number set in customizer
$services_count = get_theme_mod( 'nosfir_homepage_services_count', 6 );
$services = array_slice( $services, 0, $services_count );

if ( empty( $services ) ) {
    return;
}

/*
|--------------------------------------------------------------------------
| Section Classes
|--------------------------------------------------------------------------
*/
$section_classes = array(
    'homepage-section',
    'homepage-services',
    'services-style-' . sanitize_html_class( $style ),
    'services-columns-' . absint( $columns ),
);

/**
 * Filter section classes
 */
$section_classes = apply_filters( 'nosfir_homepage_services_classes', $section_classes );

/*
|--------------------------------------------------------------------------
| Icon SVGs
|--------------------------------------------------------------------------
*/
$icons = array(
    'code'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16,18 22,12 16,6"/><polyline points="8,6 2,12 8,18"/></svg>',
    'palette'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="0.5" fill="currentColor"/><circle cx="17.5" cy="10.5" r="0.5" fill="currentColor"/><circle cx="8.5" cy="7.5" r="0.5" fill="currentColor"/><circle cx="6.5" cy="12.5" r="0.5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.555C21.965 6.012 17.461 2 12 2z"/></svg>',
    'smartphone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
    'trending-up' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/><polyline points="17,6 23,6 23,12"/></svg>',
    'shield'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    'cloud'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>',
    'globe'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    'zap'         => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13,2 3,14 12,14 11,22 21,10 12,10 13,2"/></svg>',
    'settings'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'headphones'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
    'briefcase'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
);

?>

<section id="homepage-services" class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        
        <header class="section-header">
            <?php if ( $subtitle ) : ?>
                <span class="section-subtitle"><?php echo esc_html( $subtitle ); ?></span>
            <?php endif; ?>
            
            <?php if ( $title ) : ?>
                <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            
            <?php if ( $description ) : ?>
                <p class="section-description"><?php echo wp_kses_post( $description ); ?></p>
            <?php endif; ?>
        </header>
        
        <div class="services-grid" style="--columns: <?php echo absint( $columns ); ?>">
            <?php 
            $counter = 0;
            foreach ( $services as $service ) : 
                $counter++;
                $icon_key = $service['icon'] ?? 'settings';
                $icon_svg = $icons[ $icon_key ] ?? $icons['settings'];
            ?>
                <div class="service-item">
                    <div class="service-inner">
                        
                        <?php if ( $show_numbers ) : ?>
                            <span class="service-number"><?php echo sprintf( '%02d', $counter ); ?></span>
                        <?php endif; ?>
                        
                        <?php if ( $show_icons && $icon_svg ) : ?>
                            <div class="service-icon">
                                <?php echo $icon_svg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $service['title'] ) ) : ?>
                            <h3 class="service-title">
                                <?php if ( ! empty( $service['link'] ) ) : ?>
                                    <a href="<?php echo esc_url( $service['link'] ); ?>">
                                        <?php echo esc_html( $service['title'] ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo esc_html( $service['title'] ); ?>
                                <?php endif; ?>
                            </h3>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $service['description'] ) ) : ?>
                            <p class="service-description"><?php echo wp_kses_post( $service['description'] ); ?></p>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $service['link'] ) ) : ?>
                            <a href="<?php echo esc_url( $service['link'] ); ?>" class="service-link">
                                <?php esc_html_e( 'Learn More', 'nosfir' ); ?>
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ( $button_text && $button_url ) : ?>
            <div class="section-footer">
                <a href="<?php echo esc_url( $button_url ); ?>" class="button button-outline">
                    <?php echo esc_html( $button_text ); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <?php
        /**
         * Hook: nosfir_homepage_services_after
         */
        do_action( 'nosfir_homepage_services_after' );
        ?>
        
    </div>
</section>