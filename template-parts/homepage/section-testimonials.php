<?php
/**
 * Homepage Section: Testimonials
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
$subtitle      = get_theme_mod( 'nosfir_homepage_testimonials_subtitle', __( 'Testimonials', 'nosfir' ) );
$title         = get_theme_mod( 'nosfir_homepage_testimonials_title', __( 'What Our Clients Say', 'nosfir' ) );
$description   = get_theme_mod( 'nosfir_homepage_testimonials_description', '' );
$style         = get_theme_mod( 'nosfir_homepage_testimonials_style', 'slider' ); // slider, grid, cards
$columns       = get_theme_mod( 'nosfir_homepage_testimonials_columns', 3 );
$show_rating   = get_theme_mod( 'nosfir_homepage_testimonials_show_rating', true );
$show_image    = get_theme_mod( 'nosfir_homepage_testimonials_show_image', true );
$autoplay      = get_theme_mod( 'nosfir_homepage_testimonials_autoplay', true );
$autoplay_speed = get_theme_mod( 'nosfir_homepage_testimonials_autoplay_speed', 5000 );

/*
|--------------------------------------------------------------------------
| Testimonials Data
|--------------------------------------------------------------------------
*/
$testimonials = apply_filters( 'nosfir_homepage_testimonials', array(
    array(
        'content' => get_theme_mod( 'nosfir_homepage_testimonial_1_content', __( 'Working with this team was an absolute pleasure. They delivered our project on time and exceeded our expectations. Highly recommended!', 'nosfir' ) ),
        'author'  => get_theme_mod( 'nosfir_homepage_testimonial_1_author', __( 'John Smith', 'nosfir' ) ),
        'role'    => get_theme_mod( 'nosfir_homepage_testimonial_1_role', __( 'CEO, Company Inc.', 'nosfir' ) ),
        'image'   => get_theme_mod( 'nosfir_homepage_testimonial_1_image', '' ),
        'rating'  => get_theme_mod( 'nosfir_homepage_testimonial_1_rating', 5 ),
    ),
    array(
        'content' => get_theme_mod( 'nosfir_homepage_testimonial_2_content', __( 'The quality of work and attention to detail is outstanding. They truly understand what their clients need and deliver exceptional results.', 'nosfir' ) ),
        'author'  => get_theme_mod( 'nosfir_homepage_testimonial_2_author', __( 'Sarah Johnson', 'nosfir' ) ),
        'role'    => get_theme_mod( 'nosfir_homepage_testimonial_2_role', __( 'Marketing Director, Agency XYZ', 'nosfir' ) ),
        'image'   => get_theme_mod( 'nosfir_homepage_testimonial_2_image', '' ),
        'rating'  => get_theme_mod( 'nosfir_homepage_testimonial_2_rating', 5 ),
    ),
    array(
        'content' => get_theme_mod( 'nosfir_homepage_testimonial_3_content', __( 'Professional, responsive, and incredibly talented. Our website has never looked better. Thank you for the amazing work!', 'nosfir' ) ),
        'author'  => get_theme_mod( 'nosfir_homepage_testimonial_3_author', __( 'Michael Brown', 'nosfir' ) ),
        'role'    => get_theme_mod( 'nosfir_homepage_testimonial_3_role', __( 'Founder, Startup Co.', 'nosfir' ) ),
        'image'   => get_theme_mod( 'nosfir_homepage_testimonial_3_image', '' ),
        'rating'  => get_theme_mod( 'nosfir_homepage_testimonial_3_rating', 5 ),
    ),
) );

// Remove empty testimonials
$testimonials = array_filter( $testimonials, function( $testimonial ) {
    return ! empty( $testimonial['content'] ) && ! empty( $testimonial['author'] );
});

if ( empty( $testimonials ) ) {
    return;
}

/*
|--------------------------------------------------------------------------
| Section Classes
|--------------------------------------------------------------------------
*/
$section_classes = array(
    'homepage-section',
    'homepage-testimonials',
    'testimonials-style-' . sanitize_html_class( $style ),
);

if ( $style !== 'slider' ) {
    $section_classes[] = 'testimonials-columns-' . absint( $columns );
}

/**
 * Filter section classes
 */
$section_classes = apply_filters( 'nosfir_homepage_testimonials_classes', $section_classes );

