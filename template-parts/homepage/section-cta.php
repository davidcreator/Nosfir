<?php
/**
 * Homepage Section: Call to Action
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get section settings
$title           = get_theme_mod( 'nosfir_homepage_cta_title', __( 'Ready to Get Started?', 'nosfir' ) );
$description     = get_theme_mod( 'nosfir_homepage_cta_description', '' );
$button_text     = get_theme_mod( 'nosfir_homepage_cta_button_text', __( 'Contact Us', 'nosfir' ) );
$button_url      = get_theme_mod( 'nosfir_homepage_cta_button_url', '' );
$background      = get_theme_mod( 'nosfir_homepage_cta_background', '' );
$background_color = get_theme_mod( 'nosfir_homepage_cta_bg_color', '' );

// Build styles
$section_styles = array();
if ( $background ) {
    $section_styles[] = 'background-image: url(' . esc_url( $background ) . ')';
}
if ( $background_color ) {
    $section_styles[] = 'background-color: ' . esc_attr( $background_color );
}

$style_attr = ! empty( $section_styles ) ? 'style="' . esc_attr( implode( '; ', $section_styles ) ) . '"' : '';

?>

<section id="homepage-cta" class="homepage-section homepage-cta" <?php echo $style_attr; ?>>
    <div class="cta-overlay" aria-hidden="true"></div>
    <div class="container">
        <div class="cta-content">
            
            <?php if ( $title ) : ?>
                <h2 class="cta-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            
            <?php if ( $description ) : ?>
                <p class="cta-description"><?php echo wp_kses_post( $description ); ?></p>
            <?php endif; ?>
            
            <?php if ( $button_text && $button_url ) : ?>
                <div class="cta-buttons">
                    <a href="<?php echo esc_url( $button_url ); ?>" class="button button-large button-white">
                        <?php echo esc_html( $button_text ); ?>
                    </a>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</section>