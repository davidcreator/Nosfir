<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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

    <section class="error-404 not-found">
        
        <header class="page-header error-404-header">
            <h1 class="page-title"><?php esc_html_e( '404', 'nosfir' ); ?></h1>
            <p class="page-subtitle"><?php esc_html_e( 'Oops! That page can\'t be found.', 'nosfir' ); ?></p>
        </header><!-- .page-header -->

        <div class="page-content">
            
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'nosfir' ); ?></p>

            <?php get_search_form(); ?>

            <div class="error-404-widgets">
                
                <?php if ( nosfir_has_recent_posts() ) : ?>
                <div class="widget widget_recent_posts">
                    <h3 class="widget-title"><?php esc_html_e( 'Recent Posts', 'nosfir' ); ?></h3>
                    <ul>
                        <?php
                        $recent_posts = wp_get_recent_posts(
                            array(
                                'numberposts' => 5,
                                'post_status' => 'publish',
                            )
                        );
                        
                        foreach ( $recent_posts as $post ) :
                            ?>
                            <li>
                                <a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
                                    <?php echo esc_html( $post['post_title'] ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( nosfir_has_categories() ) : ?>
                <div class="widget widget_categories">
                    <h3 class="widget-title"><?php esc_html_e( 'Categories', 'nosfir' ); ?></h3>
                    <?php
                    wp_list_categories(
                        array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'show_count' => true,
                            'title_li'   => '',
                            'number'     => 10,
                        )
                    );
                    ?>
                </div>
                <?php endif; ?>
                
            </div><!-- .error-404-widgets -->

            <p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button">
                    <?php esc_html_e( 'Back to Homepage', 'nosfir' ); ?>
                </a>
            </p>

        </div><!-- .page-content -->
        
    </section><!-- .error-404 -->

</main><!-- #primary -->

<?php
get_footer();