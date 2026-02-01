<?php
/**
 * Nosfir hooks
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * General
 *
 * @see nosfir_header_widget_region()
 * @see nosfir_get_sidebar()
 * @see nosfir_breadcrumb()
 */
add_action( 'nosfir_before_content', 'nosfir_header_widget_region', 10 );
add_action( 'nosfir_before_content', 'nosfir_breadcrumb', 20 );
add_action( 'nosfir_sidebar', 'nosfir_get_sidebar', 10 );

/**
 * Header Top Bar
 *
 * @see nosfir_top_bar_left()
 * @see nosfir_top_bar_right()
 */
add_action( 'nosfir_top_bar', 'nosfir_top_bar_container', 0 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_left', 10 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_right', 20 );
add_action( 'nosfir_top_bar', 'nosfir_top_bar_container_close', 100 );

/**
 * Header
 *
 * @see nosfir_skip_links()
 * @see nosfir_site_branding()
 * @see nosfir_primary_navigation()
 * @see nosfir_secondary_navigation()
 * @see nosfir_header_search()
 * @see nosfir_header_account()
 * @see nosfir_header_cart()
 */
add_action( 'nosfir_header', 'nosfir_header_container', 0 );
add_action( 'nosfir_header', 'nosfir_skip_links', 5 );
add_action( 'nosfir_header', 'nosfir_site_branding', 20 );
add_action( 'nosfir_header', 'nosfir_primary_navigation', 30 );
add_action( 'nosfir_header', 'nosfir_header_search', 40 );
add_action( 'nosfir_header', 'nosfir_header_account', 50 );

// WooCommerce cart - conditional
if ( nosfir_is_woocommerce_activated() ) {
	add_action( 'nosfir_header', 'nosfir_header_cart', 60 );
}

add_action( 'nosfir_header', 'nosfir_header_container_close', 100 );

/**
 * Header After
 *
 * @see nosfir_secondary_navigation()
 * @see nosfir_header_widget_region()
 */
add_action( 'nosfir_after_header', 'nosfir_secondary_navigation', 10 );
add_action( 'nosfir_after_header', 'nosfir_mobile_navigation', 20 );

/**
 * Hero Section
 *
 * @see nosfir_hero_section()
 */
add_action( 'nosfir_before_content', 'nosfir_hero_section', 5 );

/**
 * Footer
 *
 * @see nosfir_footer_widgets()
 * @see nosfir_footer_navigation()
 * @see nosfir_footer_social()
 * @see nosfir_credit()
 */
add_action( 'nosfir_before_footer', 'nosfir_footer_cta', 10 );

add_action( 'nosfir_footer', 'nosfir_footer_container', 0 );
add_action( 'nosfir_footer', 'nosfir_footer_widgets', 10 );
add_action( 'nosfir_footer', 'nosfir_footer_navigation', 20 );
add_action( 'nosfir_footer', 'nosfir_footer_social', 30 );
add_action( 'nosfir_footer', 'nosfir_footer_container_close', 40 );

add_action( 'nosfir_after_footer', 'nosfir_footer_bottom_container', 0 );
add_action( 'nosfir_after_footer', 'nosfir_credit', 10 );
add_action( 'nosfir_after_footer', 'nosfir_footer_bottom_container_close', 100 );

/**
 * Homepage
 *
 * @see nosfir_homepage_content()
 * @see nosfir_homepage_sections()
 */
add_action( 'nosfir_homepage', 'nosfir_homepage_content', 10 );
add_action( 'nosfir_homepage', 'nosfir_homepage_sections', 20 );

// Homepage sections
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_hero', 10 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_featured', 20 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_services', 30 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_about', 40 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_portfolio', 50 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_testimonials', 60 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_blog', 70 );
add_action( 'nosfir_homepage_sections', 'nosfir_homepage_cta', 80 );

/**
 * Posts
 *
 * @see nosfir_post_header()
 * @see nosfir_post_meta()
 * @see nosfir_post_content()
 * @see nosfir_post_footer()
 * @see nosfir_paging_nav()
 * @see nosfir_post_navigation()
 * @see nosfir_display_comments()
 */
// Archive/Blog
add_action( 'nosfir_loop_post', 'nosfir_post_thumbnail', 10 );
add_action( 'nosfir_loop_post', 'nosfir_post_header', 20 );
add_action( 'nosfir_loop_post', 'nosfir_post_content', 30 );
add_action( 'nosfir_loop_post', 'nosfir_post_footer_meta', 40 );

add_action( 'nosfir_loop_after', 'nosfir_paging_nav', 10 );

// Single Post
add_action( 'nosfir_single_post_top', 'nosfir_post_thumbnail', 10 );

