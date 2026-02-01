<?php
/**
 * Template used to display post content.
 *
 * Este template é usado para exibir o conteúdo de posts no loop principal.
 * Utiliza hooks para permitir fácil customização por child themes e plugins.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * Functions hooked in to nosfir_loop_post action.
	 *
	 * @hooked nosfir_post_header          - 10
	 * @hooked nosfir_post_thumbnail       - 20
	 * @hooked nosfir_post_content         - 30
	 * @hooked nosfir_post_taxonomy        - 40
	 * @hooked nosfir_post_footer          - 50
	 */
	do_action( 'nosfir_loop_post' );
	?>

</article><!-- #post-<?php the_ID(); ?> -->