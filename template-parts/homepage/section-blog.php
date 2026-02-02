<?php
/**
 * Homepage Section: Blog/Latest Posts
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get section settings
$title       = get_theme_mod( 'nosfir_homepage_blog_title', __( 'Latest from the Blog', 'nosfir' ) );
$subtitle    = get_theme_mod( 'nosfir_homepage_blog_subtitle', '' );
$posts_count = get_theme_mod( 'nosfir_homepage_blog_count', 3 );
$show_button = get_theme_mod( 'nosfir_homepage_blog_button', true );
$button_text = get_theme_mod( 'nosfir_homepage_blog_button_text', __( 'View All Posts', 'nosfir' ) );

// Query latest posts
$latest_posts = new WP_Query(
    array(
        'post_type'           => 'post',
        'posts_per_page'      => absint( $posts_count ),
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
    )
);

if ( ! $latest_posts->have_posts() ) {
    return;
}

?>

<section id="homepage-blog" class="homepage-section homepage-blog">
    <div class="container">
        
        <header class="section-header">
            <?php if ( $subtitle ) : ?>
                <span class="section-subtitle"><?php echo esc_html( $subtitle ); ?></span>
            <?php endif; ?>
            
            <?php if ( $title ) : ?>
                <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
        </header>
        
        <div class="blog-posts-grid posts-grid">
            <?php
            while ( $latest_posts->have_posts() ) :
                $latest_posts->the_post();
                ?>
                <article <?php post_class( 'blog-post-card' ); ?>>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium_large' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?php echo get_the_date(); ?></span>
                            <?php
                            $categories = get_the_category();
                            if ( ! empty( $categories ) ) :
                                ?>
                                <span class="post-category">
                                    <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
                                        <?php echo esc_html( $categories[0]->name ); ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="post-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php esc_html_e( 'Read More', 'nosfir' ); ?>
                            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                    
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        
        <?php if ( $show_button ) : ?>
            <div class="section-footer">
                <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="button button-outline">
                    <?php echo esc_html( $button_text ); ?>
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</section>