<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

		</div><!-- .container -->
	</div><!-- #content -->

	<?php do_action( 'nosfir_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="container">

			<?php
			/**
			 * Functions hooked in to nosfir_footer action
			 *
			 * @hooked nosfir_footer_widgets - 10
			 * @hooked nosfir_footer_links   - 20
			 * @hooked nosfir_credit         - 30
			 */
			do_action( 'nosfir_footer' );
			?>

		</div><!-- .container -->
	</footer><!-- #colophon -->

	<?php do_action( 'nosfir_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>