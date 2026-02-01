<?php
/**
 * The template used for displaying page content in template-homepage.php
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Get homepage settings
$hero_enabled = get_theme_mod( 'nosfir_homepage_hero_enable', true );
$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$parallax_enabled = get_theme_mod( 'nosfir_homepage_parallax', false );
$overlay_enabled = get_theme_mod( 'nosfir_homepage_overlay', true );
$overlay_opacity = get_theme_mod( 'nosfir_homepage_overlay_opacity', '0.5' );

// Page meta options
$hide_title = get_post_meta( get_the_ID(), '_nosfir_hide_title', true );
$custom_subtitle = get_post_meta( get_the_ID(), '_nosfir_page_subtitle', true );
$hero_content = get_post_meta( get_the_ID(), '_nosfir_hero_content', true );
$cta_button_text = get_post_meta( get_the_ID(), '_nosfir_cta_button_text', true );
$cta_button_url = get_post_meta( get_the_ID(), '_nosfir_cta_button_url', true );
$secondary_cta_text = get_post_meta( get_the_ID(), '_nosfir_secondary_cta_text', true );
$secondary_cta_url = get_post_meta( get_the_ID(), '_nosfir_secondary_cta_url', true );

// Build data attributes for JavaScript
$data_attrs = array();
if ( $parallax_enabled && $featured_image ) {
	$data_attrs[] = 'data-parallax="true"';
	$data_attrs[] = 'data-parallax-speed="0.5"';
}
if ( $featured_image ) {
	$data_attrs[] = 'data-featured-image="' . esc_url( $featured_image ) . '"';
}

// Build inline styles
$inline_styles = array();
if ( $featured_image && $hero_enabled ) {
	$inline_styles[] = 'background-image: url(' . esc_url( $featured_image ) . ')';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'homepage-content' ); ?> <?php echo implode( ' ', $data_attrs ); ?>>
	
	<?php if ( $hero_enabled && ( $featured_image || $hero_content || ! $hide_title ) ) : ?>
		<!-- Hero Section -->
		<section class="homepage-hero<?php echo $parallax_enabled ? ' has-parallax' : ''; ?>" style="<?php echo esc_attr( implode( '; ', $inline_styles ) ); ?>">
			
			<?php if ( $overlay_enabled && $featured_image ) : ?>
				<div class="hero-overlay" style="opacity: <?php echo esc_attr( $overlay_opacity ); ?>"></div>
			<?php endif; ?>
			
			<div class="hero-content">
				<div class="container">
					<div class="hero-inner">
						
						<?php if ( ! $hide_title ) : ?>
							<h1 class="hero-title animate-fade-up">
								<?php the_title(); ?>
							</h1>
						<?php endif; ?>
						
						<?php if ( $custom_subtitle ) : ?>
							<p class="hero-subtitle animate-fade-up animate-delay-1">
								<?php echo wp_kses_post( $custom_subtitle ); ?>
							</p>
						<?php endif; ?>
						
						<?php if ( $hero_content ) : ?>
							<div class="hero-description animate-fade-up animate-delay-2">
								<?php echo wp_kses_post( $hero_content ); ?>
							</div>
						<?php endif; ?>
						
						<?php if ( $cta_button_text && $cta_button_url ) : ?>
							<div class="hero-buttons animate-fade-up animate-delay-3">
								<a href="<?php echo esc_url( $cta_button_url ); ?>" class="button button-primary button-large">
									<?php echo esc_html( $cta_button_text ); ?>
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
										<path d="M7 10h6m0 0l-3-3m3 3l-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</a>
								
								<?php if ( $secondary_cta_text && $secondary_cta_url ) : ?>
									<a href="<?php echo esc_url( $secondary_cta_url ); ?>" class="button button-outline button-large">
										<?php echo esc_html( $secondary_cta_text ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						
						<?php if ( get_theme_mod( 'nosfir_homepage_hero_scroll_indicator', true ) ) : ?>
							<div class="scroll-indicator animate-bounce">
								<svg width="30" height="30" viewBox="0 0 20 20" fill="none" stroke="currentColor">
									<path d="M10 5v10m0 0l-3-3m3 3l3-3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<span class="screen-reader-text"><?php esc_html_e( 'Scroll down', 'nosfir' ); ?></span>
							</div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	
	<!-- Main Content -->
	<div class="homepage-main-content">
		<div class="container">
			<?php
			/**
			 * Functions hooked into nosfir_homepage action
			 *
			 * @hooked nosfir_homepage_content - 10
			 * @hooked nosfir_homepage_sections - 20
			 */
			do_action( 'nosfir_homepage' );
			?>
			
			<?php if ( get_the_content() ) : ?>
				<div class="homepage-page-content">
					<div class="entry-content">
						<?php
						the_content();
						
						wp_link_pages(
							array(
								'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
								'after'       => '</div>',
								'link_before' => '<span class="page-number">',
								'link_after'  => '</span>',
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	
	<?php
	// Homepage Sections
	$sections = array(
		'features'     => get_theme_mod( 'nosfir_homepage_features_enable', true ),
		'about'        => get_theme_mod( 'nosfir_homepage_about_enable', true ),
		'services'     => get_theme_mod( 'nosfir_homepage_services_enable', true ),
		'portfolio'    => get_theme_mod( 'nosfir_homepage_portfolio_enable', false ),
		'testimonials' => get_theme_mod( 'nosfir_homepage_testimonials_enable', true ),
		'team'         => get_theme_mod( 'nosfir_homepage_team_enable', false ),
		'blog'         => get_theme_mod( 'nosfir_homepage_blog_enable', true ),
		'cta'          => get_theme_mod( 'nosfir_homepage_cta_enable', true ),
	);
	
	foreach ( $sections as $section => $enabled ) {
		if ( $enabled ) {
			get_template_part( 'template-parts/homepage/section', $section );
		}
	}
	?>
	
	<?php if ( nosfir_is_woocommerce_activated() ) : ?>
		<!-- WooCommerce Sections -->
		<div class="homepage-woocommerce-sections">
			<?php
			// Featured Products
			if ( get_theme_mod( 'nosfir_homepage_featured_products_enable', true ) ) : ?>
				<section class="homepage-featured-products">
					<div class="container">
						<div class="section-header">
							<h2 class="section-title">
								<?php echo esc_html( get_theme_mod( 'nosfir_homepage_featured_products_title', __( 'Featured Products', 'nosfir' ) ) ); ?>
							</h2>
							<?php
							$featured_desc = get_theme_mod( 'nosfir_homepage_featured_products_desc' );
							if ( $featured_desc ) : ?>
								<p class="section-description"><?php echo wp_kses_post( $featured_desc ); ?></p>
							<?php endif; ?>
						</div>
						<?php
						echo do_shortcode( '[featured_products per_page="' . get_theme_mod( 'nosfir_homepage_featured_products_count', 4 ) . '" columns="4"]' );
						?>
					</div>
				</section>
			<?php endif; ?>
			
			<?php
			// Product Categories
			if ( get_theme_mod( 'nosfir_homepage_product_categories_enable', true ) ) : ?>
				<section class="homepage-product-categories">
					<div class="container">
						<div class="section-header">
							<h2 class="section-title">
								<?php echo esc_html( get_theme_mod( 'nosfir_homepage_product_categories_title', __( 'Shop by Category', 'nosfir' ) ) ); ?>
							</h2>
							<?php
							$categories_desc = get_theme_mod( 'nosfir_homepage_product_categories_desc' );
							if ( $categories_desc ) : ?>
								<p class="section-description"><?php echo wp_kses_post( $categories_desc ); ?></p>
							<?php endif; ?>
						</div>
						<?php
						echo do_shortcode( '[product_categories number="' . get_theme_mod( 'nosfir_homepage_product_categories_count', 6 ) . '" parent="0" columns="3"]' );
						?>
					</div>
				</section>
			<?php endif; ?>
			
			<?php
			// Recent Products
			if ( get_theme_mod( 'nosfir_homepage_recent_products_enable', true ) ) : ?>
				<section class="homepage-recent-products">
					<div class="container">
						<div class="section-header">
							<h2 class="section-title">
								<?php echo esc_html( get_theme_mod( 'nosfir_homepage_recent_products_title', __( 'New Arrivals', 'nosfir' ) ) ); ?>
							</h2>
							<?php
							$recent_desc = get_theme_mod( 'nosfir_homepage_recent_products_desc' );
							if ( $recent_desc ) : ?>
								<p class="section-description"><?php echo wp_kses_post( $recent_desc ); ?></p>
							<?php endif; ?>
						</div>
						<?php
						echo do_shortcode( '[recent_products per_page="' . get_theme_mod( 'nosfir_homepage_recent_products_count', 8 ) . '" columns="4"]' );
						?>
					</div>
				</section>
			<?php endif; ?>
			
			<?php
			// Sale Products
			if ( get_theme_mod( 'nosfir_homepage_sale_products_enable', false ) ) :
				$sale_products = wc_get_product_ids_on_sale();
				if ( ! empty( $sale_products ) ) : ?>
					<section class="homepage-sale-products">
						<div class="container">
							<div class="section-header">
								<h2 class="section-title">
									<?php echo esc_html( get_theme_mod( 'nosfir_homepage_sale_products_title', __( 'Special Offers', 'nosfir' ) ) ); ?>
								</h2>
								<?php
								$sale_desc = get_theme_mod( 'nosfir_homepage_sale_products_desc' );
								if ( $sale_desc ) : ?>
									<p class="section-description"><?php echo wp_kses_post( $sale_desc ); ?></p>
								<?php endif; ?>
							</div>
							<?php
							echo do_shortcode( '[sale_products per_page="' . get_theme_mod( 'nosfir_homepage_sale_products_count', 4 ) . '" columns="4"]' );
							?>
						</div>
					</section>
				<?php endif;
			endif; ?>
			
			<?php
			// Best Selling Products
			if ( get_theme_mod( 'nosfir_homepage_best_sellers_enable', false ) ) : ?>
				<section class="homepage-best-sellers">
					<div class="container">
						<div class="section-header">
							<h2 class="section-title">
								<?php echo esc_html( get_theme_mod( 'nosfir_homepage_best_sellers_title', __( 'Best Sellers', 'nosfir' ) ) ); ?>
							</h2>
							<?php
							$best_desc = get_theme_mod( 'nosfir_homepage_best_sellers_desc' );
							if ( $best_desc ) : ?>
								<p class="section-description"><?php echo wp_kses_post( $best_desc ); ?></p>
							<?php endif; ?>
						</div>
						<?php
						echo do_shortcode( '[best_selling_products per_page="' . get_theme_mod( 'nosfir_homepage_best_sellers_count', 4 ) . '" columns="4"]' );
						?>
					</div>
				</section>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php
	// Custom sections via hook
	do_action( 'nosfir_homepage_after_sections' );
	?>
	
	<?php if ( current_user_can( 'edit_pages' ) ) : ?>
		<div class="edit-link-wrapper">
			<?php
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post */
					wp_kses(
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
	
</article><!-- #post-<?php the_ID(); ?> -->