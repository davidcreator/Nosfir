<?php
/**
 * Nosfir WooCommerce hooks
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage
 *
 * @see  nosfir_product_categories()
 * @see  nosfir_recent_products()
 * @see  nosfir_featured_products()
 * @see  nosfir_popular_products()
 * @see  nosfir_on_sale_products()
 * @see  nosfir_best_selling_products()
 */
add_action( 'nosfir_homepage', 'nosfir_product_categories', 20 );
add_action( 'nosfir_homepage', 'nosfir_recent_products', 30 );
add_action( 'nosfir_homepage', 'nosfir_featured_products', 40 );
add_action( 'nosfir_homepage', 'nosfir_popular_products', 50 );
add_action( 'nosfir_homepage', 'nosfir_on_sale_products', 60 );
add_action( 'nosfir_homepage', 'nosfir_best_selling_products', 70 );

/**
 * Layout
 *
 * @see  nosfir_before_content()
 * @see  nosfir_after_content()
 * @see  woocommerce_breadcrumb()
 * @see  nosfir_shop_messages()
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_action( 'woocommerce_before_main_content', 'nosfir_before_content', 10 );
add_action( 'woocommerce_after_main_content', 'nosfir_after_content', 10 );
add_action( 'nosfir_content_top', 'nosfir_shop_messages', 15 );
add_action( 'nosfir_before_content', 'woocommerce_breadcrumb', 10 );

/**
 * Shop Loop Sorting and Pagination
 */
add_action( 'woocommerce_after_shop_loop', 'nosfir_sorting_wrapper', 9 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 30 );
add_action( 'woocommerce_after_shop_loop', 'nosfir_sorting_wrapper_close', 31 );

add_action( 'woocommerce_before_shop_loop', 'nosfir_sorting_wrapper', 9 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'nosfir_woocommerce_pagination', 30 );
add_action( 'woocommerce_before_shop_loop', 'nosfir_sorting_wrapper_close', 31 );

/**
 * Shop Loop Products
 *
 * @see nosfir_template_loop_product_thumbnail()
 * @see nosfir_shop_loop_item_title()
 * @see nosfir_product_loop_start()
 * @see nosfir_product_loop_end()
 */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'nosfir_template_loop_product_thumbnail', 10 );

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'nosfir_shop_loop_item_title', 10 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );

/**
 * Product Loop Actions
 *
 * @see nosfir_quick_view_button()
 * @see nosfir_wishlist_button()
 * @see nosfir_compare_button()
 * @see nosfir_product_badge()
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'nosfir_product_badge', 9 );
add_action( 'woocommerce_after_shop_loop_item', 'nosfir_quick_view_button', 15 );
add_action( 'woocommerce_after_shop_loop_item', 'nosfir_wishlist_button', 20 );
add_action( 'woocommerce_after_shop_loop_item', 'nosfir_compare_button', 25 );

/**
 * Single Product
 *
 * @see nosfir_edit_post_link()
 * @see nosfir_upsell_display()
 * @see nosfir_single_product_pagination()
 * @see nosfir_sticky_single_add_to_cart()
 * @see nosfir_product_share()
 */
add_action( 'woocommerce_single_product_summary', 'nosfir_edit_post_link', 60 );
add_action( 'woocommerce_single_product_summary', 'nosfir_product_share', 50 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'nosfir_upsell_display', 15 );

add_action( 'woocommerce_after_single_product_summary', 'nosfir_single_product_pagination', 30 );
add_action( 'nosfir_after_footer', 'nosfir_sticky_single_add_to_cart', 999 );

/**
 * Single Product Gallery
 *
 * @see nosfir_product_gallery_trigger()
 */
add_filter( 'woocommerce_single_product_image_gallery_classes', 'nosfir_product_gallery_classes' );

/**
 * Single Product Tabs
 */
add_filter( 'woocommerce_product_tabs', 'nosfir_product_tabs', 98 );
add_filter( 'woocommerce_product_description_heading', '__return_null' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );

/**
 * Header
 *
 * @see nosfir_product_search()
 * @see nosfir_header_cart()
 */
add_action( 'nosfir_header', 'nosfir_product_search', 40 );
add_action( 'nosfir_header', 'nosfir_header_cart', 60 );

