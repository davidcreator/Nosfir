<?php
/**
 * WooCommerce Template Functions.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'nosfir_woo_cart_available' ) ) {
	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	function nosfir_woo_cart_available() {
		$woo = WC();
		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}
}

if ( ! function_exists( 'nosfir_before_content' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_before_content() {
		?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
		<?php
	}
}

if ( ! function_exists( 'nosfir_after_content' ) ) {
	/**
	 * After Content
	 * Closes the wrapping divs
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_after_content() {
		?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php
		do_action( 'nosfir_sidebar' );
	}
}

if ( ! function_exists( 'nosfir_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	function nosfir_cart_link_fragment( $fragments ) {
		global $woocommerce;

		ob_start();
		nosfir_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		ob_start();
		nosfir_handheld_footer_bar_cart_link();
		$fragments['a.footer-cart-contents'] = ob_get_clean();

		ob_start();
		?>
		<span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
		<?php
		$fragments['.cart-count'] = ob_get_clean();

		return $fragments;
	}
}

if ( ! function_exists( 'nosfir_cart_link' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function nosfir_cart_link() {
		if ( ! nosfir_woo_cart_available() ) {
			return;
		}
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'nosfir' ); ?>">
			<?php
			$cart_icon_style = get_theme_mod( 'nosfir_cart_icon_style', 'outline' );
			
			// Cart icon
			echo nosfir_get_cart_icon( $cart_icon_style );
			
			// Cart count
			if ( get_theme_mod( 'nosfir_show_cart_count', true ) ) {
				$item_count = WC()->cart->get_cart_contents_count();
				?>
				<span class="cart-count"><?php echo esc_html( $item_count ); ?></span>
				<?php
			}
			
			// Cart total
			if ( get_theme_mod( 'nosfir_show_cart_total', false ) ) {
				?>
				<span class="cart-total"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span>
				<?php
			}
			?>
		</a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_get_cart_icon' ) ) {
	/**
	 * Get cart icon SVG
	 *
	 * @param string $style Icon style
	 * @return string
	 */
	function nosfir_get_cart_icon( $style = 'outline' ) {
		$icons = array(
			'outline' => '<svg class="cart-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7" cy="17" r="1"/><circle cx="14" cy="17" r="1"/><path d="M3 3h1l1 11h10l1-7H6"/></svg>',
			'filled' => '<svg class="cart-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/><circle cx="7" cy="17" r="1"/><circle cx="14" cy="17" r="1"/></svg>',
			'bag' => '<svg class="cart-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M5 7h10l-.5 8H5.5L5 7zm0 0l.5-4h9l.5 4M7 7v2m6-2v2"/></svg>',
			'basket' => '<svg class="cart-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path d="M1 7h18M3 7l1 10h12l1-10M8 7V4a2 2 0 012-2v0a2 2 0 012 2v3"/></svg>',
		);
		
		return isset( $icons[ $style ] ) ? $icons[ $style ] : $icons['outline'];
	}
}

