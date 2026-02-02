<?php
/**
 * The main template file
 *
 * @package Nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Determine sidebar
$sidebar_position = get_theme_mod( 'nosfir_sidebar_position', 'right' );
$has_sidebar = is_active_sidebar( 'sidebar-1' ) && $sidebar_position !== 'none';

$wrapper_class = 'content-wrapper';
if ( $has_sidebar ) {
    $wrapper_class .= ' has-sidebar';
    if ( $sidebar_position === 'left' ) {
        $wrapper_class .= ' sidebar-left';
    }
} else {
    $wrapper_class .= ' no-sidebar';
}
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="<?php echo esc_attr( $wrapper_class ); ?>">
            
            <div id="main-content">
                <?php
                if ( have_posts() ) :
                    
                    if ( is_home() && ! is_front_page() ) :
                        ?>
                        <header class="page-header">
                            <h1 class="page-title"><?php single_post_title(); ?></h1>
                        </header>
                        <?php
                    endif;
                    
                    echo '<div class="posts-grid">';
                    
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content', get_post_type() );
                    endwhile;
                    
                    echo '</div>';
                    
                    // Pagination
                    the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => '&larr;',
                        'next_text' => '&rarr;',
                    ) );
                    
                else :
                    get_template_part( 'template-parts/content', 'none' );
                endif;
                ?>
            </div><!-- #main-content -->
            
            <?php if ( $has_sidebar ) : ?>
                <aside id="secondary" class="widget-area" role="complementary">
                    <?php dynamic_sidebar( 'sidebar-1' ); ?>
                </aside>
            <?php endif; ?>
            
        </div><!-- .content-wrapper -->
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();