add_action( 'nosfir_single_post', 'nosfir_post_header', 10 );
add_action( 'nosfir_single_post', 'nosfir_post_content', 20 );
add_action( 'nosfir_single_post', 'nosfir_post_footer', 30 );

add_action( 'nosfir_single_post_bottom', 'nosfir_post_tags', 10 );
add_action( 'nosfir_single_post_bottom', 'nosfir_post_share', 20 );
add_action( 'nosfir_single_post_bottom', 'nosfir_author_box', 30 );
add_action( 'nosfir_single_post_bottom', 'nosfir_related_posts', 40 );
add_action( 'nosfir_single_post_bottom', 'nosfir_post_navigation', 50 );
add_action( 'nosfir_single_post_bottom', 'nosfir_display_comments', 60 );

/**
 * Pages
 *
 * @see nosfir_page_header()
 * @see nosfir_page_content()
 * @see nosfir_display_comments()
 */
add_action( 'nosfir_page', 'nosfir_page_header', 10 );
add_action( 'nosfir_page', 'nosfir_page_content', 20 );
add_action( 'nosfir_page_after', 'nosfir_display_comments', 10 );

/**
 * Search Results
 *
 * @see nosfir_search_header()
 * @see nosfir_search_form()
 */
add_action( 'nosfir_search_before', 'nosfir_search_header', 10 );
add_action( 'nosfir_search_before', 'nosfir_search_form', 20 );

/**
 * 404 Page
 *
 * @see nosfir_404_header()
 * @see nosfir_404_content()
 * @see nosfir_404_search_form()
 */
add_action( 'nosfir_404', 'nosfir_404_header', 10 );
add_action( 'nosfir_404', 'nosfir_404_content', 20 );
add_action( 'nosfir_404', 'nosfir_404_search_form', 30 );
add_action( 'nosfir_404', 'nosfir_404_recent_posts', 40 );

/**
 * Extras that display outside of main templates
 */
add_action( 'wp_body_open', 'nosfir_body_open', 10 );
add_action( 'nosfir_before_site', 'nosfir_preloader', 10 );
add_action( 'nosfir_after_site', 'nosfir_scroll_to_top', 10 );

/**
 * Mobile specific hooks
 */
if ( wp_is_mobile() ) {
	add_action( 'nosfir_after_site', 'nosfir_mobile_menu_overlay', 20 );
}

/**
 * Helper functions for template hooks
 */

if ( ! function_exists( 'nosfir_header_container' ) ) {
	/**
	 * The header container
	 */
	function nosfir_header_container() {
		echo '<div class="container">';
	}
}

if ( ! function_exists( 'nosfir_header_container_close' ) ) {
	/**
	 * The header container close
	 */
	function nosfir_header_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_footer_container' ) ) {
	/**
	 * The footer container
	 */
	function nosfir_footer_container() {
		echo '<div class="footer-main"><div class="container">';
	}
}

if ( ! function_exists( 'nosfir_footer_container_close' ) ) {
	/**
	 * The footer container close
	 */
	function nosfir_footer_container_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'nosfir_footer_bottom_container' ) ) {
	/**
	 * The footer bottom container
	 */
	function nosfir_footer_bottom_container() {
		echo '<div class="footer-bottom"><div class="container">';
	}
}

if ( ! function_exists( 'nosfir_footer_bottom_container_close' ) ) {
	/**
	 * The footer bottom container close
	 */
	function nosfir_footer_bottom_container_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'nosfir_top_bar_container' ) ) {
	/**
	 * The top bar container
	 */
	function nosfir_top_bar_container() {
		echo '<div class="top-bar"><div class="container">';
	}
}