/**
 * Mobile Menu
 *
 * @see nosfir_handheld_footer_bar()
 */
add_action( 'nosfir_footer', 'nosfir_handheld_footer_bar', 999 );

/**
 * Cart
 *
 * @see nosfir_cart_link_fragment()
 * @see nosfir_cross_sell_display()
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'nosfir_cart_link_fragment' );
add_filter( 'woocommerce_cross_sells_columns', 'nosfir_cross_sells_columns' );

/**
 * Checkout
 *
 * @see nosfir_checkout_order_review_heading()
 */
add_action( 'woocommerce_checkout_order_review', 'nosfir_checkout_order_review_heading', 5 );

/**
 * My Account
 *
 * @see nosfir_account_navigation_wrapper()
 * @see nosfir_account_navigation_wrapper_close()
 * @see nosfir_account_content_wrapper()
 * @see nosfir_account_content_wrapper_close()
 */
add_action( 'woocommerce_before_account_navigation', 'nosfir_account_navigation_wrapper', 5 );
add_action( 'woocommerce_after_account_navigation', 'nosfir_account_navigation_wrapper_close', 15 );
add_action( 'woocommerce_before_account_navigation', 'nosfir_account_user_info', 10 );
add_action( 'woocommerce_before_my_account', 'nosfir_account_content_wrapper', 5 );
add_action( 'woocommerce_after_my_account', 'nosfir_account_content_wrapper_close', 15 );

/**
 * Filters
 *
 * @see nosfir_products_per_page()
 * @see nosfir_loop_columns()
 * @see nosfir_thumbnail_columns()
 * @see nosfir_related_products_args()
 */
add_filter( 'loop_shop_per_page', 'nosfir_products_per_page' );
add_filter( 'loop_shop_columns', 'nosfir_loop_columns' );
add_filter( 'woocommerce_product_thumbnails_columns', 'nosfir_thumbnail_columns' );
add_filter( 'woocommerce_output_related_products_args', 'nosfir_related_products_args' );

/**
 * Ajax Handlers
 *
 * @see nosfir_ajax_add_to_cart()
 * @see nosfir_quick_view_ajax()
 * @see nosfir_add_to_wishlist_ajax()
 * @see nosfir_remove_from_wishlist_ajax()
 * @see nosfir_add_to_compare_ajax()
 */
add_action( 'wp_ajax_nosfir_add_to_cart', 'nosfir_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_nosfir_add_to_cart', 'nosfir_ajax_add_to_cart' );
add_action( 'wp_ajax_nosfir_quick_view', 'nosfir_quick_view_ajax' );
add_action( 'wp_ajax_nopriv_nosfir_quick_view', 'nosfir_quick_view_ajax' );
add_action( 'wp_ajax_nosfir_add_to_wishlist', 'nosfir_add_to_wishlist_ajax' );
add_action( 'wp_ajax_nopriv_nosfir_add_to_wishlist', 'nosfir_add_to_wishlist_ajax' );
add_action( 'wp_ajax_nosfir_remove_from_wishlist', 'nosfir_remove_from_wishlist_ajax' );
add_action( 'wp_ajax_nopriv_nosfir_remove_from_wishlist', 'nosfir_remove_from_wishlist_ajax' );
add_action( 'wp_ajax_nosfir_add_to_compare', 'nosfir_add_to_compare_ajax' );
add_action( 'wp_ajax_nopriv_nosfir_add_to_compare', 'nosfir_add_to_compare_ajax' );

/**
 * Integrations
 *
 * @see nosfir_woocommerce_brands_archive()
 * @see nosfir_woocommerce_brands_single()
 * @see nosfir_woocommerce_brands_homepage_section()
 */
if ( class_exists( 'WC_Brands' ) ) {
	add_action( 'woocommerce_archive_description', 'nosfir_woocommerce_brands_archive', 5 );
	add_action( 'woocommerce_single_product_summary', 'nosfir_woocommerce_brands_single', 4 );
	add_action( 'nosfir_homepage', 'nosfir_woocommerce_brands_homepage_section', 80 );
}

/**
 * YITH Wishlist Integration
 */
if ( defined( 'YITH_WCWL' ) ) {
	remove_action( 'woocommerce_after_shop_loop_item', 'nosfir_wishlist_button', 20 );
	add_action( 'woocommerce_after_shop_loop_item', array( YITH_WCWL_Frontend(), 'print_button' ), 20 );
}

