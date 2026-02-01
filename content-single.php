<?php
/**
 * Template used to display post content on single pages.
 *
 * @package Nosfir
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nosfir-single-post' ); ?>>

	<?php
	/**
	 * Hook: nosfir_single_post_top
	 *
	 * @hooked nosfir_single_post_thumbnail - 10
	 */
	do_action( 'nosfir_single_post_top' );
	?>

	<header class="entry-header">
		<?php
		/**
		 * Hook: nosfir_single_post_header
		 *
		 * @hooked nosfir_post_categories - 10
		 * @hooked nosfir_single_post_title - 20
		 * @hooked nosfir_post_meta - 30
		 */
		do_action( 'nosfir_single_post_header' );
		?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		/**
		 * Hook: nosfir_single_post_content_before
		 */
		do_action( 'nosfir_single_post_content_before' );

		the_content();

		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'nosfir' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'nosfir' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text">, </span>',
		) );

		/**
		 * Hook: nosfir_single_post_content_after
		 *
		 * @hooked nosfir_post_tags - 10
		 */
		do_action( 'nosfir_single_post_content_after' );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
		/**
		 * Hook: nosfir_single_post_footer
		 *
		 * @hooked nosfir_post_author_bio - 10
		 * @hooked nosfir_post_share_buttons - 20
		 */
		do_action( 'nosfir_single_post_footer' );
		?>
	</footer><!-- .entry-footer -->

	<?php
	/**
	 * Hook: nosfir_single_post_bottom
	 *
	 * @hooked nosfir_post_navigation - 10
	 * @hooked nosfir_related_posts - 20
	 * @hooked nosfir_display_comments - 30
	 */
	do_action( 'nosfir_single_post_bottom' );
	?>

</article><!-- #post-<?php the_ID(); ?> -->