if ( ! function_exists( 'nosfir_product_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @since  1.0.0
	 * @uses  nosfir_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function nosfir_product_search() {
		if ( nosfir_is_woocommerce_activated() ) {
			?>
			<div class="site-search">
				<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  nosfir_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function nosfir_header_cart() {
		if ( nosfir_is_woocommerce_activated() ) {
			if ( is_cart() ) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
			?>
			<ul id="site-header-cart" class="site-header-cart menu">
				<li class="<?php echo esc_attr( $class ); ?>">
					<?php nosfir_cart_link(); ?>
				</li>
				<li>
					<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
				</li>
			</ul>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_upsell_display' ) ) {
	/**
	 * Upsells
	 * Replace the default upsell function with our own which displays the correct number product columns
	 *
	 * @since   1.0.0
	 * @return  void
	 * @uses    woocommerce_upsell_display()
	 */
	function nosfir_upsell_display() {
		$columns = apply_filters( 'nosfir_upsells_columns', 4 );
		woocommerce_upsell_display( -1, $columns );
	}
}

if ( ! function_exists( 'nosfir_sorting_wrapper' ) ) {
	/**
	 * Sorting wrapper
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_sorting_wrapper() {
		echo '<div class="nosfir-sorting">';
	}
}

if ( ! function_exists( 'nosfir_sorting_wrapper_close' ) ) {
	/**
	 * Sorting wrapper close
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_sorting_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_product_columns_wrapper' ) ) {
	/**
	 * Product columns wrapper
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_product_columns_wrapper() {
		$columns = nosfir_loop_columns();
		echo '<div class="columns-' . absint( $columns ) . '">';
	}
}

if ( ! function_exists( 'nosfir_loop_columns' ) ) {
	/**
	 * Default loop columns on product archives
	 *
	 * @return integer products per row
	 * @since  1.0.0
	 */
	function nosfir_loop_columns() {
		$columns = get_theme_mod( 'nosfir_products_per_row', 4 );

		// Adjust columns for smaller screens
		if ( wp_is_mobile() ) {
			$columns = 2;
		}

		return apply_filters( 'nosfir_loop_columns', $columns );
	}
}

if ( ! function_exists( 'nosfir_product_columns_wrapper_close' ) ) {
	/**
	 * Product columns wrapper close
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function nosfir_product_columns_wrapper_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_shop_messages' ) ) {
	/**
	 * Nosfir shop messages
	 *
	 * @since   1.0.0
	 * @uses    nosfir_do_shortcode
	 */
	function nosfir_shop_messages() {
		if ( ! is_checkout() ) {
			echo nosfir_do_shortcode( 'woocommerce_messages' );
		}
	}
}

if ( ! function_exists( 'nosfir_woocommerce_pagination' ) ) {
	/**
	 * Nosfir WooCommerce Pagination
	 *
	 * @since 1.0.0
	 */
	function nosfir_woocommerce_pagination() {
		if ( woocommerce_products_will_display() ) {
			woocommerce_pagination();
		}
	}
}

if ( ! function_exists( 'nosfir_do_shortcode' ) ) {
	/**
	 * Call a shortcode function by tag name.
	 *
	 * @since  1.0.0
	 * @param string $tag     The shortcode whose function to call.
	 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
	 * @param array  $content The shortcode's content. Default is null (none).
	 * @return string|bool False on failure, the result of the shortcode on success.
	 */
	function nosfir_do_shortcode( $tag, array $atts = array(), $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}
}

if ( ! function_exists( 'nosfir_product_categories' ) ) {
	/**
	 * Display Product Categories
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function nosfir_product_categories( $args ) {
		$args = apply_filters(
			'nosfir_product_categories_args',
			array(
				'limit'            => 6,
				'columns'          => 3,
				'child_categories' => 0,
				'orderby'          => 'menu_order',
				'title'            => __( 'Shop by Category', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'product_categories',
			apply_filters(
				'nosfir_product_categories_shortcode_args',
				array(
					'number'  => intval( $args['limit'] ),
					'columns' => intval( $args['columns'] ),
					'orderby' => esc_attr( $args['orderby'] ),
					'parent'  => esc_attr( $args['child_categories'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product-category' ) ) {
			echo '<section class="nosfir-product-section nosfir-product-categories" aria-label="' . esc_attr__( 'Product Categories', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_product_categories' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_product_categories_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_product_categories' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_recent_products' ) ) {
	/**
	 * Display Recent Products
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function nosfir_recent_products( $args ) {
		$args = apply_filters(
			'nosfir_recent_products_args',
			array(
				'limit'   => 8,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'title'   => __( 'New Arrivals', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'products',
			apply_filters(
				'nosfir_recent_products_shortcode_args',
				array(
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="nosfir-product-section nosfir-recent-products" aria-label="' . esc_attr__( 'Recent Products', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_recent_products' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_recent_products_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_recent_products' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_featured_products' ) ) {
	/**
	 * Display Featured Products
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function nosfir_featured_products( $args ) {
		$args = apply_filters(
			'nosfir_featured_products_args',
			array(
				'limit'      => 8,
				'columns'    => 4,
				'orderby'    => 'date',
				'order'      => 'desc',
				'visibility' => 'featured',
				'title'      => __( 'Featured Products', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'products',
			apply_filters(
				'nosfir_featured_products_shortcode_args',
				array(
					'per_page'   => intval( $args['limit'] ),
					'columns'    => intval( $args['columns'] ),
					'orderby'    => esc_attr( $args['orderby'] ),
					'order'      => esc_attr( $args['order'] ),
					'visibility' => esc_attr( $args['visibility'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="nosfir-product-section nosfir-featured-products" aria-label="' . esc_attr__( 'Featured Products', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_featured_products' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_featured_products_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_featured_products' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_popular_products' ) ) {
	/**
	 * Display Popular Products
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function nosfir_popular_products( $args ) {
		$args = apply_filters(
			'nosfir_popular_products_args',
			array(
				'limit'   => 8,
				'columns' => 4,
				'orderby' => 'rating',
				'order'   => 'desc',
				'title'   => __( 'Top Rated', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'products',
			apply_filters(
				'nosfir_popular_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="nosfir-product-section nosfir-popular-products" aria-label="' . esc_attr__( 'Popular Products', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_popular_products' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_popular_products_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_popular_products' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_on_sale_products' ) ) {
	/**
	 * Display On Sale Products
	 *
	 * @param array $args the product section args.
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_on_sale_products( $args ) {
		$args = apply_filters(
			'nosfir_on_sale_products_args',
			array(
				'limit'   => 8,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'on_sale' => 'true',
				'title'   => __( 'Special Offers', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'products',
			apply_filters(
				'nosfir_on_sale_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
					'on_sale'  => esc_attr( $args['on_sale'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="nosfir-product-section nosfir-on-sale-products" aria-label="' . esc_attr__( 'On Sale Products', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_on_sale_products' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_on_sale_products_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_on_sale_products' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_best_selling_products' ) ) {
	/**
	 * Display Best Selling Products
	 *
	 * @since 1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function nosfir_best_selling_products( $args ) {
		$args = apply_filters(
			'nosfir_best_selling_products_args',
			array(
				'limit'   => 8,
				'columns' => 4,
				'orderby' => 'popularity',
				'order'   => 'desc',
				'title'   => esc_attr__( 'Best Sellers', 'nosfir' ),
			)
		);

		$shortcode_content = nosfir_do_shortcode(
			'products',
			apply_filters(
				'nosfir_best_selling_products_shortcode_args',
				array(
					'per_page' => intval( $args['limit'] ),
					'columns'  => intval( $args['columns'] ),
					'orderby'  => esc_attr( $args['orderby'] ),
					'order'    => esc_attr( $args['order'] ),
				)
			)
		);

		if ( false !== strpos( $shortcode_content, 'product' ) ) {
			echo '<section class="nosfir-product-section nosfir-best-selling-products" aria-label="' . esc_attr__( 'Best Selling Products', 'nosfir' ) . '">';
			
			do_action( 'nosfir_homepage_before_best_selling_products' );
			
			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
			
			do_action( 'nosfir_homepage_after_best_selling_products_title' );
			
			echo $shortcode_content;
			
			do_action( 'nosfir_homepage_after_best_selling_products' );
			
			echo '</section>';
		}
	}
}

if ( ! function_exists( 'nosfir_promoted_products' ) ) {
	/**
	 * Featured and On-Sale Products
	 *
	 * @since  1.0.0
	 * @param integer $per_page total products to display.
	 * @param integer $columns columns to arrange products in to.
	 * @param boolean $recent_fallback Should the function display recent products as a fallback.
	 * @return void
	 */
	function nosfir_promoted_products( $per_page = '4', $columns = '2', $recent_fallback = true ) {
		if ( nosfir_is_woocommerce_activated() ) {

			if ( wc_get_featured_product_ids() ) {

				echo '<h2>' . esc_html__( 'Featured Products', 'nosfir' ) . '</h2>';

				echo nosfir_do_shortcode(
					'featured_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
			} elseif ( wc_get_product_ids_on_sale() ) {

				echo '<h2>' . esc_html__( 'On Sale Now', 'nosfir' ) . '</h2>';

				echo nosfir_do_shortcode(
					'sale_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
			} elseif ( $recent_fallback ) {

				echo '<h2>' . esc_html__( 'New In Store', 'nosfir' ) . '</h2>';

				echo nosfir_do_shortcode(
					'recent_products',
					array(
						'per_page' => $per_page,
						'columns'  => $columns,
					)
				);
			}
		}
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar' ) ) {
	/**
	 * Display a menu intended for use on handheld devices
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar() {
		$links = array(
			'shop'       => array(
				'priority' => 5,
				'callback' => 'nosfir_handheld_footer_bar_shop_link',
			),
			'my-account' => array(
				'priority' => 10,
				'callback' => 'nosfir_handheld_footer_bar_account_link',
			),
			'search'     => array(
				'priority' => 20,
				'callback' => 'nosfir_handheld_footer_bar_search',
			),
			'wishlist'   => array(
				'priority' => 25,
				'callback' => 'nosfir_handheld_footer_bar_wishlist',
			),
			'cart'       => array(
				'priority' => 30,
				'callback' => 'nosfir_handheld_footer_bar_cart_link',
			),
		);

		if ( ! nosfir_is_woocommerce_activated() ) {
			unset( $links['shop'] );
			unset( $links['cart'] );
			unset( $links['wishlist'] );
		}

		if ( wc_get_page_id( 'myaccount' ) === -1 ) {
			unset( $links['my-account'] );
		}

		if ( wc_get_page_id( 'cart' ) === -1 ) {
			unset( $links['cart'] );
		}

		if ( ! get_theme_mod( 'nosfir_wishlist_button', true ) ) {
			unset( $links['wishlist'] );
		}

		$links = apply_filters( 'nosfir_handheld_footer_bar_links', $links );
		?>
		<div class="nosfir-handheld-footer-bar">
			<ul class="columns-<?php echo count( $links ); ?>">
				<?php foreach ( $links as $key => $link ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>">
						<?php
						if ( $link['callback'] ) {
							call_user_func( $link['callback'], $key, $link );
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar_shop_link' ) ) {
	/**
	 * The shop callback function for the handheld footer bar
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar_shop_link() {
		?>
		<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
				<path d="M3 3h14l-1 10H4L3 3zm0 0l-.5-2h15l.5 2M8 18h4"/>
			</svg>
			<span><?php esc_html_e( 'Shop', 'nosfir' ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar_search' ) ) {
	/**
	 * The search callback function for the handheld footer bar
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar_search() {
		?>
		<a href="#" class="mobile-search-trigger">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
				<circle cx="8" cy="8" r="6"/>
				<path d="M14 14l4 4"/>
			</svg>
			<span><?php esc_html_e( 'Search', 'nosfir' ); ?></span>
		</a>
		<div class="mobile-search-form">
			<?php nosfir_product_search(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar_cart_link' ) ) {
	/**
	 * The cart callback function for the handheld footer bar
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar_cart_link() {
		if ( ! nosfir_woo_cart_available() ) {
			return;
		}
		?>
		<a class="footer-cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<?php echo nosfir_get_cart_icon( get_theme_mod( 'nosfir_cart_icon_style', 'outline' ) ); ?>
			<span><?php esc_html_e( 'Cart', 'nosfir' ); ?></span>
			<span class="count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar_wishlist' ) ) {
	/**
	 * The wishlist callback function for the handheld footer bar
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar_wishlist() {
		?>
		<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'wishlist' ) ) ); ?>">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
				<path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
			</svg>
			<span><?php esc_html_e( 'Wishlist', 'nosfir' ); ?></span>
			<span class="wishlist-count"><?php echo esc_html( nosfir_get_wishlist_count() ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_handheld_footer_bar_account_link' ) ) {
	/**
	 * The account callback function for the handheld footer bar
	 *
	 * @since 1.0.0
	 */
	function nosfir_handheld_footer_bar_account_link() {
		?>
		<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
				<circle cx="10" cy="7" r="4"/>
				<path d="M2 19c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
			</svg>
			<span><?php esc_html_e( 'Account', 'nosfir' ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_single_product_pagination' ) ) {
	/**
	 * Single Product Pagination
	 *
	 * @since 1.0.0
	 */
	function nosfir_single_product_pagination() {
		if ( ! get_theme_mod( 'nosfir_product_pagination', true ) ) {
			return;
		}

		$in_same_term   = apply_filters( 'nosfir_single_product_pagination_same_category', true );
		$excluded_terms = apply_filters( 'nosfir_single_product_pagination_excluded_terms', '' );
		$taxonomy       = apply_filters( 'nosfir_single_product_pagination_taxonomy', 'product_cat' );

		$previous_product = nosfir_get_previous_product( $in_same_term, $excluded_terms, $taxonomy );
		$next_product     = nosfir_get_next_product( $in_same_term, $excluded_terms, $taxonomy );

		if ( ! $previous_product && ! $next_product ) {
			return;
		}

		?>
		<nav class="nosfir-product-pagination" aria-label="<?php esc_attr_e( 'More products', 'nosfir' ); ?>">
			<?php if ( $previous_product ) : ?>
				<a href="<?php echo esc_url( $previous_product->get_permalink() ); ?>" rel="prev">
					<span class="nav-direction"><?php esc_html_e( 'Previous', 'nosfir' ); ?></span>
					<?php echo wp_kses_post( $previous_product->get_image( 'thumbnail' ) ); ?>
					<span class="nosfir-product-pagination__title"><?php echo wp_kses_post( $previous_product->get_name() ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $next_product ) : ?>
				<a href="<?php echo esc_url( $next_product->get_permalink() ); ?>" rel="next">
					<span class="nav-direction"><?php esc_html_e( 'Next', 'nosfir' ); ?></span>
					<?php echo wp_kses_post( $next_product->get_image( 'thumbnail' ) ); ?>
					<span class="nosfir-product-pagination__title"><?php echo wp_kses_post( $next_product->get_name() ); ?></span>
				</a>
			<?php endif; ?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'nosfir_sticky_single_add_to_cart' ) ) {
	/**
	 * Sticky Add to Cart
	 *
	 * @since 1.0.0
	 */
	function nosfir_sticky_single_add_to_cart() {
		global $product;

		if ( ! get_theme_mod( 'nosfir_sticky_add_to_cart', true ) ) {
			return;
		}

		if ( ! $product || ! is_product() ) {
			return;
		}

		$show = false;

		if ( $product->is_purchasable() && $product->is_in_stock() ) {
			$show = true;
		} elseif ( $product->is_type( 'external' ) ) {
			$show = true;
		}

		if ( ! $show ) {
			return;
		}

		$params = apply_filters(
			'nosfir_sticky_add_to_cart_params',
			array(
				'trigger_class' => 'entry-summary',
			)
		);

		wp_localize_script( 'nosfir-sticky-add-to-cart', 'nosfir_sticky_add_to_cart_params', $params );
		wp_enqueue_script( 'nosfir-sticky-add-to-cart' );
		?>
		<section class="nosfir-sticky-add-to-cart">
			<div class="container">
				<div class="nosfir-sticky-add-to-cart__content">
					<?php echo wp_kses_post( woocommerce_get_product_thumbnail() ); ?>
					<div class="nosfir-sticky-add-to-cart__content-product-info">
						<span class="nosfir-sticky-add-to-cart__content-title">
							<strong><?php the_title(); ?></strong>
						</span>
						<span class="nosfir-sticky-add-to-cart__content-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
						<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
					</div>
					<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
					   class="nosfir-sticky-add-to-cart__content-button button alt" 
					   rel="nofollow">
						<?php echo esc_attr( $product->add_to_cart_text() ); ?>
					</a>
				</div>
			</div>
		</section>
		<?php
	}
}

if ( ! function_exists( 'nosfir_product_loop_start' ) ) {
	/**
	 * Output the start of a product loop.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function nosfir_product_loop_start( $echo = true ) {
		ob_start();

		wc_set_loop_prop( 'loop', 0 );
		wc_set_loop_prop( 'columns', nosfir_loop_columns() );

		?>
		<ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
		<?php

		$loop_start = apply_filters( 'nosfir_product_loop_start', ob_get_clean() );

		if ( $echo ) {
			echo $loop_start;
		} else {
			return $loop_start;
		}
	}
}

if ( ! function_exists( 'nosfir_product_loop_end' ) ) {
	/**
	 * Output the end of a product loop.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function nosfir_product_loop_end( $echo = true ) {
		ob_start();
		?>
		</ul>
		<?php

		$loop_end = apply_filters( 'nosfir_product_loop_end', ob_get_clean() );

		if ( $echo ) {
			echo $loop_end;
		} else {
			return $loop_end;
		}
	}
}

if ( ! function_exists( 'nosfir_shop_loop_item_title' ) ) {
	/**
	 * Show the product title in the product loop.
	 */
	function nosfir_shop_loop_item_title() {
		echo '<h2 class="' . esc_attr( apply_filters( 'nosfir_shop_loop_item_title_classes', 'woocommerce-loop-product__title' ) ) . '">';
		
		woocommerce_template_loop_product_link_open();
		echo get_the_title();
		woocommerce_template_loop_product_link_close();
		
		echo '</h2>';
	}
}

if ( ! function_exists( 'nosfir_product_thumbnail_in_loop' ) ) {
	/**
	 * Get the product thumbnail for the loop.
	 */
	function nosfir_product_thumbnail_in_loop() {
		echo nosfir_get_product_thumbnail();
	}
}

if ( ! function_exists( 'nosfir_template_loop_product_thumbnail' ) ) {
	/**
	 * Get the product thumbnail, or the placeholder if not set.
	 */
	function nosfir_template_loop_product_thumbnail() {
		global $product;

		$secondary_image_id = '';
		$hover_effect = get_theme_mod( 'nosfir_product_hover_image', true );

		if ( $hover_effect ) {
			$gallery_image_ids = $product->get_gallery_image_ids();
			if ( ! empty( $gallery_image_ids ) ) {
				$secondary_image_id = $gallery_image_ids[0];
			}
		}

		echo '<div class="product-thumbnail-wrapper">';
		echo woocommerce_get_product_thumbnail();

		if ( $secondary_image_id ) {
			echo wp_get_attachment_image(
				$secondary_image_id,
				'woocommerce_thumbnail',
				false,
				array(
					'class' => 'secondary-image',
				)
			);
		}

		echo '</div>';
	}
}