if ( ! function_exists( 'nosfir_top_bar_container_close' ) ) {
	/**
	 * The top bar container close
	 */
	function nosfir_top_bar_container_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'nosfir_top_bar_left' ) ) {
	/**
	 * Top bar left content
	 */
	function nosfir_top_bar_left() {
		?>
		<div class="top-bar-left">
			<?php
			// Contact info
			$phone = get_theme_mod( 'nosfir_header_phone' );
			$email = get_theme_mod( 'nosfir_header_email' );
			
			if ( $phone ) {
				echo '<span class="top-bar-phone"><i class="icon-phone"></i> ' . esc_html( $phone ) . '</span>';
			}
			
			if ( $email ) {
				echo '<span class="top-bar-email"><i class="icon-email"></i> ' . esc_html( $email ) . '</span>';
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_top_bar_right' ) ) {
	/**
	 * Top bar right content
	 */
	function nosfir_top_bar_right() {
		?>
		<div class="top-bar-right">
			<?php
			// Social links
			if ( has_nav_menu( 'social' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'social',
					'menu_class'     => 'social-menu',
					'container'      => false,
					'depth'          => 1,
					'link_before'    => '<span class="screen-reader-text">',
					'link_after'     => '</span>',
				) );
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_header_search' ) ) {
	/**
	 * Header search
	 */
	function nosfir_header_search() {
		if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
			return;
		}
		?>
		<div class="header-search">
			<button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'nosfir' ); ?>">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
					<circle cx="9" cy="9" r="7"/>
					<path d="M14 14l3.5 3.5"/>
				</svg>
			</button>
			<div class="search-dropdown">
				<?php nosfir_search_form( 'header-search' ); ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_header_account' ) ) {
	/**
	 * Header account
	 */
	function nosfir_header_account() {
		if ( ! get_theme_mod( 'nosfir_header_account', true ) ) {
			return;
		}
		
		$account_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		if ( ! $account_url ) {
			$account_url = wp_login_url();
		}
		?>
		<div class="header-account">
			<a href="<?php echo esc_url( $account_url ); ?>" class="account-link">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
					<circle cx="10" cy="7" r="4"/>
					<path d="M2 19c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'My Account', 'nosfir' ); ?></span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_hero_section' ) ) {
	/**
	 * Hero section
	 */
	function nosfir_hero_section() {
		if ( ! is_front_page() || ! get_theme_mod( 'nosfir_hero_enable', true ) ) {
			return;
		}
		
		get_template_part( 'template-parts/hero' );
	}
}

if ( ! function_exists( 'nosfir_footer_navigation' ) ) {
	/**
	 * Footer navigation
	 */
	function nosfir_footer_navigation() {
		if ( ! has_nav_menu( 'footer' ) ) {
			return;
		}
		?>
		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'nosfir' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'menu_class'     => 'footer-menu',
				'container'      => false,
				'depth'          => 1,
			) );
			?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'nosfir_footer_social' ) ) {
	/**
	 * Footer social links
	 */
	function nosfir_footer_social() {
		if ( ! has_nav_menu( 'social' ) ) {
			return;
		}
		?>
		<div class="footer-social">
			<h3 class="footer-social-title"><?php esc_html_e( 'Follow Us', 'nosfir' ); ?></h3>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'social',
				'menu_class'     => 'social-menu',
				'container'      => false,
				'depth'          => 1,
				'link_before'    => '<span class="screen-reader-text">',
				'link_after'     => '</span>',
			) );
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_footer_cta' ) ) {
	/**
	 * Footer CTA section
	 */
	function nosfir_footer_cta() {
		if ( ! get_theme_mod( 'nosfir_footer_cta_enable', false ) ) {
			return;
		}
		
		$title = get_theme_mod( 'nosfir_footer_cta_title' );
		$text = get_theme_mod( 'nosfir_footer_cta_text' );
		$button_text = get_theme_mod( 'nosfir_footer_cta_button_text' );
		$button_url = get_theme_mod( 'nosfir_footer_cta_button_url' );
		
		if ( ! $title && ! $text ) {
			return;
		}
		?>
		<div class="footer-cta">
			<div class="container">
				<div class="footer-cta-inner">
					<?php if ( $title ) : ?>
						<h2 class="footer-cta-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
					
					<?php if ( $text ) : ?>
						<div class="footer-cta-text"><?php echo wp_kses_post( $text ); ?></div>
					<?php endif; ?>
					
					<?php if ( $button_text && $button_url ) : ?>
						<a href="<?php echo esc_url( $button_url ); ?>" class="footer-cta-button button">
							<?php echo esc_html( $button_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_body_open' ) ) {
	/**
	 * Body open
	 */
	function nosfir_body_open() {
		do_action( 'nosfir_body_open' );
	}
}

if ( ! function_exists( 'nosfir_preloader' ) ) {
	/**
	 * Preloader
	 */
	function nosfir_preloader() {
		if ( ! get_theme_mod( 'nosfir_preloader', false ) ) {
			return;
		}
		?>
		<div class="preloader">
			<div class="preloader-inner">
				<div class="spinner"></div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_scroll_to_top' ) ) {
	/**
	 * Scroll to top button
	 */
	function nosfir_scroll_to_top() {
		if ( ! get_theme_mod( 'nosfir_scroll_to_top', true ) ) {
			return;
		}
		?>
		<button class="scroll-to-top" aria-label="<?php esc_attr_e( 'Scroll to top', 'nosfir' ); ?>">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
				<path d="M10 6l-6 6 1.41 1.41L10 8.83l4.59 4.58L16 12z"/>
			</svg>
		</button>
		<?php
	}
}

if ( ! function_exists( 'nosfir_mobile_menu_overlay' ) ) {
	/**
	 * Mobile menu overlay
	 */
	function nosfir_mobile_menu_overlay() {
		echo '<div class="mobile-menu-overlay"></div>';
	}
}

if ( ! function_exists( 'nosfir_post_footer_meta' ) ) {
	/**
	 * Post footer meta for archives
	 */
	function nosfir_post_footer_meta() {
		if ( 'post' !== get_post_type() ) {
			return;
		}
		?>
		<footer class="entry-footer">
			<a href="<?php the_permalink(); ?>" class="read-more">
				<?php esc_html_e( 'Read More', 'nosfir' ); ?>
				<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
					<path d="M6.293 11.293a1 1 0 010 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 1.414L5.414 8l2.293 2.293a1 1 0 01-1.414 1.414z"/>
				</svg>
			</a>
		</footer>
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_tags' ) ) {
	/**
	 * Post tags
	 */
	function nosfir_post_tags() {
		if ( ! has_tag() || ! get_theme_mod( 'nosfir_post_tags', true ) ) {
			return;
		}
		?>
		<div class="post-tags">
			<span class="tags-label"><?php esc_html_e( 'Tags:', 'nosfir' ); ?></span>
			<?php the_tags( '', ', ', '' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_share' ) ) {
	/**
	 * Post share buttons
	 */
	function nosfir_post_share() {
		if ( ! get_theme_mod( 'nosfir_post_share', true ) ) {
			return;
		}
		
		nosfir_social_share();
	}
}

if ( ! function_exists( 'nosfir_search_header' ) ) {
	/**
	 * Search results header
	 */
	function nosfir_search_header() {
		?>
		<header class="page-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search Results for: %s', 'nosfir' ),
					'<span>' . get_search_query() . '</span>'
				);
				?>
			</h1>
		</header>
		<?php
	}
}

if ( ! function_exists( 'nosfir_404_header' ) ) {
	/**
	 * 404 page header
	 */
	function nosfir_404_header() {
		?>
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( '404', 'nosfir' ); ?></h1>
			<p class="page-subtitle"><?php esc_html_e( 'Oops! That page can\'t be found.', 'nosfir' ); ?></p>
		</header>
		<?php
	}
}

if ( ! function_exists( 'nosfir_404_content' ) ) {
	/**
	 * 404 page content
	 */
	function nosfir_404_content() {
		?>
		<div class="page-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'nosfir' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_404_search_form' ) ) {
	/**
	 * 404 page search form
	 */
	function nosfir_404_search_form() {
		nosfir_search_form( 'error-404-search' );
	}
}

if ( ! function_exists( 'nosfir_404_recent_posts' ) ) {
	/**
	 * 404 page recent posts
	 */
	function nosfir_404_recent_posts() {
		$recent_posts = wp_get_recent_posts( array(
			'numberposts' => 5,
			'post_status' => 'publish',
		) );
		
		if ( empty( $recent_posts ) ) {
			return;
		}
		?>
		<div class="recent-posts">
			<h2><?php esc_html_e( 'Recent Posts', 'nosfir' ); ?></h2>
			<ul>
				<?php foreach ( $recent_posts as $post ) : ?>
					<li>
						<a href="<?php echo esc_url( get_permalink( $post['ID'] ) ); ?>">
							<?php echo esc_html( $post['post_title'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Homepage section functions (placeholders)
 * These would be implemented based on specific requirements
 */
if ( ! function_exists( 'nosfir_homepage_hero' ) ) {
	function nosfir_homepage_hero() {
		// Implement hero section
	}
}

if ( ! function_exists( 'nosfir_homepage_featured' ) ) {
	function nosfir_homepage_featured() {
		// Implement featured section
	}
}

if ( ! function_exists( 'nosfir_homepage_services' ) ) {
	function nosfir_homepage_services() {
		// Implement services section
	}
}

if ( ! function_exists( 'nosfir_homepage_about' ) ) {
	function nosfir_homepage_about() {
		// Implement about section
	}
}

if ( ! function_exists( 'nosfir_homepage_portfolio' ) ) {
	function nosfir_homepage_portfolio() {
		// Implement portfolio section
	}
}

if ( ! function_exists( 'nosfir_homepage_testimonials' ) ) {
	function nosfir_homepage_testimonials() {
		// Implement testimonials section
	}
}

if ( ! function_exists( 'nosfir_homepage_blog' ) ) {
	function nosfir_homepage_blog() {
		// Implement blog section
	}
}

if ( ! function_exists( 'nosfir_homepage_cta' ) ) {
	function nosfir_homepage_cta() {
		// Implement CTA section
	}
}

if ( ! function_exists( 'nosfir_homepage_content' ) ) {
	function nosfir_homepage_content() {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
}

if ( ! function_exists( 'nosfir_homepage_sections' ) ) {
	function nosfir_homepage_sections() {
		// Sections are added via hooks
		do_action( 'nosfir_homepage_sections' );
	}
}

/**
 * Custom Hook Locations
 * These allow child themes and plugins to add content
 */
do_action( 'nosfir_hooks_loaded' );