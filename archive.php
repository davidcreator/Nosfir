<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nosfir
 * @since 1.0.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<div class="container">
						<div class="page-header-content">
							<?php
							/**
							 * Archive title and description
							 */
							the_archive_title( '<h1 class="page-title">', '</h1>' );
							
							// Archive description
							the_archive_description( '<div class="archive-description">', '</div>' );
							
							// Additional archive information
							if ( is_category() || is_tag() || is_tax() ) {
								$term = get_queried_object();
								
								// Display term image if available (requires custom field or plugin)
								if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
									$term_image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
									
									if ( $term_image_id ) {
										echo '<div class="archive-thumbnail">';
										echo wp_get_attachment_image( $term_image_id, 'large' );
										echo '</div>';
									}
									
									// Display post count
									if ( isset( $term->count ) ) {
										echo '<div class="archive-meta">';
										printf(
											/* translators: %s: number of posts */
											esc_html( _n( '%s post', '%s posts', $term->count, 'nosfir' ) ),
											'<span class="count">' . number_format_i18n( $term->count ) . '</span>'
										);
										echo '</div>';
									}
								}
							}
							
							// Author archive specific information
							if ( is_author() ) {
								$author_id = get_the_author_meta( 'ID' );
								?>
								<div class="author-info">
									<div class="author-avatar">
										<?php echo get_avatar( $author_id, 120 ); ?>
									</div>
									
									<div class="author-details">
										<?php
										$author_description = get_the_author_meta( 'description' );
										if ( $author_description ) {
											echo '<div class="author-bio">' . wp_kses_post( $author_description ) . '</div>';
										}
										
										// Author social links
										$social_links = nosfir_get_author_social( $author_id );
										if ( ! empty( $social_links ) ) {
											echo '<div class="author-social">';
											foreach ( $social_links as $platform => $url ) {
												printf(
													'<a href="%1$s" class="social-%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s">
														<span class="screen-reader-text">%3$s</span>
													</a>',
													esc_url( $url ),
													esc_attr( $platform ),
													esc_attr( ucfirst( $platform ) )
												);
											}
											echo '</div>';
										}
										
										// Author stats
										$author_posts = count_user_posts( $author_id );
										$author_comments = get_comments( array(
											'author__in' => array( $author_id ),
											'count' => true,
										) );
										
										echo '<div class="author-stats">';
										echo '<span class="stat-posts">';
										printf(
											/* translators: %s: number of posts */
											esc_html( _n( '%s Post', '%s Posts', $author_posts, 'nosfir' ) ),
											'<strong>' . number_format_i18n( $author_posts ) . '</strong>'
										);
										echo '</span>';
										
										if ( $author_comments ) {
											echo '<span class="stat-comments">';
											printf(
												/* translators: %s: number of comments */
												esc_html( _n( '%s Comment', '%s Comments', $author_comments, 'nosfir' ) ),
												'<strong>' . number_format_i18n( $author_comments ) . '</strong>'
											);
											echo '</span>';
										}
										echo '</div>';
										?>
									</div>
								</div>
								<?php
							}
							
							// Date archive specific information
							if ( is_date() ) {
								echo '<div class="date-archive-meta">';
								
								if ( is_day() ) {
									/* translators: %s: date */
									printf( esc_html__( 'Daily Archives: %s', 'nosfir' ), '<span>' . get_the_date() . '</span>' );
								} elseif ( is_month() ) {
									/* translators: %s: date */
									printf( esc_html__( 'Monthly Archives: %s', 'nosfir' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );
								} elseif ( is_year() ) {
									/* translators: %s: date */
									printf( esc_html__( 'Yearly Archives: %s', 'nosfir' ), '<span>' . get_the_date( 'Y' ) . '</span>' );
								}
								
								echo '</div>';
							}
							?>
							
							<?php
							/**
							 * Archive filters and sorting
							 */
							if ( get_theme_mod( 'nosfir_archive_filters', true ) ) : ?>
								<div class="archive-filters">
									<div class="filter-group">
										<label for="archive-orderby" class="screen-reader-text">
											<?php esc_html_e( 'Order by', 'nosfir' ); ?>
										</label>
										<select id="archive-orderby" class="archive-orderby">
											<option value="date-desc"><?php esc_html_e( 'Latest', 'nosfir' ); ?></option>
											<option value="date-asc"><?php esc_html_e( 'Oldest', 'nosfir' ); ?></option>
											<option value="title-asc"><?php esc_html_e( 'Title (A-Z)', 'nosfir' ); ?></option>
											<option value="title-desc"><?php esc_html_e( 'Title (Z-A)', 'nosfir' ); ?></option>
											<option value="comment_count"><?php esc_html_e( 'Most Commented', 'nosfir' ); ?></option>
										</select>
									</div>
									
									<div class="view-switcher">
										<button class="view-grid active" aria-label="<?php esc_attr_e( 'Grid view', 'nosfir' ); ?>" data-view="grid">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
												<rect x="2" y="2" width="7" height="7"/>
												<rect x="11" y="2" width="7" height="7"/>
												<rect x="2" y="11" width="7" height="7"/>
												<rect x="11" y="11" width="7" height="7"/>
											</svg>
										</button>
										<button class="view-list" aria-label="<?php esc_attr_e( 'List view', 'nosfir' ); ?>" data-view="list">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
												<rect x="2" y="3" width="16" height="2"/>
												<rect x="2" y="9" width="16" height="2"/>
												<rect x="2" y="15" width="16" height="2"/>
											</svg>
										</button>
									</div>
								</div>
							<?php endif; ?>
							
						</div><!-- .page-header-content -->
					</div><!-- .container -->
				</header><!-- .page-header -->

				<div class="archive-content">
					<div class="container">
						<div class="content-layout <?php echo esc_attr( nosfir_get_layout_class() ); ?>">
							
							<div class="posts-container">
								<?php
								/**
								 * Hook before archive loop
								 */
								do_action( 'nosfir_before_archive_loop' );
								
								// Get the loop template
								$archive_layout = get_theme_mod( 'nosfir_archive_layout', 'grid' );
								
								if ( 'grid' === $archive_layout || 'masonry' === $archive_layout ) {
									echo '<div class="posts-' . esc_attr( $archive_layout ) . '" data-layout="' . esc_attr( $archive_layout ) . '">';
								} else {
									echo '<div class="posts-list">';
								}
								
								/* Start the Loop */
								while ( have_posts() ) :
									the_post();

									/*
									 * Include the Post-Format-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
									 */
									get_template_part( 'template-parts/content', get_post_format() );

								endwhile;
								
								echo '</div><!-- .posts-grid/list -->';
								
								/**
								 * Hook after archive loop
								 */
								do_action( 'nosfir_after_archive_loop' );
								
								// Pagination
								nosfir_pagination( array(
									'prev_text' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg><span class="screen-reader-text">' . __( 'Previous', 'nosfir' ) . '</span>',
									'next_text' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg><span class="screen-reader-text">' . __( 'Next', 'nosfir' ) . '</span>',
								) );
								?>
							</div><!-- .posts-container -->
							
							<?php if ( 'none' !== nosfir_get_sidebar_position() ) : ?>
								<aside class="sidebar-container">
									<?php get_sidebar(); ?>
								</aside>
							<?php endif; ?>
							
						</div><!-- .content-layout -->
					</div><!-- .container -->
				</div><!-- .archive-content -->

			<?php else : ?>

				<div class="no-results">
					<div class="container">
						<?php get_template_part( 'template-parts/content', 'none' ); ?>
					</div>
				</div>

			<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php
	/**
	 * Related categories or tags
	 */
	if ( ( is_category() || is_tag() ) && get_theme_mod( 'nosfir_show_related_terms', true ) ) :
		$current_term = get_queried_object();
		
		if ( is_category() ) {
			$terms = get_categories( array(
				'parent'     => $current_term->parent,
				'exclude'    => $current_term->term_id,
				'number'     => 6,
				'hide_empty' => true,
			) );
			$term_type = __( 'Related Categories', 'nosfir' );
		} else {
			$terms = get_tags( array(
				'exclude'    => $current_term->term_id,
				'number'     => 8,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'hide_empty' => true,
			) );
			$term_type = __( 'Related Tags', 'nosfir' );
		}
		
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<section class="related-terms">
				<div class="container">
					<h2 class="section-title"><?php echo esc_html( $term_type ); ?></h2>
					<div class="terms-list">
						<?php foreach ( $terms as $term ) : ?>
							<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="term-item">
								<?php
								$term_image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
								if ( $term_image_id ) {
									echo wp_get_attachment_image( $term_image_id, 'thumbnail' );
								}
								?>
								<span class="term-name"><?php echo esc_html( $term->name ); ?></span>
								<span class="term-count">(<?php echo esc_html( $term->count ); ?>)</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif;
	endif;
	?>

<?php
get_footer();