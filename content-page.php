<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Get page meta options
$hide_title = get_post_meta( get_the_ID(), '_nosfir_hide_title', true );
$page_subtitle = get_post_meta( get_the_ID(), '_nosfir_page_subtitle', true );
$page_layout = get_post_meta( get_the_ID(), '_nosfir_page_layout', true );
$header_style = get_post_meta( get_the_ID(), '_nosfir_header_style', true );
$show_breadcrumb = get_post_meta( get_the_ID(), '_nosfir_show_breadcrumb', true );
$show_featured_image = get_post_meta( get_the_ID(), '_nosfir_show_featured_image', true );
$featured_image_position = get_post_meta( get_the_ID(), '_nosfir_featured_image_position', true );
$enable_share = get_post_meta( get_the_ID(), '_nosfir_enable_share', true );
$custom_css_class = get_post_meta( get_the_ID(), '_nosfir_custom_css_class', true );

// Default values
if ( empty( $page_layout ) ) {
	$page_layout = 'default';
}
if ( empty( $header_style ) ) {
	$header_style = 'standard';
}
if ( empty( $featured_image_position ) ) {
	$featured_image_position = 'before-title';
}

// Build page classes
$page_classes = array( 'page-content-wrapper' );
if ( $page_layout && 'default' !== $page_layout ) {
	$page_classes[] = 'layout-' . esc_attr( $page_layout );
}
if ( $header_style ) {
	$page_classes[] = 'header-' . esc_attr( $header_style );
}
if ( $custom_css_class ) {
	$page_classes[] = esc_attr( $custom_css_class );
}
if ( has_post_thumbnail() && $show_featured_image !== 'hide' ) {
	$page_classes[] = 'has-featured-image';
	$page_classes[] = 'featured-image-' . esc_attr( $featured_image_position );
}

