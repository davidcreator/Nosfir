<?php
/**
 * Template part for displaying posts
 *
 * @package Nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-card__thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium_large' ); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="post-card__content">
        
        <div class="post-card__meta">
            <span class="post-card__date">
                <?php echo get_the_date(); ?>
            </span>
            <?php if ( has_category() ) : ?>
                <span class="post-card__category">
                    <?php the_category( ', ' ); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <h2 class="post-card__title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>
        
        <div class="post-card__excerpt">
            <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
        </div>
        
        <a href="<?php the_permalink(); ?>" class="nosfir-btn nosfir-btn--sm nosfir-btn--secondary">
            <?php esc_html_e( 'Read More', 'nosfir' ); ?>
        </a>
        
    </div>
    
</article>