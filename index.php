<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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

get_header();
?>

<main id="primary" class="content-area">

    <?php do_action( 'nosfir_before_main_content' ); ?>

    <?php if ( have_posts() ) : ?>

        <?php if ( is_home() && ! is_front_page() ) : ?>
            <header class="page-header">
                <h1 class="page-title screen-reader-text">
                    <?php single_post_title(); ?>
                </h1>
            </header>
        <?php endif; ?>

        <div class="posts-loop">
            
            <?php
            // Start the Loop
            while ( have_posts() ) :
                the_post();
                
                /**
                 * Include the Post-Type-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                 */
                get_template_part( 'template-parts/content', get_post_type() );
                
            endwhile;
            ?>
            
        </div><!-- .posts-loop -->

        <?php
        // Pagination
        nosfir_pagination();
        ?>

    <?php else : ?>

        <?php get_template_part( 'template-parts/content', 'none' ); ?>

    <?php endif; ?>

    <?php do_action( 'nosfir_after_main_content' ); ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();