<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
            <?php do_action( 'nosfir_content_bottom' ); ?>
            
        </div><!-- .container -->
    </div><!-- #content -->

    <?php do_action( 'nosfir_before_footer' ); ?>

    <footer id="colophon" class="site-footer" role="contentinfo">
        
        <?php
        /**
         * Footer CTA section (optional)
         */
        do_action( 'nosfir_footer_cta' );
        ?>
        
        <?php if ( nosfir_has_footer_widgets() ) : ?>
        <div class="footer-main">
            <div class="container">
                
                <?php
                /**
                 * Footer widgets
                 */
                nosfir_footer_widgets();
                ?>
                
            </div><!-- .container -->
        </div><!-- .footer-main -->
        <?php endif; ?>
        
        <div class="footer-bottom">
            <div class="container">
                
                <div class="site-info">
                    <?php nosfir_footer_credits(); ?>
                </div><!-- .site-info -->
                
                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'nosfir' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                        )
                    );
                    ?>
                </nav><!-- .footer-navigation -->
                <?php endif; ?>
                
                <?php if ( has_nav_menu( 'social' ) ) : ?>
                <div class="footer-social">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'social',
                            'menu_class'     => 'social-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'link_before'    => '<span class="screen-reader-text">',
                            'link_after'     => '</span>',
                        )
                    );
                    ?>
                </div><!-- .footer-social -->
                <?php endif; ?>
                
            </div><!-- .container -->
        </div><!-- .footer-bottom -->
        
    </footer><!-- #colophon -->

    <?php do_action( 'nosfir_after_footer' ); ?>

</div><!-- #page -->

<?php do_action( 'nosfir_after_site' ); ?>

<?php wp_footer(); ?>

</body>
</html>