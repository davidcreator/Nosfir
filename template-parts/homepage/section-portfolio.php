<?php
/**
 * Homepage Section: Portfolio
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
$subtitle       = get_theme_mod( 'nosfir_homepage_portfolio_subtitle', __( 'Our Work', 'nosfir' ) );
$title          = get_theme_mod( 'nosfir_homepage_portfolio_title', __( 'Featured Projects', 'nosfir' ) );
$description    = get_theme_mod( 'nosfir_homepage_portfolio_description', '' );
$posts_count    = get_theme_mod( 'nosfir_homepage_portfolio_count', 6 );
$columns        = get_theme_mod( 'nosfir_homepage_portfolio_columns', 3 );
$style          = get_theme_mod( 'nosfir_homepage_portfolio_style', 'grid' ); // grid, masonry, carousel
$show_filter    = get_theme_mod( 'nosfir_homepage_portfolio_show_filter', true );
$show_overlay   = get_theme_mod( 'nosfir_homepage_portfolio_show_overlay', true );
$button_text    = get_theme_mod( 'nosfir_homepage_portfolio_button_text', __( 'View All Projects', 'nosfir' ) );
$button_url     = get_theme_mod( 'nosfir_homepage_portfolio_button_url', '' );

/*
|--------------------------------------------------------------------------
| Determine Post Type
|--------------------------------------------------------------------------
*/
$portfolio_post_type = 'portfolio';

// Check for common portfolio plugin post types
if ( ! post_type_exists( 'portfolio' ) ) {
    if ( post_type_exists( 'project' ) ) {
        $portfolio_post_type = 'project';
    } elseif ( post_type_exists( 'work' ) ) {
        $portfolio_post_type = 'work';
    } else {
        // Fallback to regular posts with a specific category
        $portfolio_post_type = 'post';
    }
}

$portfolio_post_type = apply_filters( 'nosfir_portfolio_post_type', $portfolio_post_type );

/*
|--------------------------------------------------------------------------
| Query Portfolio Items
|--------------------------------------------------------------------------
*/
$query_args = array(
    'post_type'           => $portfolio_post_type,
    'posts_per_page'      => absint( $posts_count ),
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
);

// If using posts, filter by category
if ( $portfolio_post_type === 'post' ) {
    $portfolio_category = get_theme_mod( 'nosfir_homepage_portfolio_category', '' );
    if ( $portfolio_category ) {
        $query_args['cat'] = $portfolio_category;
    }
}

$query_args = apply_filters( 'nosfir_homepage_portfolio_query_args', $query_args );

$portfolio_query = new WP_Query( $query_args );

if ( ! $portfolio_query->have_posts() ) {
    // Show placeholder if no items and user can edit
    if ( current_user_can( 'edit_posts' ) ) {
        ?>
        <section id="homepage-portfolio" class="homepage-section homepage-portfolio portfolio-empty">
            <div class="container">
                <div class="empty-state">
                    <p><?php esc_html_e( 'No portfolio items found. Add some projects to display them here.', 'nosfir' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . $portfolio_post_type ) ); ?>" class="button">
                        <?php esc_html_e( 'Add Project', 'nosfir' ); ?>
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
    return;
}

/*
|--------------------------------------------------------------------------
| Get Categories/Terms for Filter
|--------------------------------------------------------------------------
*/
$filter_terms = array();

