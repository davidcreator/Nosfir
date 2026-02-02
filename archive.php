<?php
/**
 * The template for displaying archive pages
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

    <?php do_action( 'nosfir_before_archive_content' ); ?>

    <?php if ( have_posts() ) : ?>

        <header class="page-header">
            <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="archive-description">', '</div>' );
            ?>
        </header><!-- .page-header -->

        <div class="posts-loop">
            
            <?php
            while ( have_posts() ) :
                the_post();
                
                get_template_part( 'template-parts/content', get_post_type() );
                
            endwhile;
            ?>
            
        </div><!-- .posts-loop -->

        <?php
        nosfir_pagination();
        ?>

    <?php else : ?>

        <?php get_template_part( 'template-parts/content', 'none' ); ?>

    <?php endif; ?>

    <?php do_action( 'nosfir_after_archive_content' ); ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();