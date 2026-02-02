<?php
/**
 * Homepage Section: Features
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get section settings
$title       = get_theme_mod( 'nosfir_homepage_features_title', __( 'Our Features', 'nosfir' ) );
$subtitle    = get_theme_mod( 'nosfir_homepage_features_subtitle', '' );
$description = get_theme_mod( 'nosfir_homepage_features_description', '' );

// Get features (example - you can customize this to use repeater fields, widgets, etc.)
$features = apply_filters( 'nosfir_homepage_features', array() );

?>

<section id="homepage-features" class="homepage-section homepage-features">
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
        
        <?php if ( ! empty( $features ) ) : ?>
            <div class="features-grid">
                <?php foreach ( $features as $feature ) : ?>
                    <div class="feature-item">
                        <?php if ( ! empty( $feature['icon'] ) ) : ?>
                            <div class="feature-icon">
                                <?php echo wp_kses_post( $feature['icon'] ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $feature['title'] ) ) : ?>
                            <h3 class="feature-title"><?php echo esc_html( $feature['title'] ); ?></h3>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $feature['description'] ) ) : ?>
                            <p class="feature-description"><?php echo wp_kses_post( $feature['description'] ); ?></p>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $feature['link'] ) ) : ?>
                            <a href="<?php echo esc_url( $feature['link'] ); ?>" class="feature-link">
                                <?php esc_html_e( 'Learn More', 'nosfir' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php
            // Fallback: use widget area or default content
            if ( is_active_sidebar( 'homepage-features' ) ) {
                dynamic_sidebar( 'homepage-features' );
            }
            ?>
        <?php endif; ?>
        
    </div>
</section>