if ( $show_filter ) {
    $taxonomy = $portfolio_post_type === 'post' ? 'category' : 'portfolio_category';
    
    // Try different taxonomy names
    $possible_taxonomies = array( 'portfolio_category', 'project_category', 'portfolio_cat', 'project_cat', 'category' );
    
    foreach ( $possible_taxonomies as $tax ) {
        if ( taxonomy_exists( $tax ) ) {
            $filter_terms = get_terms( array(
                'taxonomy'   => $tax,
                'hide_empty' => true,
            ) );
            
            if ( ! is_wp_error( $filter_terms ) && ! empty( $filter_terms ) ) {
                break;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Section Classes
|--------------------------------------------------------------------------
*/
$section_classes = array(
    'homepage-section',
    'homepage-portfolio',
    'portfolio-style-' . sanitize_html_class( $style ),
    'portfolio-columns-' . absint( $columns ),
);

if ( $show_overlay ) {
    $section_classes[] = 'has-overlay';
}

/**
 * Filter section classes
 */
$section_classes = apply_filters( 'nosfir_homepage_portfolio_classes', $section_classes );

?>

<section id="homepage-portfolio" class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
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
        
        <?php if ( $show_filter && ! empty( $filter_terms ) && ! is_wp_error( $filter_terms ) ) : ?>
            <div class="portfolio-filter">
                <button class="filter-button active" data-filter="*">
                    <?php esc_html_e( 'All', 'nosfir' ); ?>
                </button>
                <?php foreach ( $filter_terms as $term ) : ?>
                    <button class="filter-button" data-filter=".<?php echo esc_attr( $term->slug ); ?>">
                        <?php echo esc_html( $term->name ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="portfolio-grid" style="--columns: <?php echo absint( $columns ); ?>">
            <?php
            while ( $portfolio_query->have_posts() ) :
                $portfolio_query->the_post();
                
                // Get terms for filtering
                $item_terms = wp_get_post_terms( get_the_ID(), $filter_terms[0]->taxonomy ?? 'category', array( 'fields' => 'slugs' ) );
                $term_classes = is_wp_error( $item_terms ) ? '' : implode( ' ', $item_terms );
                ?>
                <article <?php post_class( 'portfolio-item ' . $term_classes ); ?>>
                    <div class="portfolio-item-inner">
                        
                        <div class="portfolio-thumbnail">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium_large' ); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="portfolio-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21,15 16,10 5,21"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ( $show_overlay ) : ?>
                                <div class="portfolio-overlay">
                                    <div class="portfolio-overlay-content">
                                        <h3 class="portfolio-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <?php
                                        $project_terms = wp_get_post_terms( get_the_ID(), $filter_terms[0]->taxonomy ?? 'category' );
                                        if ( ! is_wp_error( $project_terms ) && ! empty( $project_terms ) ) :
                                            ?>
                                            <span class="portfolio-category">
                                                <?php echo esc_html( $project_terms[0]->name ); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <div class="portfolio-actions">
                                            <a href="<?php the_permalink(); ?>" class="portfolio-link" aria-label="<?php esc_attr_e( 'View project', 'nosfir' ); ?>">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                    <polyline points="15,3 21,3 21,9"/>
                                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                                </svg>
                                            </a>
                                            
                                            <?php
                                            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                                            if ( $thumbnail_url ) :
                                                ?>
                                                <a href="<?php echo esc_url( $thumbnail_url ); ?>" class="portfolio-lightbox" data-lightbox="portfolio" aria-label="<?php esc_attr_e( 'View larger image', 'nosfir' ); ?>">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8"/>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                                        <line x1="11" y1="8" x2="11" y2="14"/>
                                                        <line x1="8" y1="11" x2="14" y2="11"/>
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ( ! $show_overlay ) : ?>
                            <div class="portfolio-info">
                                <h3 class="portfolio-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <?php
                                $project_terms = wp_get_post_terms( get_the_ID(), $filter_terms[0]->taxonomy ?? 'category' );
                                if ( ! is_wp_error( $project_terms ) && ! empty( $project_terms ) ) :
                                    ?>
                                    <span class="portfolio-category">
                                        <?php echo esc_html( $project_terms[0]->name ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php wp_reset_postdata(); ?>
        
        <?php if ( $button_text && $button_url ) : ?>
            <div class="section-footer">
                <a href="<?php echo esc_url( $button_url ); ?>" class="button button-outline">
                    <?php echo esc_html( $button_text ); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <?php
        /**
         * Hook: nosfir_homepage_portfolio_after
         */
        do_action( 'nosfir_homepage_portfolio_after' );
        ?>
        
    </div>
</section>