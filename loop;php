<?php
/**
 * The loop template file.
 *
 * Included on pages like index.php, archive.php and search.php to display a loop of posts.
 * Learn more: https://developer.wordpress.org/themes/basics/the-loop/
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hook: nosfir_loop_before
 *
 * Executado antes do início do loop de posts.
 *
 * @hooked nosfir_loop_header - 10
 */
do_action( 'nosfir_loop_before' );

while ( have_posts() ) :
	the_post();

	/**
	 * Hook: nosfir_loop_post_before
	 *
	 * Executado antes de cada post no loop.
	 */
	do_action( 'nosfir_loop_post_before' );

	/**
	 * Include the Post-Format-specific template for the content.
	 * If you want to override this in a child theme, then include a file
	 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
	 *
	 * Hierarchy:
	 * 1. content-{post-format}.php (e.g., content-video.php)
	 * 2. content-{post-type}.php (e.g., content-product.php)
	 * 3. content.php
	 */
	$post_format = get_post_format();
	$post_type   = get_post_type();

	if ( $post_format ) {
		// Tenta carregar template específico do formato (content-video.php, content-gallery.php, etc.)
		get_template_part( 'content', $post_format );
	} elseif ( 'post' !== $post_type ) {
		// Tenta carregar template específico do post type (content-product.php, content-portfolio.php, etc.)
		get_template_part( 'content', $post_type );
	} else {
		// Fallback para o template padrão
		get_template_part( 'content' );
	}

	/**
	 * Hook: nosfir_loop_post_after
	 *
	 * Executado após cada post no loop.
	 */
	do_action( 'nosfir_loop_post_after' );

endwhile;

/**
 * Hook: nosfir_loop_after
 *
 * Executado após o término do loop de posts.
 *
 * @hooked nosfir_paging_nav - 10
 */
do_action( 'nosfir_loop_after' );