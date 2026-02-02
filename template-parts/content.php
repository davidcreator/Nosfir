<?php
/**
 * Template part for displaying posts
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

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php
                the_post_thumbnail(
                    'medium_large',
                    array(
                        'alt' => the_title_attribute( array( 'echo' => false ) ),
                        'loading' => 'lazy',
                    )
                );
                ?>
            </a>
        </div><!-- .post-thumbnail -->
    <?php endif; ?>

    <header class="entry-header">
        <?php
        if ( 'post' === get_post_type() ) :
            ?>
            <div class="entry-meta">
                <?php
                nosfir_posted_on();
                ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
        
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;
        ?>
        
        <?php if ( 'post' === get_post_type() ) : ?>
            <div class="entry-meta entry-meta-bottom">
                <?php nosfir_posted_by(); ?>
            </div>
        <?php endif; ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php
        if ( is_singular() ) :
            the_content();
        else :
            the_excerpt();
        endif;
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php if ( ! is_singular() ) : ?>
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e( 'Read More', 'nosfir' ); ?>
                <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </a>
        <?php endif; ?>
    </footer><!-- .entry-footer -->
    
</article><!-- #post-<?php the_ID(); ?> -->