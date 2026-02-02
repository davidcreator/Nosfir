<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

    <?php do_action( 'nosfir_before_search_content' ); ?>

    <?php if ( have_posts() ) : ?>

        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'Search Results for: %s', 'nosfir' ),
                    '<span>' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            
            <div class="search-results-count">
                <?php
                printf(
                    /* translators: %d: number of results */
                    esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'nosfir' ) ),
                    (int) $wp_query->found_posts
                );
                ?>
            </div>
        </header><!-- .page-header -->

        <div class="search-form-container">
            <?php get_search_form(); ?>
        </div>

        <div class="posts-loop">
            
            <?php
            while ( have_posts() ) :
                the_post();
                
                get_template_part( 'template-parts/content', 'search' );
                
            endwhile;
            ?>
            
        </div><!-- .posts-loop -->

        <?php
        nosfir_pagination();
        ?>

    <?php else : ?>

        <header class="page-header">
            <h1 class="page-title">
                <?php esc_html_e( 'Nothing Found', 'nosfir' ); ?>
            </h1>
        </header><!-- .page-header -->

        <div class="page-content">
            <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'nosfir' ); ?></p>
            
            <?php get_search_form(); ?>
        </div><!-- .page-content -->

    <?php endif; ?>

    <?php do_action( 'nosfir_after_search_content' ); ?>

</main><!-- #primary -->

<?php
get_sidebar();
get_footer();