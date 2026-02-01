<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nosfir
 * @since 1.0.0
 */
?>

<section class="no-results not-found">
	<div class="no-results-wrapper">
		
		<header class="page-header">
			<div class="no-results-icon">
				<?php if ( is_search() ) : ?>
					<!-- Search Icon -->
					<svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="11" cy="11" r="8"></circle>
						<path d="m21 21-4.35-4.35"></path>
						<path d="M11 8v6M8 11h6" opacity="0.5"></path>
					</svg>
				<?php else : ?>
					<!-- Empty State Icon -->
					<svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M9 9h6v6H9z" opacity="0.3"></path>
						<path d="M3 3h18v18H3z"></path>
						<line x1="9" y1="3" x2="9" y2="21" opacity="0.3"></line>
						<line x1="15" y1="3" x2="15" y2="21" opacity="0.3"></line>
						<line x1="3" y1="9" x2="21" y2="9" opacity="0.3"></line>
						<line x1="3" y1="15" x2="21" y2="15" opacity="0.3"></line>
					</svg>
				<?php endif; ?>
			</div>
			
			<h1 class="page-title">
				<?php
				if ( is_home() && current_user_can( 'publish_posts' ) ) :
					esc_html_e( 'No Posts Yet', 'nosfir' );
				elseif ( is_search() ) :
					esc_html_e( 'No Results Found', 'nosfir' );
				elseif ( is_category() ) :
					/* translators: %s: category name */
					printf( esc_html__( 'No posts in %s', 'nosfir' ), '<span>' . single_cat_title( '', false ) . '</span>' );
				elseif ( is_tag() ) :
					/* translators: %s: tag name */
					printf( esc_html__( 'No posts tagged %s', 'nosfir' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				elseif ( is_author() ) :
					/* translators: %s: author name */
					printf( esc_html__( 'No posts by %s', 'nosfir' ), '<span>' . get_the_author() . '</span>' );
				elseif ( is_date() ) :
					esc_html_e( 'No posts found for this date', 'nosfir' );
				elseif ( is_tax() ) :
					$term = get_queried_object();
					/* translators: %s: taxonomy term */
					printf( esc_html__( 'No posts in %s', 'nosfir' ), '<span>' . esc_html( $term->name ) . '</span>' );
				else :
					esc_html_e( 'Nothing Found', 'nosfir' );
				endif;
				?>
			</h1>
		</header><!-- .page-header -->

		<div class="page-content">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				
				<p class="no-results-message">
					<?php esc_html_e( 'Ready to share your thoughts with the world? Let\'s create your first post!', 'nosfir' ); ?>
				</p>
				
				<div class="no-results-actions">
					<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="button button-primary">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
							<line x1="10" y1="5" x2="10" y2="15"></line>
							<line x1="5" y1="10" x2="15" y2="10"></line>
						</svg>
						<?php esc_html_e( 'Create Your First Post', 'nosfir' ); ?>
					</a>
				</div>
				
				<div class="getting-started-tips">
					<h3><?php esc_html_e( 'Getting Started Tips:', 'nosfir' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Choose a compelling title for your post', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Add a featured image to make it stand out', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Use categories and tags to organize your content', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Preview before publishing to ensure everything looks great', 'nosfir' ); ?></li>
					</ul>
				</div>

			<?php elseif ( is_search() ) : ?>
				
				<p class="no-results-message">
					<?php
					printf(
						/* translators: %s: search query */
						esc_html__( 'Sorry, but nothing matched your search for "%s". Please try again with different keywords.', 'nosfir' ),
						'<mark>' . get_search_query() . '</mark>'
					);
					?>
				</p>
				
				<div class="search-form-wrapper">
					<?php get_search_form(); ?>
				</div>
				
				<div class="search-suggestions">
					<h3><?php esc_html_e( 'Search Suggestions:', 'nosfir' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Check your spelling', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Try more general keywords', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Use different keywords', 'nosfir' ); ?></li>
						<li><?php esc_html_e( 'Try searching for related terms', 'nosfir' ); ?></li>
					</ul>
				</div>
				
				<?php
				// Popular search terms (if available)
				$popular_searches = get_theme_mod( 'nosfir_popular_searches' );
				if ( $popular_searches ) :
					$searches = explode( ',', $popular_searches );
					?>
					<div class="popular-searches">
						<h3><?php esc_html_e( 'Popular Searches:', 'nosfir' ); ?></h3>
						<div class="search-tags">
							<?php foreach ( $searches as $term ) : ?>
								<a href="<?php echo esc_url( home_url( '/?s=' . trim( $term ) ) ); ?>" class="search-tag">
									<?php echo esc_html( trim( $term ) ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

			<?php elseif ( is_category() || is_tag() || is_tax() ) : ?>
				
				<p class="no-results-message">
					<?php esc_html_e( 'There are currently no posts in this category. Check back soon or explore other sections of our site.', 'nosfir' ); ?>
				</p>
				
				<?php
				// Show other categories/tags with posts
				if ( is_category() ) {
					$terms = get_categories( array(
						'orderby'    => 'count',
						'order'      => 'DESC',
						'number'     => 8,
						'hide_empty' => true,
						'exclude'    => get_queried_object_id(),
					) );
					$term_type = __( 'Other Categories', 'nosfir' );
				} elseif ( is_tag() ) {
					$terms = get_tags( array(
						'orderby'    => 'count',
						'order'      => 'DESC',
						'number'     => 12,
						'hide_empty' => true,
						'exclude'    => get_queried_object_id(),
					) );
					$term_type = __( 'Other Tags', 'nosfir' );
				} else {
					$terms = array();
					$term_type = '';
				}
				
				if ( ! empty( $terms ) ) : ?>
					<div class="alternative-terms">
						<h3><?php echo esc_html( $term_type ); ?></h3>
						<div class="terms-cloud">
							<?php foreach ( $terms as $term ) : ?>
								<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="term-link">
									<?php echo esc_html( $term->name ); ?>
									<span class="count">(<?php echo esc_html( $term->count ); ?>)</span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
				
				<div class="search-form-wrapper">
					<h3><?php esc_html_e( 'Try searching:', 'nosfir' ); ?></h3>
					<?php get_search_form(); ?>
				</div>

			<?php elseif ( is_author() ) : ?>
				
				<p class="no-results-message">
					<?php esc_html_e( 'This author hasn\'t published any posts yet. Check back later for new content.', 'nosfir' ); ?>
				</p>
				
				<?php
				// Show other authors with posts
				$authors = get_users( array(
					'orderby'      => 'post_count',
					'order'        => 'DESC',
					'who'          => 'authors',
					'has_published_posts' => true,
					'number'       => 6,
					'exclude'      => get_queried_object_id(),
				) );
				
				if ( ! empty( $authors ) ) : ?>
					<div class="other-authors">
						<h3><?php esc_html_e( 'Other Authors', 'nosfir' ); ?></h3>
						<div class="authors-grid">
							<?php foreach ( $authors as $author ) :
								$post_count = count_user_posts( $author->ID );
								?>
								<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" class="author-card">
									<?php echo get_avatar( $author->ID, 60 ); ?>
									<div class="author-info">
										<h4><?php echo esc_html( $author->display_name ); ?></h4>
										<span class="post-count">
											<?php
											printf(
												/* translators: %s: number of posts */
												esc_html( _n( '%s post', '%s posts', $post_count, 'nosfir' ) ),
												number_format_i18n( $post_count )
											);
											?>
										</span>
									</div>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

			<?php else : ?>
				
				<p class="no-results-message">
					<?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'nosfir' ); ?>
				</p>
				
				<div class="search-form-wrapper">
					<?php get_search_form(); ?>
				</div>
				
				<?php
				// Recent posts
				$recent_posts = wp_get_recent_posts( array(
					'numberposts' => 6,
					'post_status' => 'publish',
				) );
				
				if ( ! empty( $recent_posts ) ) : ?>
					<div class="recent-content">
						<h3><?php esc_html_e( 'Recent Posts', 'nosfir' ); ?></h3>
						<div class="posts-grid">
							<?php foreach ( $recent_posts as $post ) : ?>
								<article class="post-card">
									<?php if ( has_post_thumbnail( $post['ID'] ) ) : ?>
										<a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>" class="post-thumbnail">
											<?php echo get_the_post_thumbnail( $post['ID'], 'medium' ); ?>
										</a>
									<?php endif; ?>
									
									<div class="post-content">
										<h4 class="post-title">
											<a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
												<?php echo esc_html( $post['post_title'] ); ?>
											</a>
										</h4>
										
										<div class="post-meta">
											<time datetime="<?php echo esc_attr( get_the_date( 'c', $post['ID'] ) ); ?>">
												<?php echo esc_html( get_the_date( '', $post['ID'] ) ); ?>
											</time>
										</div>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

			<?php endif; ?>
			
			<?php
			// Common quick links for all scenarios
			?>
			<div class="quick-links">
				<h3><?php esc_html_e( 'Quick Links', 'nosfir' ); ?></h3>
				<div class="links-grid">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="quick-link">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
							<polyline points="9 22 9 12 15 12 15 22"></polyline>
						</svg>
						<span><?php esc_html_e( 'Homepage', 'nosfir' ); ?></span>
					</a>
					
					<?php if ( get_option( 'page_for_posts' ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="quick-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
								<polyline points="14 2 14 8 20 8"></polyline>
								<line x1="16" y1="13" x2="8" y2="13"></line>
								<line x1="16" y1="17" x2="8" y2="17"></line>
								<polyline points="10 9 9 9 8 9"></polyline>
							</svg>
							<span><?php esc_html_e( 'Blog', 'nosfir' ); ?></span>
						</a>
					<?php endif; ?>
					
					<?php if ( nosfir_is_woocommerce_activated() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="quick-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<circle cx="9" cy="21" r="1"></circle>
								<circle cx="20" cy="21" r="1"></circle>
								<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
							</svg>
							<span><?php esc_html_e( 'Shop', 'nosfir' ); ?></span>
						</a>
					<?php endif; ?>
					
					<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="quick-link">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
						</svg>
						<span><?php esc_html_e( 'Contact', 'nosfir' ); ?></span>
					</a>
				</div>
			</div>
			
			<?php
			/**
			 * Hook for additional no results content
			 */
			do_action( 'nosfir_no_results_content' );
			?>
			
		</div><!-- .page-content -->
		
	</div><!-- .no-results-wrapper -->
</section><!-- .no-results -->