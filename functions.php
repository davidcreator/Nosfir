<?php
/**
 * Nosfir engine room
 *
 * O arquivo principal de funções do tema que carrega todos os
 * componentes necessários para o funcionamento do tema.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assign the Nosfir version to a var
 */
$theme           = wp_get_theme( 'nosfir' );
$nosfir_version  = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /* pixels */
}

/**
 * Define theme constants
 */
if ( ! defined( 'NOSFIR_VERSION' ) ) {
	define( 'NOSFIR_VERSION', $nosfir_version );
}

if ( ! defined( 'NOSFIR_DIR' ) ) {
	define( 'NOSFIR_DIR', get_template_directory() );
}

if ( ! defined( 'NOSFIR_URI' ) ) {
	define( 'NOSFIR_URI', get_template_directory_uri() );
}

if ( ! defined( 'NOSFIR_INC_DIR' ) ) {
	define( 'NOSFIR_INC_DIR', NOSFIR_DIR . '/inc' );
}

if ( ! defined( 'NOSFIR_ASSETS_URI' ) ) {
	define( 'NOSFIR_ASSETS_URI', NOSFIR_URI . '/assets' );
}

/**
 * Initialize theme object with all components
 *
 * @since 1.0.0
 */
$nosfir = (object) array(
	'version' => $nosfir_version,

	/**
	 * Initialize the main class.
	 */
	'main' => require NOSFIR_INC_DIR . '/class-nosfir.php',

	/**
	 * Initialize the customizer class.
	 */
	'customizer' => require NOSFIR_INC_DIR . '/customizer/class-nosfir-customizer.php',
);

/**
 * Load core theme files
 *
 * @since 1.0.0
 */
require NOSFIR_INC_DIR . '/nosfir-functions.php';
require NOSFIR_INC_DIR . '/nosfir-template-hooks.php';
require NOSFIR_INC_DIR . '/nosfir-template-functions.php';
require NOSFIR_INC_DIR . '/wordpress-shims.php';

/**
 * Load helper functions
 *
 * @since 1.0.0
 */
require NOSFIR_INC_DIR . '/helpers/template-tags.php';
require NOSFIR_INC_DIR . '/helpers/extras.php';

/**
 * Jetpack compatibility
 *
 * @since 1.0.0
 */
if ( class_exists( 'Jetpack' ) ) {
	$nosfir->jetpack = require NOSFIR_INC_DIR . '/jetpack/class-nosfir-jetpack.php';
}

/**
 * WooCommerce compatibility
 *
 * @since 1.0.0
 */
if ( nosfir_is_woocommerce_activated() ) {
	$nosfir->woocommerce            = require NOSFIR_INC_DIR . '/woocommerce/class-nosfir-woocommerce.php';
	$nosfir->woocommerce_customizer = require NOSFIR_INC_DIR . '/woocommerce/class-nosfir-woocommerce-customizer.php';

	require NOSFIR_INC_DIR . '/woocommerce/nosfir-woocommerce-template-hooks.php';
	require NOSFIR_INC_DIR . '/woocommerce/nosfir-woocommerce-template-functions.php';
	require NOSFIR_INC_DIR . '/woocommerce/nosfir-woocommerce-functions.php';
}

/**
 * Admin specific functionality
 *
 * @since 1.0.0
 */
if ( is_admin() ) {
	$nosfir->admin = require NOSFIR_INC_DIR . '/admin/class-nosfir-admin.php';

	require NOSFIR_INC_DIR . '/admin/class-nosfir-plugin-install.php';
}

/**
 * Starter Content & Guided Tour
 * Only load if WP version is 4.7.3 or above
 *
 * @since 1.0.0
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require NOSFIR_INC_DIR . '/nux/class-nosfir-nux-admin.php';
	require NOSFIR_INC_DIR . '/nux/class-nosfir-nux-guided-tour.php';
	require NOSFIR_INC_DIR . '/nux/class-nosfir-nux-starter-content.php';
}

/**
 * Widgets
 *
 * @since 1.0.0
 */
require NOSFIR_INC_DIR . '/widgets/class-nosfir-widgets.php';

/**
 * Block Editor (Gutenberg) support
 *
 * @since 1.0.0
 */
if ( function_exists( 'register_block_type' ) ) {
	require NOSFIR_INC_DIR . '/blocks/class-nosfir-blocks.php';
}

/**
 * Note: Do not add any custom code here. Please use a child theme or
 * a custom plugin so that your customizations aren't lost during updates.
 *
 * @link https://developer.wordpress.org/themes/advanced-topics/child-themes/
 */