/*
|--------------------------------------------------------------------------
| Data attributes for slider
|--------------------------------------------------------------------------
*/
$slider_data = array();
if ( $style === 'slider' ) {
    $slider_data['data-slider'] = 'true';
    $slider_data['data-autoplay'] = $autoplay ? 'true' : 'false';
    $slider_data['data-autoplay-speed'] = absint( $autoplay_speed );
}

$slider_data_string = '';
foreach ( $slider_data as $key => $value ) {
    $slider_data_string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
}

?>

<section id="homepage-testimonials" class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"<?php echo $slider_data_string; ?>>
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
        
        <!-- Quote Icon -->
        <div class="testimonials-quote-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
        </div>
        
        <div class="testimonials-wrapper">
            
            <?php if ( $style === 'slider' ) : ?>
                <!-- Slider Navigation -->
                <button class="testimonials-nav testimonials-prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'nosfir' ); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15,18 9,12 15,6"/>
                    </svg>
                </button>
            <?php endif; ?>
            
            <div class="testimonials-container<?php echo $style === 'slider' ? ' testimonials-slider' : ' testimonials-grid'; ?>" style="<?php echo $style !== 'slider' ? '--columns: ' . absint( $columns ) . ';' : ''; ?>">
                
                <?php foreach ( $testimonials as $index => $testimonial ) : ?>
                    <div class="testimonial-item<?php echo $style === 'slider' && $index === 0 ? ' active' : ''; ?>">
                        <div class="testimonial-inner">
                            
                            <?php if ( $show_rating && ! empty( $testimonial['rating'] ) ) : ?>
                                <div class="testimonial-rating" aria-label="<?php printf( esc_attr__( 'Rating: %d out of 5 stars', 'nosfir' ), absint( $testimonial['rating'] ) ); ?>">
                                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                        <svg class="star<?php echo $i <= $testimonial['rating'] ? ' filled' : ''; ?>" 
                                             width="20" 
                                             height="20" 
                                             viewBox="0 0 24 24" 
                                             fill="<?php echo $i <= $testimonial['rating'] ? 'currentColor' : 'none'; ?>" 
                                             stroke="currentColor" 
                                             stroke-width="2"
                                             aria-hidden="true">
                                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                            
                            <blockquote class="testimonial-content">
                                <p><?php echo wp_kses_post( $testimonial['content'] ); ?></p>
                            </blockquote>
                            
                            <div class="testimonial-author">
                                <?php if ( $show_image ) : ?>
                                    <div class="testimonial-author-image">
                                        <?php if ( ! empty( $testimonial['image'] ) ) : ?>
                                            <img src="<?php echo esc_url( $testimonial['image'] ); ?>" 
                                                 alt="<?php echo esc_attr( $testimonial['author'] ); ?>" 
                                                 loading="lazy">
                                        <?php else : ?>
                                            <div class="testimonial-author-placeholder">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="testimonial-author-info">
                                    <cite class="testimonial-author-name">
                                        <?php echo esc_html( $testimonial['author'] ); ?>
                                    </cite>
                                    
                                    <?php if ( ! empty( $testimonial['role'] ) ) : ?>
                                        <span class="testimonial-author-role">
                                            <?php echo esc_html( $testimonial['role'] ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </div>
            
            <?php if ( $style === 'slider' ) : ?>
                <!-- Slider Navigation -->
                <button class="testimonials-nav testimonials-next" aria-label="<?php esc_attr_e( 'Next testimonial', 'nosfir' ); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9,18 15,12 9,6"/>
                    </svg>
                </button>
            <?php endif; ?>
            
        </div>
        
        <?php if ( $style === 'slider' && count( $testimonials ) > 1 ) : ?>
            <!-- Slider Dots -->
            <div class="testimonials-dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial navigation', 'nosfir' ); ?>">
                <?php for ( $i = 0; $i < count( $testimonials ); $i++ ) : ?>
                    <button class="testimonials-dot<?php echo $i === 0 ? ' active' : ''; ?>" 
                            role="tab"
                            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                            aria-label="<?php printf( esc_attr__( 'Go to testimonial %d', 'nosfir' ), $i + 1 ); ?>"
                            data-index="<?php echo $i; ?>">
                        <span class="screen-reader-text">
                            <?php printf( esc_html__( 'Testimonial %d', 'nosfir' ), $i + 1 ); ?>
                        </span>
                    </button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
        
        <?php
        /**
         * Hook: nosfir_homepage_testimonials_after
         */
        do_action( 'nosfir_homepage_testimonials_after' );
        ?>
        
    </div>
</section>