// Check if page has children
$children = get_pages( array(
	'child_of' => get_the_ID(),
	'sort_column' => 'menu_order',
	'sort_order' => 'ASC',
) );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $page_classes ); ?>>
	
	<?php
	// Breadcrumb
	if ( $show_breadcrumb !== 'hide' && ! is_front_page() ) :
		nosfir_breadcrumb();
	endif;
	?>
	
	<?php
	// Featured image - Hero style
	if ( has_post_thumbnail() && $show_featured_image !== 'hide' && 'hero' === $featured_image_position ) :
		$featured_image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		?>
		<div class="page-hero" style="background-image: url(<?php echo esc_url( $featured_image_url ); ?>);">
			<div class="hero-overlay"></div>
			<div class="hero-content">
				<div class="container">
					<?php if ( ! $hide_title ) : ?>
						<h1 class="page-title hero-title"><?php the_title(); ?></h1>
					<?php endif; ?>
					
					<?php if ( $page_subtitle ) : ?>
						<p class="page-subtitle hero-subtitle"><?php echo wp_kses_post( $page_subtitle ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="page-inner">
		<header class="page-header">
			<?php
			// Featured image - Before title
			if ( has_post_thumbnail() && $show_featured_image !== 'hide' && 'before-title' === $featured_image_position ) : ?>
				<div class="page-featured-image featured-image-before-title">
					<?php the_post_thumbnail( 'large', array( 'class' => 'featured-image' ) ); ?>
					<?php
					$caption = get_the_post_thumbnail_caption();
					if ( $caption ) : ?>
						<figcaption class="featured-image-caption"><?php echo wp_kses_post( $caption ); ?></figcaption>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php if ( ! $hide_title && 'hero' !== $featured_image_position ) : ?>
				<h1 class="page-title"><?php the_title(); ?></h1>
			<?php endif; ?>
			
			<?php if ( $page_subtitle && 'hero' !== $featured_image_position ) : ?>
				<p class="page-subtitle"><?php echo wp_kses_post( $page_subtitle ); ?></p>
			<?php endif; ?>
			
			<?php
			// Featured image - After title
			if ( has_post_thumbnail() && $show_featured_image !== 'hide' && 'after-title' === $featured_image_position ) : ?>
				<div class="page-featured-image featured-image-after-title">
					<?php the_post_thumbnail( 'large', array( 'class' => 'featured-image' ) ); ?>
					<?php
					$caption = get_the_post_thumbnail_caption();
					if ( $caption ) : ?>
						<figcaption class="featured-image-caption"><?php echo wp_kses_post( $caption ); ?></figcaption>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php
			// Page meta information
			$show_page_meta = get_theme_mod( 'nosfir_show_page_meta', false );
			if ( $show_page_meta ) : ?>
				<div class="page-meta">
					<?php if ( get_theme_mod( 'nosfir_show_page_author', true ) ) : ?>
						<span class="page-author">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
							</svg>
							<?php
							printf(
								/* translators: %s: author name */
								esc_html__( 'By %s', 'nosfir' ),
								'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
							);
							?>
						</span>
					<?php endif; ?>
					
					<?php if ( get_theme_mod( 'nosfir_show_page_date', true ) ) : ?>
						<span class="page-date">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
							</svg>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
						</span>
					<?php endif; ?>
					
					<?php if ( get_theme_mod( 'nosfir_show_page_modified', false ) && get_the_date() !== get_the_modified_date() ) : ?>
						<span class="page-modified">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
							</svg>
							<?php
							printf(
								/* translators: %s: modified date */
								esc_html__( 'Updated: %s', 'nosfir' ),
								'<time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>'
							);
							?>
						</span>
					<?php endif; ?>
					
					<?php
					// Reading time (if content exists)
					if ( get_theme_mod( 'nosfir_show_page_reading_time', false ) && get_the_content() ) :
						$reading_time = nosfir_get_reading_time();
						?>
						<span class="page-reading-time">
							<svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
							</svg>
							<?php
							printf(
								/* translators: %s: reading time */
								esc_html( _n( '%s minute read', '%s minutes read', $reading_time, 'nosfir' ) ),
								$reading_time
							);
							?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</header><!-- .page-header -->
		
		<?php
		// Table of Contents for long pages
		if ( get_theme_mod( 'nosfir_page_toc', false ) ) :
			$content = get_the_content();
			preg_match_all( '/<h[2-3][^>]*>(.*?)<\/h[2-3]>/', $content, $matches );
			
			if ( ! empty( $matches[1] ) && count( $matches[1] ) > 3 ) : ?>
				<nav class="page-toc" aria-label="<?php esc_attr_e( 'Table of Contents', 'nosfir' ); ?>">
					<h3 class="toc-title"><?php esc_html_e( 'Table of Contents', 'nosfir' ); ?></h3>
					<ol class="toc-list">
						<?php foreach ( $matches[1] as $index => $heading ) : ?>
							<li>
								<a href="#heading-<?php echo esc_attr( $index ); ?>">
									<?php echo wp_kses_post( $heading ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>
			<?php endif;
		endif; ?>
		
		<div class="entry-content">
			<?php
			/**
			 * Hook before page content
			 */
			do_action( 'nosfir_before_page_content' );
			
			the_content();
			
			wp_link_pages(
				array(
					'before'      => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'nosfir' ) . '">',
					'after'       => '</nav>',
					'link_before' => '<span class="page-link-number">',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'nosfir' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				)
			);
			
			/**
			 * Hook after page content
			 */
			do_action( 'nosfir_after_page_content' );
			?>
		</div><!-- .entry-content -->
		
		<?php if ( ! empty( $children ) ) : ?>
			<div class="child-pages">
				<h3 class="child-pages-title"><?php esc_html_e( 'Related Pages', 'nosfir' ); ?></h3>
				<div class="child-pages-grid">
					<?php foreach ( $children as $child ) : ?>
						<div class="child-page">
							<?php if ( has_post_thumbnail( $child->ID ) ) : ?>
								<a href="<?php echo esc_url( get_permalink( $child->ID ) ); ?>" class="child-page-thumbnail">
									<?php echo get_the_post_thumbnail( $child->ID, 'medium' ); ?>
								</a>
							<?php endif; ?>
							
							<h4 class="child-page-title">
								<a href="<?php echo esc_url( get_permalink( $child->ID ) ); ?>">
									<?php echo esc_html( $child->post_title ); ?>
								</a>
							</h4>
							
							<?php if ( has_excerpt( $child->ID ) ) : ?>
								<div class="child-page-excerpt">
									<?php echo wp_kses_post( wp_trim_words( get_the_excerpt( $child->ID ), 20 ) ); ?>
								</div>
							<?php endif; ?>
							
							<a href="<?php echo esc_url( get_permalink( $child->ID ) ); ?>" class="child-page-link">
								<?php esc_html_e( 'Learn More', 'nosfir' ); ?>
								<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
									<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
								</svg>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if ( $enable_share === 'yes' || ( $enable_share !== 'no' && get_theme_mod( 'nosfir_page_share', false ) ) ) : ?>
			<footer class="page-footer">
				<div class="page-share">
					<h3 class="share-title"><?php esc_html_e( 'Share this page', 'nosfir' ); ?></h3>
					<?php nosfir_social_share(); ?>
				</div>
			</footer>
		<?php endif; ?>
		
		<?php if ( current_user_can( 'edit_pages' ) ) : ?>
			<div class="page-edit">
				<?php
				edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Edit <span class="screen-reader-text">%s</span>', 'nosfir' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					),
					'<span class="edit-link">',
					'</span>'
				);
				?>
			</div>
		<?php endif; ?>
		
	</div><!-- .page-inner -->
	
	<?php
	/**
	 * Hook for additional page content
	 */
	do_action( 'nosfir_after_page_article' );
	?>
	
</article><!-- #post-<?php the_ID(); ?> -->