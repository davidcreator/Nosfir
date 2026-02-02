<?php
/**
 * Homepage Section: About
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
$subtitle        = get_theme_mod( 'nosfir_homepage_about_subtitle', __( 'About Us', 'nosfir' ) );
$title           = get_theme_mod( 'nosfir_homepage_about_title', __( 'We Create Digital Excellence', 'nosfir' ) );
$description     = get_theme_mod( 'nosfir_homepage_about_description', '' );
$content         = get_theme_mod( 'nosfir_homepage_about_content', '' );
$image           = get_theme_mod( 'nosfir_homepage_about_image', '' );
$image_position  = get_theme_mod( 'nosfir_homepage_about_image_position', 'right' ); // left, right
$video_url       = get_theme_mod( 'nosfir_homepage_about_video_url', '' );
$button_text     = get_theme_mod( 'nosfir_homepage_about_button_text', __( 'Learn More', 'nosfir' ) );
$button_url      = get_theme_mod( 'nosfir_homepage_about_button_url', '' );
$show_stats      = get_theme_mod( 'nosfir_homepage_about_show_stats', true );

/*
|--------------------------------------------------------------------------
| Stats/Counters
|--------------------------------------------------------------------------
*/
$stats = array();

if ( $show_stats ) {
    $stats = apply_filters( 'nosfir_homepage_about_stats', array(
        array(
            'number' => get_theme_mod( 'nosfir_homepage_about_stat_1_number', '150+' ),
            'label'  => get_theme_mod( 'nosfir_homepage_about_stat_1_label', __( 'Projects Completed', 'nosfir' ) ),
            'icon'   => 'briefcase',
        ),
        array(
            'number' => get_theme_mod( 'nosfir_homepage_about_stat_2_number', '50+' ),
            'label'  => get_theme_mod( 'nosfir_homepage_about_stat_2_label', __( 'Happy Clients', 'nosfir' ) ),
            'icon'   => 'users',
        ),
        array(
            'number' => get_theme_mod( 'nosfir_homepage_about_stat_3_number', '10+' ),
            'label'  => get_theme_mod( 'nosfir_homepage_about_stat_3_label', __( 'Years Experience', 'nosfir' ) ),
            'icon'   => 'award',
        ),
        array(
            'number' => get_theme_mod( 'nosfir_homepage_about_stat_4_number', '24/7' ),
            'label'  => get_theme_mod( 'nosfir_homepage_about_stat_4_label', __( 'Support Available', 'nosfir' ) ),
            'icon'   => 'headphones',
        ),
    ) );
    
    // Remove empty stats
    $stats = array_filter( $stats, function( $stat ) {
        return ! empty( $stat['number'] ) && ! empty( $stat['label'] );
    });
}

/*
|--------------------------------------------------------------------------
| Section Classes
|--------------------------------------------------------------------------
*/
$section_classes = array(
    'homepage-section',
    'homepage-about',
    'about-image-' . sanitize_html_class( $image_position ),
);

if ( $image ) {
    $section_classes[] = 'has-image';
}

if ( $video_url ) {
    $section_classes[] = 'has-video';
}

/**
 * Filter section classes
 */
$section_classes = apply_filters( 'nosfir_homepage_about_classes', $section_classes );

?>

<section id="homepage-about" class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        
        <div class="about-wrapper">
            
            <?php if ( $image || $video_url ) : ?>
                <!-- About Media -->
                <div class="about-media">
                    <div class="about-media-inner">
                        
                        <?php if ( $image ) : ?>
                            <div class="about-image">
                                <img src="<?php echo esc_url( $image ); ?>" 
                                     alt="<?php echo esc_attr( $title ); ?>" 
                                     loading="lazy">
                                
                                <?php if ( $video_url ) : ?>
                                    <a href="<?php echo esc_url( $video_url ); ?>" 
                                       class="video-play-button" 
                                       data-video="<?php echo esc_url( $video_url ); ?>"
                                       aria-label="<?php esc_attr_e( 'Play video', 'nosfir' ); ?>">
                                        <span class="play-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        /**
                         * Hook: nosfir_homepage_about_after_image
                         */
                        do_action( 'nosfir_homepage_about_after_image' );
                        ?>
                        
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- About Content -->
            <div class="about-content">
                
                <header class="section-header text-left">
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
                
                <?php if ( $content ) : ?>
                    <div class="about-text">
                        <?php echo wp_kses_post( $content ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( ! empty( $stats ) ) : ?>
                    <div class="about-stats">
                        <?php foreach ( $stats as $stat ) : ?>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $stat['number'] ) ); ?>">
                                    <?php echo esc_html( $stat['number'] ); ?>
                                </div>
                                <div class="stat-label"><?php echo esc_html( $stat['label'] ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( $button_text && $button_url ) : ?>
                    <div class="about-buttons">
                        <a href="<?php echo esc_url( $button_url ); ?>" class="button button-primary">
                            <?php echo esc_html( $button_text ); ?>
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M7 10h6m0 0l-3-3m3 3l-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php
                /**
                 * Hook: nosfir_homepage_about_after_content
                 */
                do_action( 'nosfir_homepage_about_after_content' );
                ?>
                
            </div>
            
        </div>
        
    </div>
</section>