/**
 * YITH Quick View Integration
 */
if ( defined( 'YITH_WCQV' ) ) {
	remove_action( 'woocommerce_after_shop_loop_item', 'nosfir_quick_view_button', 15 );
}

/**
 * WooCommerce Subscriptions
 */
if ( class_exists( 'WC_Subscriptions' ) ) {
	add_filter( 'woocommerce_my_account_my_subscriptions_actions', 'nosfir_subscriptions_actions', 10, 2 );
}

/**
 * Product Vendors
 */
if ( class_exists( 'WC_Product_Vendors' ) ) {
	add_action( 'woocommerce_single_product_summary', 'nosfir_product_vendor_info', 25 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'nosfir_loop_vendor_info', 15 );
}

/**
 * Advanced Product Labels
 */
if ( class_exists( 'BeRocket_products_label' ) ) {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'nosfir_product_badge', 9 );
}

/**
 * Custom Hooks
 */
do_action( 'nosfir_woocommerce_init' );

/**
 * Helper Functions for Hooks
 */
if ( ! function_exists( 'nosfir_product_gallery_classes' ) ) {
	/**
	 * Add custom classes to product gallery
	 *
	 * @param array $classes
	 * @return array
	 */
	function nosfir_product_gallery_classes( $classes ) {
		$classes[] = 'nosfir-product-gallery';
		
		if ( get_theme_mod( 'nosfir_product_gallery_slider', true ) ) {
			$classes[] = 'with-slider';
		}
		
		if ( get_theme_mod( 'nosfir_product_zoom', true ) ) {
			$classes[] = 'with-zoom';
		}
		
		return $classes;
	}
}

if ( ! function_exists( 'nosfir_product_tabs' ) ) {
	/**
	 * Customize product tabs
	 *
	 * @param array $tabs
	 * @return array
	 */
	function nosfir_product_tabs( $tabs ) {
		// Add custom tab
		if ( get_theme_mod( 'nosfir_product_custom_tab', false ) ) {
			$tabs['custom_tab'] = array(
				'title'    => get_theme_mod( 'nosfir_product_custom_tab_title', __( 'Custom Tab', 'nosfir' ) ),
				'priority' => 50,
				'callback' => 'nosfir_product_custom_tab_content',
			);
		}
		
		// Rename tabs
		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['title'] = __( 'Details', 'nosfir' );
		}
		
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['title'] = __( 'Specifications', 'nosfir' );
		}
		
		return $tabs;
	}
}

