<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="primary" class="content-area">

    <?php do_action( 'nosfir_before_single_content' ); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <?php do_action( 'nosfir_single_post_top' ); ?>
            
            <header class="entry-header">
                <?php
                // Categories
                if ( 'post' === get_post_type() ) {
                    $categories = get_the_category();
                    if ( ! empty( $categories ) ) {
                        echo '<div class="entry-categories">';
                        foreach ( $categories as $category ) {
                            echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="category-link">' . esc_html( $category->name ) . '</a>';
                        }
                        echo '</div>';
                    }
                }
                
                // Title
                the_title( '<h1 class="entry-title">', '</h1>' );
                
                // Meta
                if ( 'post' === get_post_type() ) :
                    ?>
                    <div class="entry-meta">
                        <?php
                        nosfir_posted_on();
                        nosfir_posted_by();
                        nosfir_reading_time();
                        ?>
                    </div>
                <?php endif; ?>
            </header><!-- .entry-header -->

            <?php
            // Featured Image
            if ( has_post_thumbnail() ) :
                ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php
                the_content();

                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </div><!-- .entry-content -->

            <footer class="entry-footer">
                <?php
                // Tags
                nosfir_post_tags();
                
                // Share buttons
                nosfir_social_share();
                ?>
            </footer><!-- .entry-footer -->
            
            <?php do_action( 'nosfir_single_post_bottom' ); ?>

        </article><!-- #post-<?php the_ID(); ?> -->

        <?php
        // Author box
        nosfir_author_box();
        
        // Related posts
        nosfir_related_posts();
        
        // Post navigation
        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
            )
        );

        // Comments
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;

    endwhile;
    ?>

    <?php do_action( 'nosfir_after_single_content' ); ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();