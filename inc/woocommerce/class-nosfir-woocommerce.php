<?php
/**
 * Nosfir WooCommerce Class
 *
 * @package  Nosfir
 * @since    1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nosfir_WooCommerce' ) ) :

	/**
	 * The Nosfir WooCommerce Integration class
	 */
	class Nosfir_WooCommerce {

		/**
		 * Instance
		 *
		 * @var Nosfir_WooCommerce
		 */
		private static $instance = null;

		/**
		 * WooCommerce version
		 *
		 * @var string
		 */
		private $wc_version;

		/**
		 * Get instance
		 *
		 * @return Nosfir_WooCommerce
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->wc_version = defined( 'WC_VERSION' ) ? WC_VERSION : null;

			// Setup
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			
			// Body Classes
			add_filter( 'body_class', array( $this, 'woocommerce_body_class' ) );
			
			// Scripts and Styles
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_scripts' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_integrations_scripts' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 140 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_core_fonts' ), 130 );
			
			// WooCommerce Specific
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
			add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'thumbnail_columns' ) );
			add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimiter' ) );
			add_filter( 'woocommerce_cross_sells_columns', array( $this, 'cross_sells_columns' ) );
			add_filter( 'woocommerce_upsells_columns', array( $this, 'upsell_columns' ) );
			
			// Loop
			add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ) );
			add_filter( 'loop_shop_columns', array( $this, 'loop_columns' ) );
			
			// Product Gallery
			add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'gallery_classes' ) );
			
			// AJAX
			add_action( 'wp_ajax_nosfir_quick_view', array( $this, 'quick_view_ajax' ) );
			add_action( 'wp_ajax_nopriv_nosfir_quick_view', array( $this, 'quick_view_ajax' ) );
			add_action( 'wp_ajax_nosfir_add_to_wishlist', array( $this, 'add_to_wishlist_ajax' ) );
			add_action( 'wp_ajax_nopriv_nosfir_add_to_wishlist', array( $this, 'add_to_wishlist_ajax' ) );
			
			// Checkout
			add_filter( 'woocommerce_checkout_fields', array( $this, 'override_checkout_fields' ) );
			add_filter( 'woocommerce_enable_order_notes_field', array( $this, 'enable_order_notes' ) );
			
			// Cart
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_fragments' ) );
			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 10, 3 );
			
			// Integrations
			add_action( 'nosfir_woocommerce_setup', array( $this, 'setup_integrations' ) );
			
			// Instead of loading Core CSS files, we only register the font families
			add_filter( 'woocommerce_enqueue_styles', array( $this, 'dequeue_styles' ) );
			
			// Product Actions
			add_action( 'init', array( $this, 'remove_default_woocommerce_hooks' ) );
			add_action( 'init', array( $this, 'add_custom_woocommerce_hooks' ) );
			
			// Admin
			add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );
		}

		/**
		 * Sets up theme defaults and registers support for various WooCommerce features.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function setup() {
			// WooCommerce Support
			add_theme_support(
				'woocommerce',
				apply_filters(
					'nosfir_woocommerce_args',
					array(
						'single_image_width'    => 600,
						'thumbnail_image_width' => 400,
						'gallery_thumbnail_image_width' => 100,
						'product_grid'          => array(
							'default_columns' => 4,
							'default_rows'    => 3,
							'min_columns'     => 2,
							'max_columns'     => 6,
							'min_rows'        => 1,
						),
					)
				)
			);

			// Product Gallery Features
			if ( get_theme_mod( 'nosfir_product_zoom', true ) ) {
				add_theme_support( 'wc-product-gallery-zoom' );
			}
			
			add_theme_support( 'wc-product-gallery-lightbox' );
			
			if ( get_theme_mod( 'nosfir_product_gallery_slider', true ) ) {
				add_theme_support( 'wc-product-gallery-slider' );
			}

			// Wide Align Support
			add_theme_support( 'woocommerce', array(
				'thumbnail_image_width' => 300,
				'single_image_width'    => 600,
			) );

			/**
			 * Add 'nosfir_woocommerce_setup' action
			 *
			 * @since  1.0.0
			 */
			do_action( 'nosfir_woocommerce_setup' );
		}

		/**
		 * Remove default WooCommerce hooks
		 *
		 * @since 1.0.0
		 */
		public function remove_default_woocommerce_hooks() {
			// Remove default wrappers
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
			
			// Customize product loop
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			
			// Reposition breadcrumbs
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}

		/**
		 * Add custom WooCommerce hooks
		 *
		 * @since 1.0.0
		 */
		public function add_custom_woocommerce_hooks() {
			// Add custom wrappers
			add_action( 'woocommerce_before_main_content', array( $this, 'wrapper_start' ), 10 );
			add_action( 'woocommerce_after_main_content', array( $this, 'wrapper_end' ), 10 );
			
			// Reposition elements
			add_action( 'nosfir_before_content', 'woocommerce_breadcrumb', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_labels' ), 9 );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
			
			// Add custom elements
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_quick_view_button' ), 15 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_wishlist_button' ), 20 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_compare_button' ), 25 );
			
			// Product page
			if ( get_theme_mod( 'nosfir_sticky_add_to_cart', true ) ) {
				add_action( 'wp_footer', array( $this, 'sticky_add_to_cart' ) );
			}
			
			if ( get_theme_mod( 'nosfir_product_pagination', true ) ) {
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'product_navigation' ), 5 );
			}
		}

		/**
		 * Start the page wrapper
		 */
		public function wrapper_start() {
			?>
			<div id="primary" class="content-area">
				<main id="main" class="site-main">
			<?php
		}

		/**
		 * End the page wrapper
		 */
		public function wrapper_end() {
			?>
				</main><!-- #main -->
			</div><!-- #primary -->
			<?php
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_customizer_css() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}
			wp_add_inline_style( 'nosfir-woocommerce-style', $this->get_woocommerce_extension_css() );
		}

		/**
		 * Add CSS in <head> to register WooCommerce Core fonts
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_core_fonts() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}
			
			$fonts_url = plugins_url( '/woocommerce/assets/fonts/' );
			wp_add_inline_style(
				'nosfir-woocommerce-style',
				'@font-face {
					font-family: star;
					src: url(' . $fonts_url . 'star.eot);
					src:
						url(' . $fonts_url . 'star.eot?#iefix) format("embedded-opentype"),
						url(' . $fonts_url . 'star.woff) format("woff"),
						url(' . $fonts_url . 'star.ttf) format("truetype"),
						url(' . $fonts_url . 'star.svg#star) format("svg");
					font-weight: 400;
					font-style: normal;
				}
				@font-face {
					font-family: WooCommerce;
					src: url(' . $fonts_url . 'WooCommerce.eot);
					src:
						url(' . $fonts_url . 'WooCommerce.eot?#iefix) format("embedded-opentype"),
						url(' . $fonts_url . 'WooCommerce.woff) format("woff"),
						url(' . $fonts_url . 'WooCommerce.ttf) format("truetype"),
						url(' . $fonts_url . 'WooCommerce.svg#WooCommerce) format("svg");
					font-weight: 400;
					font-style: normal;
				}'
			);
		}

		/**
		 * Add WooCommerce specific classes to the body tag
		 *
		 * @param  array $classes css classes applied to the body tag
		 * @return array $classes modified to include 'woocommerce-active' class
		 */
		public function woocommerce_body_class( $classes ) {
			if ( class_exists( 'WooCommerce' ) ) {
				$classes[] = 'woocommerce-active';
				
				// Add class for sidebar position
				if ( is_shop() || is_product_category() || is_product_tag() ) {
					$sidebar_position = get_theme_mod( 'nosfir_shop_sidebar_position', 'right' );
					$classes[] = 'shop-sidebar-' . $sidebar_position;
				}
				
				if ( is_product() ) {
					$sidebar_position = get_theme_mod( 'nosfir_single_sidebar_position', 'right' );
					$classes[] = 'product-sidebar-' . $sidebar_position;
				}
				
				// Add columns class
				$columns = $this->loop_columns();
				$classes[] = 'columns-' . $columns;
			}

			// Remove `no-wc-breadcrumb` body class
			$key = array_search( 'no-wc-breadcrumb', $classes, true );
			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}

			return $classes;
		}

		/**
		 * WooCommerce specific scripts & stylesheets
		 *
		 * @since 1.0.0
		 */
		public function woocommerce_scripts() {
			global $nosfir_version;
			
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Styles
			wp_enqueue_style( 
				'nosfir-woocommerce-style', 
				get_template_directory_uri() . '/assets/css/woocommerce/woocommerce.css', 
				array( 'nosfir-style' ), 
				$nosfir_version 
			);
			wp_style_add_data( 'nosfir-woocommerce-style', 'rtl', 'replace' );

			// Scripts
			wp_register_script( 
				'nosfir-woocommerce', 
				get_template_directory_uri() . '/assets/js/woocommerce/woocommerce' . $suffix . '.js', 
				array( 'jquery' ), 
				$nosfir_version, 
				true 
			);
			
			wp_enqueue_script( 'nosfir-woocommerce' );
			
			// Localize script
			wp_localize_script( 'nosfir-woocommerce', 'nosfir_wc_params', array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'nosfir-woocommerce' ),
				'is_product'   => is_product(),
				'is_cart'      => is_cart(),
				'is_checkout'  => is_checkout(),
				'quick_view'   => get_theme_mod( 'nosfir_quick_view', true ),
				'wishlist'     => get_theme_mod( 'nosfir_wishlist_button', true ),
				'compare'      => get_theme_mod( 'nosfir_compare_button', false ),
				'sticky_cart'  => get_theme_mod( 'nosfir_sticky_add_to_cart', true ),
				'ajax_cart'    => get_theme_mod( 'nosfir_ajax_add_to_cart', true ),
				'mini_cart'    => get_theme_mod( 'nosfir_mini_cart_style', 'dropdown' ),
			) );

			// Header cart
			wp_register_script( 
				'nosfir-header-cart', 
				get_template_directory_uri() . '/assets/js/woocommerce/header-cart' . $suffix . '.js', 
				array( 'jquery' ), 
				$nosfir_version, 
				true 
			);
			wp_enqueue_script( 'nosfir-header-cart' );

			// Quick View
			if ( get_theme_mod( 'nosfir_quick_view', true ) ) {
				wp_enqueue_script( 
					'nosfir-quick-view', 
					get_template_directory_uri() . '/assets/js/woocommerce/quick-view' . $suffix . '.js', 
					array( 'jquery', 'wc-add-to-cart-variation' ), 
					$nosfir_version, 
					true 
				);
			}

			// Sticky Add to Cart
			if ( is_product() && get_theme_mod( 'nosfir_sticky_add_to_cart', true ) ) {
				wp_enqueue_script( 
					'nosfir-sticky-add-to-cart', 
					get_template_directory_uri() . '/assets/js/woocommerce/sticky-add-to-cart' . $suffix . '.js', 
					array( 'jquery' ), 
					$nosfir_version, 
					true 
				);
			}

			// Wishlist
			if ( get_theme_mod( 'nosfir_wishlist_button', true ) ) {
				wp_enqueue_script( 
					'nosfir-wishlist', 
					get_template_directory_uri() . '/assets/js/woocommerce/wishlist' . $suffix . '.js', 
					array( 'jquery' ), 
					$nosfir_version, 
					true 
				);
			}
		}

		/**
		 * Dequeue WooCommerce styles
		 *
		 * @param  array $enqueue_styles
		 * @return array
		 */
		public function dequeue_styles( $enqueue_styles ) {
			if ( get_theme_mod( 'nosfir_disable_wc_styles', false ) ) {
				return array();
			}
			
			// Remove default WooCommerce styles selectively
			unset( $enqueue_styles['woocommerce-layout'] );
			unset( $enqueue_styles['woocommerce-smallscreen'] );
			
			return $enqueue_styles;
		}

		/**
		 * Related Products Args
		 *
		 * @param  array $args related products args
		 * @since 1.0.0
		 * @return array $args related products args
		 */
		public function related_products_args( $args ) {
			$columns = get_theme_mod( 'nosfir_related_products_columns', 4 );
			$posts_per_page = get_theme_mod( 'nosfir_related_products_count', 4 );
			
			$args = apply_filters(
				'nosfir_related_products_args',
				array(
					'posts_per_page' => $posts_per_page,
					'columns'        => $columns,
				)
			);

			return $args;
		}

		/**
		 * Product gallery thumbnail columns
		 *
		 * @return integer number of columns
		 * @since  1.0.0
		 */
		public function thumbnail_columns() {
			$columns = 4;

			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$columns = 5;
			}

			return intval( apply_filters( 'nosfir_product_thumbnail_columns', $columns ) );
		}

		/**
		 * Products per page
		 *
		 * @return integer number of products
		 * @since  1.0.0
		 */
		public function products_per_page() {
			return intval( get_theme_mod( 'nosfir_products_per_page', 12 ) );
		}

		/**
		 * Product columns
		 *
		 * @return integer number of columns
		 * @since  1.0.0
		 */
		public function loop_columns() {
			$columns = get_theme_mod( 'nosfir_products_per_row', 4 );

			if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
				$columns = $columns + 1;
			}

			return intval( apply_filters( 'nosfir_loop_columns', $columns ) );
		}

		/**
		 * Cross sell columns
		 *
		 * @return integer number of columns
		 */
		public function cross_sells_columns() {
			return intval( apply_filters( 'nosfir_cross_sells_columns', 4 ) );
		}

		/**
		 * Upsell columns
		 *
		 * @return integer number of columns
		 */
		public function upsell_columns() {
			return intval( apply_filters( 'nosfir_upsell_columns', 4 ) );
		}

		/**
		 * Gallery classes
		 *
		 * @param array $classes
		 * @return array
		 */
		public function gallery_classes( $classes ) {
			$classes[] = 'nosfir-product-gallery';
			return $classes;
		}

		/**
		 * Query WooCommerce Extension Activation
		 *
		 * @param string $extension Extension class name
		 * @return boolean
		 */
		public function is_woocommerce_extension_activated( $extension = 'WC_Bookings' ) {
			return class_exists( $extension ) ? true : false;
		}

		/**
		 * Remove the breadcrumb delimiter
		 *
		 * @param  array $defaults The breadcrumb defaults
		 * @return array           The breadcrumb defaults
		 * @since 1.0.0
		 */
		public function change_breadcrumb_delimiter( $defaults ) {
			$defaults['delimiter']   = '<span class="breadcrumb-separator">/</span>';
			$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumbs', 'nosfir' ) . '">';
			$defaults['wrap_after']  = '</nav>';
			$defaults['home']        = esc_html__( 'Home', 'nosfir' );
			
			return $defaults;
		}

		/**
		 * Override checkout fields
		 *
		 * @param array $fields
		 * @return array
		 */
		public function override_checkout_fields( $fields ) {
			// Add placeholders
			if ( get_theme_mod( 'nosfir_checkout_placeholders', true ) ) {
				foreach ( $fields as $fieldset_key => $fieldset ) {
					foreach ( $fieldset as $field_key => $field ) {
						if ( isset( $field['label'] ) ) {
							$fields[ $fieldset_key ][ $field_key ]['placeholder'] = $field['label'];
						}
					}
				}
			}
			
			return $fields;
		}

		/**
		 * Enable order notes
		 *
		 * @return boolean
		 */
		public function enable_order_notes() {
			return get_theme_mod( 'nosfir_order_notes', true );
		}

		/**
		 * Cart Fragments
		 *
		 * @param array $fragments
		 * @return array
		 */
		public function cart_fragments( $fragments ) {
			ob_start();
			nosfir_cart_link();
			$fragments['a.cart-contents'] = ob_get_clean();

			ob_start();
			?>
			<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			<?php
			$fragments['.cart-count'] = ob_get_clean();

			return $fragments;
		}

		/**
		 * Cart item thumbnail
		 *
		 * @param string $thumbnail
		 * @param array $cart_item
		 * @param string $cart_item_key
		 * @return string
		 */
		public function cart_item_thumbnail( $thumbnail, $cart_item, $cart_item_key ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );
			}
			
			return $thumbnail;
		}

		/**
		 * Product labels
		 */
		public function product_labels() {
			global $product;
			
			if ( ! $product ) {
				return;
			}
			
			echo '<div class="product-labels">';
			
			// Sale badge
			if ( $product->is_on_sale() ) {
				$badge_type = get_theme_mod( 'nosfir_sale_badge_type', 'percentage' );
				
				switch ( $badge_type ) {
					case 'percentage':
						if ( $product->is_type( 'variable' ) ) {
							$percentages = array();
							foreach ( $product->get_visible_children() as $child_id ) {
								$variation = wc_get_product( $child_id );
								$regular = (float) $variation->get_regular_price();
								$sale = (float) $variation->get_sale_price();
								
								if ( $sale != 0 && ! empty( $regular ) ) {
									$percentages[] = round( 100 - ( $sale / $regular * 100 ) );
								}
							}
							if ( ! empty( $percentages ) ) {
								$percentage = max( $percentages );
								echo '<span class="onsale">-' . $percentage . '%</span>';
							}
						} else {
							$regular = (float) $product->get_regular_price();
							$sale = (float) $product->get_sale_price();
							
							if ( $sale != 0 && ! empty( $regular ) ) {
								$percentage = round( 100 - ( $sale / $regular * 100 ) );
								echo '<span class="onsale">-' . $percentage . '%</span>';
							}
						}
						break;
						
					case 'amount':
						if ( $product->is_type( 'variable' ) ) {
							$amounts = array();
							foreach ( $product->get_visible_children() as $child_id ) {
								$variation = wc_get_product( $child_id );
								$regular = (float) $variation->get_regular_price();
								$sale = (float) $variation->get_sale_price();
								
								if ( $sale != 0 && ! empty( $regular ) ) {
									$amounts[] = $regular - $sale;
								}
							}
							if ( ! empty( $amounts ) ) {
								$amount = max( $amounts );
								echo '<span class="onsale">-' . wc_price( $amount ) . '</span>';
							}
						} else {
							$regular = (float) $product->get_regular_price();
							$sale = (float) $product->get_sale_price();
							
							if ( $sale != 0 && ! empty( $regular ) ) {
								$amount = $regular - $sale;
								echo '<span class="onsale">-' . wc_price( $amount ) . '</span>';
							}
						}
						break;
						
					default:
						echo '<span class="onsale">' . esc_html__( 'Sale!', 'nosfir' ) . '</span>';
				}
			}
			
			// New badge
			$newness_days = get_theme_mod( 'nosfir_new_badge_days', 30 );
			$created = strtotime( $product->get_date_created() );
			if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
				echo '<span class="new-badge">' . esc_html__( 'New', 'nosfir' ) . '</span>';
			}
			
			// Featured badge
			if ( $product->is_featured() ) {
				echo '<span class="featured-badge">' . esc_html__( 'Featured', 'nosfir' ) . '</span>';
			}
			
			// Out of stock
			if ( ! $product->is_in_stock() ) {
				echo '<span class="out-of-stock-badge">' . esc_html__( 'Out of Stock', 'nosfir' ) . '</span>';
			}
			
			echo '</div>';
		}

		/**
		 * Add Quick View button
		 */
		public function add_quick_view_button() {
			if ( ! get_theme_mod( 'nosfir_quick_view', true ) ) {
				return;
			}
			
			global $product;
			
			echo '<a href="#" class="button nosfir-quick-view-btn" data-product-id="' . esc_attr( $product->get_id() ) . '">';
			echo '<span class="dashicons dashicons-search"></span>';
			echo esc_html__( 'Quick View', 'nosfir' );
			echo '</a>';
		}

		/**
		 * Add Wishlist button
		 */
		public function add_wishlist_button() {
			if ( ! get_theme_mod( 'nosfir_wishlist_button', true ) ) {
				return;
			}
			
			global $product;
			
			$wishlisted = $this->is_in_wishlist( $product->get_id() );
			$class = $wishlisted ? 'added' : '';
			
			echo '<a href="#" class="button nosfir-wishlist-btn ' . esc_attr( $class ) . '" data-product-id="' . esc_attr( $product->get_id() ) . '">';
			echo '<span class="dashicons dashicons-heart"></span>';
			echo $wishlisted ? esc_html__( 'In Wishlist', 'nosfir' ) : esc_html__( 'Add to Wishlist', 'nosfir' );
			echo '</a>';
		}

		/**
		 * Add Compare button
		 */
		public function add_compare_button() {
			if ( ! get_theme_mod( 'nosfir_compare_button', false ) ) {
				return;
			}
			
			global $product;
			
			echo '<a href="#" class="button nosfir-compare-btn" data-product-id="' . esc_attr( $product->get_id() ) . '">';
			echo '<span class="dashicons dashicons-editor-alignleft"></span>';
			echo esc_html__( 'Compare', 'nosfir' );
			echo '</a>';
		}

		/**
		 * Quick View AJAX handler
		 */
		public function quick_view_ajax() {
			if ( ! isset( $_POST['product_id'] ) ) {
				wp_die();
			}
			
			$product_id = intval( $_POST['product_id'] );
			
			// Set the main query to use our product
			wp( 'p=' . $product_id . '&post_type=product' );
			
			ob_start();
			
			// Load quickview template
			while ( have_posts() ) : 
				the_post();
				wc_get_template_part( 'content', 'single-product-quick-view' );
			endwhile;
			
			echo ob_get_clean();
			
			wp_die();
		}

		/**
		 * Add to Wishlist AJAX handler
		 */
		public function add_to_wishlist_ajax() {
			if ( ! isset( $_POST['product_id'] ) ) {
				wp_die();
			}
			
			$product_id = intval( $_POST['product_id'] );
			$user_id = get_current_user_id();
			
			if ( $user_id > 0 ) {
				$wishlist = get_user_meta( $user_id, 'nosfir_wishlist', true );
				if ( ! is_array( $wishlist ) ) {
					$wishlist = array();
				}
				
				if ( in_array( $product_id, $wishlist ) ) {
					// Remove from wishlist
					$wishlist = array_diff( $wishlist, array( $product_id ) );
					$action = 'removed';
				} else {
					// Add to wishlist
					$wishlist[] = $product_id;
					$action = 'added';
				}
				
				update_user_meta( $user_id, 'nosfir_wishlist', $wishlist );
			} else {
				// Handle guest wishlist with session
				if ( ! session_id() ) {
					session_start();
				}
				
				if ( ! isset( $_SESSION['nosfir_wishlist'] ) ) {
					$_SESSION['nosfir_wishlist'] = array();
				}
				
				if ( in_array( $product_id, $_SESSION['nosfir_wishlist'] ) ) {
					$_SESSION['nosfir_wishlist'] = array_diff( $_SESSION['nosfir_wishlist'], array( $product_id ) );
					$action = 'removed';
				} else {
					$_SESSION['nosfir_wishlist'][] = $product_id;
					$action = 'added';
				}
			}
			
			wp_send_json_success( array(
				'action' => $action,
				'message' => $action === 'added' ? 
					__( 'Product added to wishlist', 'nosfir' ) : 
					__( 'Product removed from wishlist', 'nosfir' )
			) );
		}

		/**
		 * Check if product is in wishlist
		 *
		 * @param int $product_id
		 * @return boolean
		 */
		private function is_in_wishlist( $product_id ) {
			$user_id = get_current_user_id();
			
			if ( $user_id > 0 ) {
				$wishlist = get_user_meta( $user_id, 'nosfir_wishlist', true );
				if ( is_array( $wishlist ) && in_array( $product_id, $wishlist ) ) {
					return true;
				}
			} else {
				if ( ! session_id() ) {
					session_start();
				}
				
				if ( isset( $_SESSION['nosfir_wishlist'] ) && in_array( $product_id, $_SESSION['nosfir_wishlist'] ) ) {
					return true;
				}
			}
			
			return false;
		}

		/**
		 * Sticky Add to Cart
		 */
		public function sticky_add_to_cart() {
			if ( ! is_product() || ! get_theme_mod( 'nosfir_sticky_add_to_cart', true ) ) {
				return;
			}
			
			global $product;
			?>
			<div class="nosfir-sticky-add-to-cart">
				<div class="container">
					<div class="sticky-product-info">
						<?php echo $product->get_image( 'woocommerce_gallery_thumbnail' ); ?>
						<div class="product-details">
							<h3><?php echo get_the_title(); ?></h3>
							<p class="price"><?php echo $product->get_price_html(); ?></p>
						</div>
					</div>
					<div class="sticky-add-to-cart-form">
						<?php woocommerce_template_single_add_to_cart(); ?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Product Navigation
		 */
		public function product_navigation() {
			if ( ! get_theme_mod( 'nosfir_product_pagination', true ) ) {
				return;
			}
			?>
			<nav class="nosfir-product-navigation">
				<div class="nav-links">
					<div class="nav-previous">
						<?php previous_post_link( '%link', '<span class="meta-nav">' . esc_html__( 'Previous', 'nosfir' ) . '</span> %title' ); ?>
					</div>
					<div class="nav-next">
						<?php next_post_link( '%link', '<span class="meta-nav">' . esc_html__( 'Next', 'nosfir' ) . '</span> %title' ); ?>
					</div>
				</div>
			</nav>
			<?php
		}

		/**
		 * Integration Styles & Scripts
		 *
		 * @return void
		 */
		public function woocommerce_integrations_scripts() {
			global $nosfir_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// YITH Wishlist
			if ( $this->is_woocommerce_extension_activated( 'YITH_WCWL' ) ) {
				wp_enqueue_style( 
					'nosfir-yith-wishlist', 
					get_template_directory_uri() . '/assets/css/woocommerce/extensions/yith-wishlist.css', 
					array( 'nosfir-woocommerce-style' ), 
					$nosfir_version 
				);
			}

			// YITH Quick View
			if ( $this->is_woocommerce_extension_activated( 'YITH_WCQV' ) ) {
				wp_enqueue_style( 
					'nosfir-yith-quick-view', 
					get_template_directory_uri() . '/assets/css/woocommerce/extensions/yith-quick-view.css', 
					array( 'nosfir-woocommerce-style' ), 
					$nosfir_version 
				);
			}

			// Product Add-ons
			if ( $this->is_woocommerce_extension_activated( 'WC_Product_Addons' ) ) {
				wp_enqueue_style( 
					'nosfir-product-addons', 
					get_template_directory_uri() . '/assets/css/woocommerce/extensions/product-addons.css', 
					array( 'nosfir-woocommerce-style' ), 
					$nosfir_version 
				);
			}

			// WooCommerce Subscriptions
			if ( $this->is_woocommerce_extension_activated( 'WC_Subscriptions' ) ) {
				wp_enqueue_style( 
					'nosfir-subscriptions', 
					get_template_directory_uri() . '/assets/css/woocommerce/extensions/subscriptions.css', 
					array( 'nosfir-woocommerce-style' ), 
					$nosfir_version 
				);
			}
		}

		/**
		 * Get extension css
		 *
		 * @return string $styles the css
		 */
		public function get_woocommerce_extension_css() {
			$styles = '';

			// Add extension specific styles here
			
			return apply_filters( 'nosfir_woocommerce_extension_css', $styles );
		}

		/**
		 * Sets up integrations
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function setup_integrations() {
			// WooCommerce Bundles
			if ( $this->is_woocommerce_extension_activated( 'WC_Bundles' ) ) {
				add_filter( 'woocommerce_bundled_table_item_js_enqueued', '__return_true' );
			}

			// WooCommerce Composite Products
			if ( $this->is_woocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				add_filter( 'woocommerce_composited_table_item_js_enqueued', '__return_true' );
			}
		}

		/**
		 * Admin notice for WooCommerce requirement
		 */
		public function woocommerce_notice() {
			if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'activate_plugins' ) ) {
				?>
				<div class="notice notice-warning is-dismissible">
					<p>
						<?php
						printf(
							/* translators: %s: WooCommerce plugin URL */
							esc_html__( 'Nosfir theme recommends WooCommerce plugin. Please %s to take advantage of all theme features.', 'nosfir' ),
							'<a href="' . esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ) . '">' . esc_html__( 'install WooCommerce', 'nosfir' ) . '</a>'
						);
						?>
					</p>
				</div>
				<?php
			}
		}
	}

endif;

// Initialize the class
Nosfir_WooCommerce::get_instance();