if ( ! function_exists( 'nosfir_product_custom_tab_content' ) ) {
	/**
	 * Custom tab content
	 */
	function nosfir_product_custom_tab_content() {
		echo '<h2>' . esc_html( get_theme_mod( 'nosfir_product_custom_tab_title', __( 'Custom Tab', 'nosfir' ) ) ) . '</h2>';
		echo '<div class="custom-tab-content">';
		echo wp_kses_post( get_theme_mod( 'nosfir_product_custom_tab_content', '' ) );
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_edit_post_link' ) ) {
	/**
	 * Display edit post link for products
	 */
	function nosfir_edit_post_link() {
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
	}
}

if ( ! function_exists( 'nosfir_checkout_order_review_heading' ) ) {
	/**
	 * Add heading to checkout order review
	 */
	function nosfir_checkout_order_review_heading() {
		?>
		<h3 class="order-review-heading"><?php esc_html_e( 'Your Order', 'nosfir' ); ?></h3>
		<?php
	}
}

if ( ! function_exists( 'nosfir_account_navigation_wrapper' ) ) {
	/**
	 * Account navigation wrapper start
	 */
	function nosfir_account_navigation_wrapper() {
		echo '<div class="nosfir-account-navigation-wrapper">';
	}
}

if ( ! function_exists( 'nosfir_account_navigation_wrapper_close' ) ) {
	/**
	 * Account navigation wrapper end
	 */
	function nosfir_account_navigation_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_account_content_wrapper' ) ) {
	/**
	 * Account content wrapper start
	 */
	function nosfir_account_content_wrapper() {
		echo '<div class="nosfir-account-content-wrapper">';
	}
}

if ( ! function_exists( 'nosfir_account_content_wrapper_close' ) ) {
	/**
	 * Account content wrapper end
	 */
	function nosfir_account_content_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_account_user_info' ) ) {
	/**
	 * Display user info in account navigation
	 */
	function nosfir_account_user_info() {
		$current_user = wp_get_current_user();
		if ( ! $current_user->exists() ) {
			return;
		}
		?>
		<div class="nosfir-account-user-info">
			<div class="user-avatar">
				<?php echo get_avatar( $current_user->ID, 80 ); ?>
			</div>
			<div class="user-details">
				<h3><?php echo esc_html( $current_user->display_name ); ?></h3>
				<span class="user-email"><?php echo esc_html( $current_user->user_email ); ?></span>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_product_vendor_info' ) ) {
	/**
	 * Display vendor info on single product
	 */
	function nosfir_product_vendor_info() {
		global $product;
		
		if ( ! function_exists( 'WC_Product_Vendors' ) ) {
			return;
		}
		
		$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product->get_id() );
		
		if ( ! $vendor_id ) {
			return;
		}
		
		$vendor = WC_Product_Vendors_Utils::get_vendor_data_from_id( $vendor_id );
		
		if ( ! $vendor ) {
			return;
		}
		?>
		<div class="nosfir-product-vendor">
			<span class="vendor-label"><?php esc_html_e( 'Sold by:', 'nosfir' ); ?></span>
			<a href="<?php echo esc_url( $vendor['link'] ); ?>" class="vendor-name">
				<?php echo esc_html( $vendor['name'] ); ?>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_loop_vendor_info' ) ) {
	/**
	 * Display vendor info in product loop
	 */
	function nosfir_loop_vendor_info() {
		global $product;
		
		if ( ! function_exists( 'WC_Product_Vendors' ) ) {
			return;
		}
		
		$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product->get_id() );
		
		if ( ! $vendor_id ) {
			return;
		}
		
		$vendor = WC_Product_Vendors_Utils::get_vendor_data_from_id( $vendor_id );
		
		if ( ! $vendor ) {
			return;
		}
		?>
		<div class="nosfir-loop-vendor">
			<a href="<?php echo esc_url( $vendor['link'] ); ?>">
				<?php echo esc_html( $vendor['name'] ); ?>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_subscriptions_actions' ) ) {
	/**
	 * Customize subscription actions
	 *
	 * @param array $actions
	 * @param WC_Subscription $subscription
	 * @return array
	 */
	function nosfir_subscriptions_actions( $actions, $subscription ) {
		// Add custom action
		if ( $subscription->can_be_updated_to( 'cancelled' ) ) {
			$actions['cancel'] = array(
				'url'  => wp_nonce_url( add_query_arg( array( 'subscription_id' => $subscription->get_id(), 'change_subscription_to' => 'cancelled' ) ), $subscription->get_id() ),
				'name' => __( 'Cancel', 'nosfir' ),
			);
		}
		
		return $actions;
	}
}

if ( ! function_exists( 'nosfir_woocommerce_brands_archive' ) ) {
	/**
	 * Display brand image on brand archives
	 *
	 * @return void
	 */
	function nosfir_woocommerce_brands_archive() {
		if ( is_tax( 'product_brand' ) ) {
			$term = get_queried_object();
			$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			
			if ( $thumbnail_id ) {
				echo '<div class="brand-archive-header">';
				echo wp_get_attachment_image( $thumbnail_id, 'large' );
				echo '</div>';
			}
		}
	}
}

if ( ! function_exists( 'nosfir_woocommerce_brands_single' ) ) {
	/**
	 * Display brand on single product
	 *
	 * @return void
	 */
	function nosfir_woocommerce_brands_single() {
		global $product;
		
		$brands = wp_get_post_terms( $product->get_id(), 'product_brand' );
		
		if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
			echo '<div class="product-brands">';
			foreach ( $brands as $brand ) {
				$thumbnail_id = get_term_meta( $brand->term_id, 'thumbnail_id', true );
				
				echo '<a href="' . esc_url( get_term_link( $brand ) ) . '" class="product-brand">';
				
				if ( $thumbnail_id ) {
					echo wp_get_attachment_image( $thumbnail_id, 'thumbnail' );
				} else {
					echo esc_html( $brand->name );
				}
				
				echo '</a>';
			}
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'nosfir_woocommerce_brands_homepage_section' ) ) {
	/**
	 * Display brands section on homepage
	 *
	 * @return void
	 */
	function nosfir_woocommerce_brands_homepage_section() {
		$args = apply_filters(
			'nosfir_woocommerce_brands_args',
			array(
				'number'     => 8,
				'columns'    => 4,
				'orderby'    => 'name',
				'hide_empty' => true,
				'title'      => __( 'Shop by Brand', 'nosfir' ),
			)
		);
		
		$brands = get_terms( array(
			'taxonomy'   => 'product_brand',
			'number'     => $args['number'],
			'orderby'    => $args['orderby'],
			'hide_empty' => $args['hide_empty'],
		) );
		
		if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
			?>
			<section class="nosfir-product-section nosfir-brands-section">
				<h2 class="section-title"><?php echo esc_html( $args['title'] ); ?></h2>
				<div class="brands-grid columns-<?php echo esc_attr( $args['columns'] ); ?>">
					<?php foreach ( $brands as $brand ) :
						$thumbnail_id = get_term_meta( $brand->term_id, 'thumbnail_id', true );
						?>
						<div class="brand-item">
							<a href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
								<?php if ( $thumbnail_id ) : ?>
									<?php echo wp_get_attachment_image( $thumbnail_id, 'medium' ); ?>
								<?php else : ?>
									<span class="brand-name"><?php echo esc_html( $brand->name ); ?></span>
								<?php endif; ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
			<?php
		}
	}
}

/**
 * Additional Hooks for Theme Customizer Settings
 */
if ( get_theme_mod( 'nosfir_product_hover_image', true ) ) {
	add_action( 'woocommerce_before_shop_loop_item_title', 'nosfir_product_hover_image', 11 );
}

if ( ! get_theme_mod( 'nosfir_product_rating_loop', true ) ) {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}

if ( get_theme_mod( 'nosfir_product_countdown', false ) ) {
	add_action( 'woocommerce_single_product_summary', 'nosfir_product_countdown', 25 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'nosfir_product_countdown_loop', 8 );
}

if ( get_theme_mod( 'nosfir_size_guide', false ) ) {
	add_action( 'woocommerce_single_product_summary', 'nosfir_size_guide_link', 30 );
}

if ( get_theme_mod( 'nosfir_estimated_delivery', false ) ) {
	add_action( 'woocommerce_single_product_summary', 'nosfir_estimated_delivery', 35 );
}

/**
 * Load more products AJAX
 */
if ( get_theme_mod( 'nosfir_shop_load_more', false ) ) {
	add_action( 'wp_ajax_nosfir_load_more_products', 'nosfir_load_more_products' );
	add_action( 'wp_ajax_nopriv_nosfir_load_more_products', 'nosfir_load_more_products' );
	add_filter( 'woocommerce_pagination_args', 'nosfir_pagination_args' );
}

/**
 * Infinite scroll
 */
if ( get_theme_mod( 'nosfir_shop_infinite_scroll', false ) ) {
	add_action( 'wp_enqueue_scripts', 'nosfir_infinite_scroll_scripts' );
}

/**
 * Product filters sidebar toggle for mobile
 */
if ( wp_is_mobile() ) {
	add_action( 'woocommerce_before_shop_loop', 'nosfir_mobile_filter_toggle', 8 );
}

/**
 * Apply theme customizations
 */
add_action( 'init', 'nosfir_apply_woocommerce_customizations', 999 );

if ( ! function_exists( 'nosfir_apply_woocommerce_customizations' ) ) {
	/**
	 * Apply WooCommerce customizations from theme options
	 */
	function nosfir_apply_woocommerce_customizations() {
		// Disable WooCommerce styles if option is set
		if ( get_theme_mod( 'nosfir_disable_wc_styles', false ) ) {
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		}
		
		// Custom shop columns for different devices
		if ( wp_is_mobile() ) {
			add_filter( 'loop_shop_columns', function() {
				return get_theme_mod( 'nosfir_products_per_row_mobile', 2 );
			}, 999 );
		} elseif ( wp_is_tablet() ) {
			add_filter( 'loop_shop_columns', function() {
				return get_theme_mod( 'nosfir_products_per_row_tablet', 3 );
			}, 999 );
		}
	}
}