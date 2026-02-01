<?php
/**
 * Nosfir WooCommerce Customizer Class
 *
 * @package  Nosfir
 * @since    1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nosfir_WooCommerce_Customizer' ) ) :

	/**
	 * The Nosfir WooCommerce Customizer class
	 */
	class Nosfir_WooCommerce_Customizer {

		/**
		 * Instance
		 *
		 * @var Nosfir_WooCommerce_Customizer
		 */
		private static $instance = null;

		/**
		 * Get instance
		 *
		 * @return Nosfir_WooCommerce_Customizer
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
			add_action( 'customize_register', array( $this, 'customize_register' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 130 );
			add_filter( 'nosfir_setting_default_values', array( $this, 'setting_default_values' ) );
			add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
		}

		/**
		 * Returns an array of the desired default Nosfir Options
		 *
		 * @param array $defaults array of default options.
		 * @since 1.0.0
		 * @return array
		 */
		public function setting_default_values( $defaults = array() ) {
			$defaults['nosfir_sticky_add_to_cart']     = true;
			$defaults['nosfir_product_pagination']     = true;
			$defaults['nosfir_quick_view']             = true;
			$defaults['nosfir_product_zoom']           = true;
			$defaults['nosfir_product_gallery_slider'] = true;
			$defaults['nosfir_ajax_add_to_cart']       = true;
			$defaults['nosfir_wishlist_button']        = true;
			$defaults['nosfir_compare_button']         = false;
			$defaults['nosfir_sale_badge_type']        = 'percentage';
			$defaults['nosfir_products_per_row']       = 4;
			$defaults['nosfir_products_per_page']      = 12;
			$defaults['nosfir_shop_sidebar_position']  = 'right';
			$defaults['nosfir_single_sidebar_position'] = 'right';
			$defaults['nosfir_cart_icon_style']        = 'outline';
			$defaults['nosfir_show_cart_count']        = true;
			$defaults['nosfir_mini_cart_style']        = 'dropdown';

			return $defaults;
		}

		/**
		 * Add postMessage support and register WooCommerce settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {

			/**
			 * WooCommerce Panel
			 */
			if ( ! isset( $wp_customize->panels['woocommerce'] ) ) {
				$wp_customize->add_panel(
					'woocommerce',
					array(
						'title'    => __( 'WooCommerce', 'nosfir' ),
						'priority' => 20,
					)
				);
			}

			/**
			 * Shop Page Section
			 */
			$wp_customize->add_section(
				'nosfir_shop_page',
				array(
					'title'    => __( 'Shop Page', 'nosfir' ),
					'priority' => 10,
					'panel'    => 'woocommerce',
				)
			);

			// Products per row
			$wp_customize->add_setting(
				'nosfir_products_per_row',
				array(
					'default'           => 4,
					'sanitize_callback' => 'absint',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_products_per_row',
				array(
					'type'        => 'number',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Products per Row', 'nosfir' ),
					'description' => __( 'How many products should be shown per row on shop pages?', 'nosfir' ),
					'input_attrs' => array(
						'min'  => 2,
						'max'  => 6,
						'step' => 1,
					),
					'priority'    => 10,
				)
			);

			// Products per page
			$wp_customize->add_setting(
				'nosfir_products_per_page',
				array(
					'default'           => 12,
					'sanitize_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'nosfir_products_per_page',
				array(
					'type'        => 'number',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Products per Page', 'nosfir' ),
					'description' => __( 'How many products should be shown per page?', 'nosfir' ),
					'input_attrs' => array(
						'min'  => 1,
						'max'  => 48,
						'step' => 1,
					),
					'priority'    => 20,
				)
			);

			// Shop sidebar position
			$wp_customize->add_setting(
				'nosfir_shop_sidebar_position',
				array(
					'default'           => 'right',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_shop_sidebar_position',
				array(
					'type'     => 'select',
					'section'  => 'nosfir_shop_page',
					'label'    => __( 'Shop Sidebar Position', 'nosfir' ),
					'choices'  => array(
						'none'  => __( 'No Sidebar', 'nosfir' ),
						'left'  => __( 'Left Sidebar', 'nosfir' ),
						'right' => __( 'Right Sidebar', 'nosfir' ),
					),
					'priority' => 30,
				)
			);

			// AJAX Add to Cart
			$wp_customize->add_setting(
				'nosfir_ajax_add_to_cart',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_ajax_add_to_cart',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Enable AJAX Add to Cart', 'nosfir' ),
					'description' => __( 'Add products to cart without page reload.', 'nosfir' ),
					'priority'    => 40,
				)
			);

			// Quick View
			$wp_customize->add_setting(
				'nosfir_quick_view',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_quick_view',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Enable Quick View', 'nosfir' ),
					'description' => __( 'Allow customers to quickly view product details in a modal.', 'nosfir' ),
					'priority'    => 50,
				)
			);

			// Wishlist Button
			$wp_customize->add_setting(
				'nosfir_wishlist_button',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_wishlist_button',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Show Wishlist Button', 'nosfir' ),
					'description' => __( 'Display wishlist button on product cards.', 'nosfir' ),
					'priority'    => 60,
				)
			);

			// Compare Button
			$wp_customize->add_setting(
				'nosfir_compare_button',
				array(
					'default'           => false,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_compare_button',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_shop_page',
					'label'       => __( 'Show Compare Button', 'nosfir' ),
					'description' => __( 'Display compare button on product cards.', 'nosfir' ),
					'priority'    => 70,
				)
			);

			// Sale Badge Type
			$wp_customize->add_setting(
				'nosfir_sale_badge_type',
				array(
					'default'           => 'percentage',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_sale_badge_type',
				array(
					'type'     => 'select',
					'section'  => 'nosfir_shop_page',
					'label'    => __( 'Sale Badge Style', 'nosfir' ),
					'choices'  => array(
						'text'       => __( 'Sale Text', 'nosfir' ),
						'percentage' => __( 'Percentage Off', 'nosfir' ),
						'amount'     => __( 'Amount Saved', 'nosfir' ),
					),
					'priority' => 80,
				)
			);

			/**
			 * Product Page Section
			 */
			$wp_customize->add_section(
				'nosfir_single_product_page',
				array(
					'title'    => __( 'Product Page', 'nosfir' ),
					'priority' => 20,
					'panel'    => 'woocommerce',
				)
			);

			// Product Pagination
			$wp_customize->add_setting(
				'nosfir_product_pagination',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_product_pagination',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_single_product_page',
					'label'       => __( 'Product Navigation', 'nosfir' ),
					'description' => __( 'Display next/previous product navigation.', 'nosfir' ),
					'priority'    => 10,
				)
			);

			// Sticky Add to Cart
			$wp_customize->add_setting(
				'nosfir_sticky_add_to_cart',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_sticky_add_to_cart',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_single_product_page',
					'label'       => __( 'Sticky Add to Cart', 'nosfir' ),
					'description' => __( 'Show sticky add to cart bar when scrolling.', 'nosfir' ),
					'priority'    => 20,
				)
			);

			// Product Zoom
			$wp_customize->add_setting(
				'nosfir_product_zoom',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_product_zoom',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_single_product_page',
					'label'       => __( 'Product Image Zoom', 'nosfir' ),
					'description' => __( 'Enable zoom on product images.', 'nosfir' ),
					'priority'    => 30,
				)
			);

			// Product Gallery Slider
			$wp_customize->add_setting(
				'nosfir_product_gallery_slider',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);

			$wp_customize->add_control(
				'nosfir_product_gallery_slider',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_single_product_page',
					'label'       => __( 'Gallery Slider', 'nosfir' ),
					'description' => __( 'Enable slider for product gallery.', 'nosfir' ),
					'priority'    => 40,
				)
			);

			// Single Product Sidebar
			$wp_customize->add_setting(
				'nosfir_single_sidebar_position',
				array(
					'default'           => 'right',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_single_sidebar_position',
				array(
					'type'     => 'select',
					'section'  => 'nosfir_single_product_page',
					'label'    => __( 'Product Page Sidebar', 'nosfir' ),
					'choices'  => array(
						'none'  => __( 'No Sidebar', 'nosfir' ),
						'left'  => __( 'Left Sidebar', 'nosfir' ),
						'right' => __( 'Right Sidebar', 'nosfir' ),
					),
					'priority' => 50,
				)
			);

			/**
			 * Cart & Checkout Section
			 */
			$wp_customize->add_section(
				'nosfir_cart_checkout',
				array(
					'title'    => __( 'Cart & Checkout', 'nosfir' ),
					'priority' => 30,
					'panel'    => 'woocommerce',
				)
			);

			// Cart Icon Style
			$wp_customize->add_setting(
				'nosfir_cart_icon_style',
				array(
					'default'           => 'outline',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_cart_icon_style',
				array(
					'type'     => 'select',
					'section'  => 'nosfir_cart_checkout',
					'label'    => __( 'Cart Icon Style', 'nosfir' ),
					'choices'  => array(
						'outline' => __( 'Outline', 'nosfir' ),
						'filled'  => __( 'Filled', 'nosfir' ),
						'bag'     => __( 'Shopping Bag', 'nosfir' ),
						'basket'  => __( 'Basket', 'nosfir' ),
					),
					'priority' => 10,
				)
			);

			// Show Cart Count
			$wp_customize->add_setting(
				'nosfir_show_cart_count',
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'nosfir_show_cart_count',
				array(
					'type'        => 'checkbox',
					'section'     => 'nosfir_cart_checkout',
					'label'       => __( 'Show Cart Item Count', 'nosfir' ),
					'description' => __( 'Display number of items in cart.', 'nosfir' ),
					'priority'    => 20,
				)
			);

			// Mini Cart Style
			$wp_customize->add_setting(
				'nosfir_mini_cart_style',
				array(
					'default'           => 'dropdown',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				'nosfir_mini_cart_style',
				array(
					'type'     => 'select',
					'section'  => 'nosfir_cart_checkout',
					'label'    => __( 'Mini Cart Style', 'nosfir' ),
					'choices'  => array(
						'dropdown' => __( 'Dropdown', 'nosfir' ),
						'sidebar'  => __( 'Sidebar Slide', 'nosfir' ),
						'modal'    => __( 'Modal Popup', 'nosfir' ),
					),
					'priority' => 30,
				)
			);

			/**
			 * WooCommerce Colors Section
			 */
			$wp_customize->add_section(
				'nosfir_woocommerce_colors',
				array(
					'title'    => __( 'Store Colors', 'nosfir' ),
					'priority' => 40,
					'panel'    => 'woocommerce',
				)
			);

			// Sale Badge Color
			$wp_customize->add_setting(
				'nosfir_sale_badge_color',
				array(
					'default'           => '#ff0000',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'nosfir_sale_badge_color',
					array(
						'label'    => __( 'Sale Badge Color', 'nosfir' ),
						'section'  => 'nosfir_woocommerce_colors',
						'priority' => 10,
					)
				)
			);

			// Add to Cart Button Color
			$wp_customize->add_setting(
				'nosfir_add_to_cart_color',
				array(
					'default'           => '#333333',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'nosfir_add_to_cart_color',
					array(
						'label'    => __( 'Add to Cart Button Color', 'nosfir' ),
						'section'  => 'nosfir_woocommerce_colors',
						'priority' => 20,
					)
				)
			);

			// Price Color
			$wp_customize->add_setting(
				'nosfir_price_color',
				array(
					'default'           => '#77a464',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'nosfir_price_color',
					array(
						'label'    => __( 'Price Color', 'nosfir' ),
						'section'  => 'nosfir_woocommerce_colors',
						'priority' => 30,
					)
				)
			);

			// Star Rating Color
			$wp_customize->add_setting(
				'nosfir_star_rating_color',
				array(
					'default'           => '#ff9800',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'nosfir_star_rating_color',
					array(
						'label'    => __( 'Star Rating Color', 'nosfir' ),
						'section'  => 'nosfir_woocommerce_colors',
						'priority' => 40,
					)
				)
			);
		}

		/**
		 * Get Customizer CSS
		 *
		 * @since 1.0.0
		 * @return string $styles the css
		 */
		public function get_css() {
			$sale_badge_color    = get_theme_mod( 'nosfir_sale_badge_color', '#ff0000' );
			$add_to_cart_color   = get_theme_mod( 'nosfir_add_to_cart_color', '#333333' );
			$price_color         = get_theme_mod( 'nosfir_price_color', '#77a464' );
			$star_rating_color   = get_theme_mod( 'nosfir_star_rating_color', '#ff9800' );
			$products_per_row    = get_theme_mod( 'nosfir_products_per_row', 4 );

			$styles = '
			/* WooCommerce Custom Styles */
			.onsale,
			.wc-block-grid__product-onsale {
				background-color: ' . $sale_badge_color . ';
				color: #fff;
			}

			.woocommerce ul.products li.product .button,
			.woocommerce div.product form.cart .button,
			.woocommerce #respond input#submit,
			.woocommerce a.button,
			.woocommerce button.button,
			.woocommerce input.button {
				background-color: ' . $add_to_cart_color . ';
				color: #fff;
			}

			.woocommerce ul.products li.product .button:hover,
			.woocommerce div.product form.cart .button:hover,
			.woocommerce #respond input#submit:hover,
			.woocommerce a.button:hover,
			.woocommerce button.button:hover,
			.woocommerce input.button:hover {
				background-color: ' . $this->adjust_brightness( $add_to_cart_color, -20 ) . ';
				color: #fff;
			}

			.woocommerce ul.products li.product .price,
			.woocommerce div.product p.price,
			.woocommerce div.product span.price {
				color: ' . $price_color . ';
			}

			.woocommerce .star-rating span:before,
			.woocommerce p.stars a:hover:after,
			.woocommerce p.stars a:after {
				color: ' . $star_rating_color . ';
			}

			/* Products per row */
			@media (min-width: 768px) {
				.woocommerce ul.products li.product,
				.woocommerce-page ul.products li.product {
					width: ' . ( 100 / $products_per_row - 2 ) . '%;
				}
			}

			/* Quick View Button */
			.nosfir-quick-view-btn {
				background-color: ' . $add_to_cart_color . ';
				color: #fff;
			}

			.nosfir-quick-view-btn:hover {
				background-color: ' . $this->adjust_brightness( $add_to_cart_color, -20 ) . ';
			}

			/* Wishlist Button */
			.nosfir-wishlist-btn {
				color: ' . $sale_badge_color . ';
			}

			.nosfir-wishlist-btn.added {
				background-color: ' . $sale_badge_color . ';
				color: #fff;
			}

			/* Sticky Add to Cart */
			.nosfir-sticky-add-to-cart {
				background-color: #fff;
				border-top: 2px solid ' . $add_to_cart_color . ';
			}

			/* Mini Cart */
			.site-header-cart .cart-contents .count {
				background-color: ' . $sale_badge_color . ';
				color: #fff;
			}

			.widget_shopping_cart .buttons a {
				background-color: ' . $add_to_cart_color . ';
				color: #fff;
			}

			.widget_shopping_cart .buttons a:hover {
				background-color: ' . $this->adjust_brightness( $add_to_cart_color, -20 ) . ';
			}
			';

			// Add sidebar specific styles
			$shop_sidebar = get_theme_mod( 'nosfir_shop_sidebar_position', 'right' );
			if ( 'left' === $shop_sidebar ) {
				$styles .= '
				.woocommerce #primary {
					float: right;
				}
				.woocommerce #secondary {
					float: left;
				}';
			} elseif ( 'none' === $shop_sidebar ) {
				$styles .= '
				.woocommerce #primary {
					width: 100%;
				}
				.woocommerce #secondary {
					display: none;
				}';
			}

			return apply_filters( 'nosfir_woocommerce_customizer_css', $styles );
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
			wp_add_inline_style( 'nosfir-woocommerce-style', $this->get_css() );
		}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 *
		 * @since 1.0.0
		 */
		public function customize_preview_js() {
			wp_enqueue_script(
				'nosfir-woocommerce-customizer',
				get_template_directory_uri() . '/assets/js/woocommerce-customizer.js',
				array( 'customize-preview', 'jquery' ),
				'1.0.0',
				true
			);

			wp_localize_script(
				'nosfir-woocommerce-customizer',
				'nosfir_woo_customizer',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'nosfir_customizer_nonce' ),
				)
			);
		}

		/**
		 * Adjust brightness of a hex color
		 *
		 * @param string $hex   The hex color.
		 * @param int    $steps Brightness steps.
		 * @return string
		 */
		private function adjust_brightness( $hex, $steps ) {
			// Steps should be between -255 and 255. Negative = darker, positive = lighter
			$steps = max( -255, min( 255, $steps ) );

			// Normalize into a six character long hex string
			$hex = str_replace( '#', '', $hex );
			if ( strlen( $hex ) == 3 ) {
				$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
			}

			// Split into three parts: R, G and B
			$color_parts = str_split( $hex, 2 );
			$return      = '#';

			foreach ( $color_parts as $color ) {
				$color   = hexdec( $color ); // Convert to decimal
				$color   = max( 0, min( 255, $color + $steps ) ); // Adjust color
				$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
			}

			return $return;
		}
	}

endif;

// Initialize the class
Nosfir_WooCommerce_Customizer::get_instance();