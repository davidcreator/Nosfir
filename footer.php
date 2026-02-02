<?php
/**
 * The footer for our theme
 *
 * @package Nosfir
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

    </div><!-- #content .site-content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <?php if ( function_exists( 'nosfir_has_footer_widgets' ) && nosfir_has_footer_widgets() ) : ?>
                <?php nosfir_footer_widgets(); ?>
            <?php endif; ?>
            
            <div class="footer-credits">
                <?php if ( function_exists( 'nosfir_footer_credits' ) ) : ?>
                    <?php nosfir_footer_credits(); ?>
                <?php else : ?>
                    <span class="copyright">
                        &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> 
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
                    </span>
                <?php endif; ?>
            </div>
            
        </div>
    </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>