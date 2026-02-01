<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Nosfir
 * @since 1.0.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found">
				<div class="container">
					<div class="error-404-content">
						
						<header class="page-header">
							<div class="error-404-icon">
								<svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="10"></circle>
									<line x1="12" y1="8" x2="12" y2="12"></line>
									<line x1="12" y1="16" x2="12.01" y2="16"></line>
								</svg>
							</div>
							
							<h1 class="page-title">
								<span class="error-code"><?php esc_html_e( '404', 'nosfir' ); ?></span>
								<span class="error-message"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'nosfir' ); ?></span>
							</h1>
							
							<p class="error-description">
								<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'nosfir' ); ?>
							</p>
						</header><!-- .page-header -->

						<div class="page-content">
							
							<?php
							/**
							 * Search Form Section
							 */
							echo '<section class="error-404-search" aria-label="' . esc_attr__( 'Search', 'nosfir' ) . '">';
								echo '<h2 class="section-title">' . esc_html__( 'Search our site', 'nosfir' ) . '</h2>';
								
								if ( nosfir_is_woocommerce_activated() ) {
									echo '<div class="product-search-form">';
									the_widget( 'WC_Widget_Product_Search', array(
										'title' => '',
									) );
									echo '</div>';
								} else {
									get_search_form();
								}
							echo '</section>';

							/**
							 * Quick Links Section
							 */
							?>
							<section class="error-404-links" aria-label="<?php esc_attr_e( 'Quick Links', 'nosfir' ); ?>">
								<h2 class="section-title"><?php esc_html_e( 'Quick Links', 'nosfir' ); ?></h2>
								<div class="quick-links-grid">
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="quick-link">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
											<polyline points="9 22 9 12 15 12 15 22"></polyline>
										</svg>
										<span><?php esc_html_e( 'Home', 'nosfir' ); ?></span>
									</a>
									
									<?php if ( nosfir_is_woocommerce_activated() ) : ?>
										<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="quick-link">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<circle cx="9" cy="21" r="1"></circle>
												<circle cx="20" cy="21" r="1"></circle>
												<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
											</svg>
											<span><?php esc_html_e( 'Shop', 'nosfir' ); ?></span>
										</a>
										
										<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="quick-link">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
												<circle cx="12" cy="7" r="4"></circle>
											</svg>
											<span><?php esc_html_e( 'My Account', 'nosfir' ); ?></span>
										</a>
									<?php endif; ?>
									
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
								</div>
							</section>

							<?php
							/**
							 * WooCommerce Sections
							 */
							if ( nosfir_is_woocommerce_activated() ) :
								
								// Product Categories
								?>
								<section class="error-404-categories" aria-label="<?php esc_attr_e( 'Product Categories', 'nosfir' ); ?>">
									<h2 class="section-title"><?php esc_html_e( 'Browse Categories', 'nosfir' ); ?></h2>
									<div class="product-categories-list">
										<?php
										the_widget(
											'WC_Widget_Product_Categories',
											array(
												'title'        => '',
												'orderby'      => 'name',
												'dropdown'     => 0,
												'count'        => 1,
												'hierarchical' => 1,
												'show_children_only' => 0,
												'hide_empty'   => 1,
												'max_depth'    => 2,
											),
											array(
												'before_widget' => '<div class="widget widget_product_categories">',
												'after_widget'  => '</div>',
											)
										);
										?>
									</div>
								</section>

								<?php
								// Featured Products
								$featured_products = wc_get_featured_product_ids();
								if ( ! empty( $featured_products ) ) :
									?>
									<section class="error-404-featured" aria-label="<?php esc_attr_e( 'Featured Products', 'nosfir' ); ?>">
										<h2 class="section-title"><?php esc_html_e( 'Featured Products', 'nosfir' ); ?></h2>
										<?php
										echo nosfir_do_shortcode(
											'featured_products',
											array(
												'per_page' => 4,
												'columns'  => 4,
												'orderby'  => 'date',
												'order'    => 'DESC',
											)
										);
										?>
									</section>
									<?php
								endif;

								// Best Selling Products
								?>
								<section class="error-404-popular" aria-label="<?php esc_attr_e( 'Popular Products', 'nosfir' ); ?>">
									<h2 class="section-title"><?php esc_html_e( 'Popular Products', 'nosfir' ); ?></h2>
									<?php
									echo nosfir_do_shortcode(
										'best_selling_products',
										array(
											'per_page' => 4,
											'columns'  => 4,
										)
									);
									?>
								</section>

								<?php
								// On Sale Products
								$on_sale = wc_get_product_ids_on_sale();
								if ( ! empty( $on_sale ) ) :
									?>
									<section class="error-404-sale" aria-label="<?php esc_attr_e( 'On Sale', 'nosfir' ); ?>">
										<h2 class="section-title"><?php esc_html_e( 'On Sale Now', 'nosfir' ); ?></h2>
										<?php
										echo nosfir_do_shortcode(
											'sale_products',
											array(
												'per_page' => 4,
												'columns'  => 4,
												'orderby'  => 'date',
												'order'    => 'DESC',
											)
										);
										?>
									</section>
									<?php
								endif;

							else : // If WooCommerce is not activated
								
								/**
								 * Recent Posts Section
								 */
								$recent_posts = wp_get_recent_posts( array(
									'numberposts' => 6,
									'post_status' => 'publish',
								) );
								
								if ( ! empty( $recent_posts ) ) :
									?>
									<section class="error-404-posts" aria-label="<?php esc_attr_e( 'Recent Posts', 'nosfir' ); ?>">
										<h2 class="section-title"><?php esc_html_e( 'Recent Posts', 'nosfir' ); ?></h2>
										<div class="posts-grid">
											<?php foreach ( $recent_posts as $post ) : ?>
												<article class="post-card">
													<?php if ( has_post_thumbnail( $post['ID'] ) ) : ?>
														<a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>" class="post-thumbnail">
															<?php echo get_the_post_thumbnail( $post['ID'], 'medium' ); ?>
														</a>
													<?php endif; ?>
													
													<div class="post-content">
														<h3 class="post-title">
															<a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
																<?php echo esc_html( $post['post_title'] ); ?>
															</a>
														</h3>
														
														<div class="post-meta">
															<time datetime="<?php echo esc_attr( get_the_date( 'c', $post['ID'] ) ); ?>">
																<?php echo esc_html( get_the_date( '', $post['ID'] ) ); ?>
															</time>
														</div>
														
														<?php if ( has_excerpt( $post['ID'] ) ) : ?>
															<div class="post-excerpt">
																<?php echo wp_kses_post( wp_trim_words( get_the_excerpt( $post['ID'] ), 20 ) ); ?>
															</div>
														<?php endif; ?>
													</div>
												</article>
											<?php endforeach; ?>
										</div>
									</section>
									<?php
								endif;

								/**
								 * Categories Section
								 */
								$categories = get_categories( array(
									'orderby'    => 'count',
									'order'      => 'DESC',
									'number'     => 8,
									'hide_empty' => true,
								) );
								
								if ( ! empty( $categories ) ) :
									?>
									<section class="error-404-blog-categories" aria-label="<?php esc_attr_e( 'Categories', 'nosfir' ); ?>">
										<h2 class="section-title"><?php esc_html_e( 'Browse by Category', 'nosfir' ); ?></h2>
										<div class="categories-cloud">
											<?php foreach ( $categories as $category ) : ?>
												<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
												   class="category-tag">
													<?php echo esc_html( $category->name ); ?>
													<span class="count"><?php echo esc_html( $category->count ); ?></span>
												</a>
											<?php endforeach; ?>
										</div>
									</section>
									<?php
								endif;

								/**
								 * Archives Section
								 */
								?>
								<section class="error-404-archives" aria-label="<?php esc_attr_e( 'Archives', 'nosfir' ); ?>">
									<h2 class="section-title"><?php esc_html_e( 'Monthly Archives', 'nosfir' ); ?></h2>
									<div class="archives-list">
										<ul>
											<?php wp_get_archives( array(
												'type'            => 'monthly',
												'limit'           => 12,
												'show_post_count' => true,
											) ); ?>
										</ul>
									</div>
								</section>
								<?php

							endif; // End WooCommerce check
							?>

							<?php
							/**
							 * Hook for additional 404 content
							 */
							do_action( 'nosfir_404_page_content' );
							?>

						</div><!-- .page-content -->

						<div class="error-404-footer">
							<p class="back-to-home">
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button button-primary">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<polyline points="15 18 9 12 15 6"></polyline>
									</svg>
									<?php esc_html_e( 'Back to Homepage', 'nosfir' ); ?>
								</a>
							</p>
						</div>

					</div><!-- .error-404-content -->
				</div><!-- .container -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();