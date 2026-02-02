<?php
/**
 * Nosfir engine room
 *
 * O arquivo principal de funções do tema que carrega todos os
 * componentes necessários para o funcionamento do tema.
 *
 * @package Nosfir
 * @since 1.0.0
 * @author David Creator
 */

// Impede acesso direto ao arquivo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check PHP version compatibility
 *
 * @since 1.0.0
 * @return bool
 */
function nosfir_check_requirements() {
    $min_php = '7.4';
    $min_wp  = '5.0';
    
    // Check PHP version
    if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
        add_action( 'admin_notices', function() use ( $min_php ) {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                sprintf(
                    /* translators: 1: Required PHP version 2: Current PHP version */
                    esc_html__( 'Nosfir requires PHP %1$s or higher. Current version: %2$s', 'nosfir' ),
                    $min_php,
                    PHP_VERSION
                )
            );
        });
        return false;
    }
    
    // Check WordPress version
    if ( version_compare( $GLOBALS['wp_version'], $min_wp, '<' ) ) {
        add_action( 'admin_notices', function() use ( $min_wp ) {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                sprintf(
                    /* translators: 1: Required WP version 2: Current WP version */
                    esc_html__( 'Nosfir requires WordPress %1$s or higher. Current version: %2$s', 'nosfir' ),
                    $min_wp,
                    $GLOBALS['wp_version']
                )
            );
        });
        return false;
    }
    
    return true;
}

// Halt if requirements not met
if ( ! nosfir_check_requirements() ) {
    return;
}

/**
 * Get theme version safely
 *
 * @since 1.0.0
 * @return string
 */
function nosfir_get_version() {
    static $version = null;
    
    if ( null === $version ) {
        $theme   = wp_get_theme( 'nosfir' );
        $version = $theme->get( 'Version' );
    }
    
    return $version;
}

/**
 * Define theme constants
 *
 * @since 1.0.0
 */
function nosfir_define_constants() {
    $constants = array(
        'NOSFIR_VERSION'    => nosfir_get_version(),
        'NOSFIR_DIR'        => get_template_directory(),
        'NOSFIR_URI'        => get_template_directory_uri(),
        'NOSFIR_INC_DIR'    => get_template_directory() . '/inc',
        'NOSFIR_ASSETS_URI' => get_template_directory_uri() . '/assets',
    );
    
    foreach ( $constants as $name => $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }
}
nosfir_define_constants();

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since 1.0.0
 * @global int $content_width
 */
function nosfir_content_width() {
    /**
     * Filter the content width in pixels.
     *
     * @since 1.0.0
     * @param int $width Content width in pixels. Default 1200.
     */
    $GLOBALS['content_width'] = apply_filters( 'nosfir_content_width', 1200 );
}
add_action( 'after_setup_theme', 'nosfir_content_width', 0 );

/**
 * Safely require a file
 *
 * @since 1.0.0
 * @param string $file File path.
 * @return mixed|false File return value or false if not found.
 */
function nosfir_require_file( $file ) {
    if ( file_exists( $file ) ) {
        return require $file;
    }
    
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        error_log( 
            sprintf( 
                '[Nosfir Theme] Required file not found: %s', 
                esc_html( $file )
            ) 
        );
    }
    
    return false;
}

/**
 * Safely require multiple files
 *
 * @since 1.0.0
 * @param array $files Array of file paths.
 */
function nosfir_require_files( array $files ) {
    foreach ( $files as $file ) {
        $full_path = NOSFIR_INC_DIR . $file;
        
        if ( file_exists( $full_path ) ) {
            require_once $full_path;
        } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( 
                sprintf( 
                    '[Nosfir Theme] File not found: %s', 
                    esc_html( $full_path )
                ) 
            );
        }
    }
}

/**
 * Initialize theme object with all components
 *
 * @since 1.0.0
 * @global object $nosfir Theme object containing all components.
 */
$nosfir = (object) array(
    'version' => nosfir_get_version(),
    
    /**
     * Initialize the main class.
     */
    'main' => nosfir_require_file( NOSFIR_INC_DIR . '/class-nosfir.php' ),
    
    /**
     * Initialize the customizer class.
     */
    'customizer' => nosfir_require_file( NOSFIR_INC_DIR . '/customizer/class-nosfir-customizer.php' ),
);

/**
 * Enqueue Customizer Scripts and Styles
 */
function nosfir_customize_controls_enqueue() {
    $theme_version = wp_get_theme()->get('Version');
    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    
    // Radio Image Control CSS
    wp_enqueue_style(
        'nosfir-radio-image-control',
        get_template_directory_uri() . '/assets/css/customizer/radio-image-control.css',
        array(),
        $theme_version
    );
    
    // Customizer Controls JS
    wp_enqueue_script(
        'nosfir-customizer-controls',
        get_template_directory_uri() . '/assets/js/customizer/customizer-controls.js',
        array('jquery', 'customize-controls'),
        $theme_version,
        true
    );
    
    // Radio Image Control JS
    wp_enqueue_script(
        'nosfir-radio-image-control',
        get_template_directory_uri() . '/assets/js/customizer/radio-image-control.js',
        array('jquery', 'customize-controls'),
        $theme_version,
        true
    );
    
    // WooCommerce Customizer (only if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        wp_enqueue_script(
            'nosfir-woocommerce-customizer',
            get_template_directory_uri() . '/assets/js/customizer/woocommerce-customizer.js',
            array('jquery', 'customize-controls'),
            $theme_version,
            true
        );
    }
}
add_action('customize_controls_enqueue_scripts', 'nosfir_customize_controls_enqueue');

/**
 * Enqueue Customizer Preview Scripts
 */
function nosfir_customize_preview_enqueue() {
    $theme_version = wp_get_theme()->get('Version');
    
    // Customizer Preview JS
    wp_enqueue_script(
        'nosfir-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer/customizer-preview.js',
        array('jquery', 'customize-preview'),
        $theme_version,
        true
    );
    
    // WooCommerce Customizer Preview (only if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        wp_enqueue_script(
            'nosfir-woocommerce-customizer-preview',
            get_template_directory_uri() . '/assets/js/customizer/woocommerce-customizer.js',
            array('jquery', 'customize-preview'),
            $theme_version,
            true
        );
    }
}
add_action('customize_preview_init', 'nosfir_customize_preview_enqueue');

/**
 * Load core theme files
 *
 * @since 1.0.0
 */
nosfir_require_files( array(
    '/nosfir-functions.php',
    '/nosfir-template-hooks.php',
    '/nosfir-template-functions.php',
    '/wordpress-shims.php',
    '/helpers/template-tags.php',
    '/helpers/extras.php',
) );

/**
 * Jetpack compatibility
 *
 * @since 1.0.0
 */
if ( class_exists( 'Jetpack' ) ) {
    $nosfir->jetpack = nosfir_require_file( NOSFIR_INC_DIR . '/jetpack/class-nosfir-jetpack.php' );
}

/**
 * WooCommerce compatibility
 *
 * @since 1.0.0
 */
if ( function_exists( 'nosfir_is_woocommerce_activated' ) && nosfir_is_woocommerce_activated() ) {
    $nosfir->woocommerce            = nosfir_require_file( NOSFIR_INC_DIR . '/inc/woocommerce/class-nosfir-woocommerce.php' );
    $nosfir->woocommerce_customizer = nosfir_require_file( NOSFIR_INC_DIR . '/inc/woocommerce/class-nosfir-woocommerce-customizer.php' );
    
    nosfir_require_files( array(
        '/woocommerce/nosfir-woocommerce-template-hooks.php',
        '/woocommerce/nosfir-woocommerce-template-functions.php',
        '/woocommerce/nosfir-woocommerce-functions.php',
    ) );
}

/**
 * Admin specific functionality
 *
 * @since 1.0.0
 */
if ( is_admin() ) {
    $nosfir->admin = nosfir_require_file( NOSFIR_INC_DIR . '/admin/class-nosfir-admin.php' );
    
    nosfir_require_files( array(
        '/admin/class-nosfir-plugin-install.php',
    ) );
}

/**
 * Starter Content & Guided Tour
 * Only load if WP version supports it
 *
 * @since 1.0.0
 */
if ( version_compare( $GLOBALS['wp_version'], '5.0', '>=' ) && 
     ( is_admin() || is_customize_preview() ) ) {
    nosfir_require_files( array(
        '/nux/class-nosfir-nux-admin.php',
        '/nux/class-nosfir-nux-guided-tour.php',
        '/nux/class-nosfir-nux-starter-content.php',
    ) );
}

/**
 * Widgets
 *
 * @since 1.0.0
 */
nosfir_require_file( NOSFIR_INC_DIR . '/widgets/class-nosfir-widgets.php' );

/**
 * Block Editor (Gutenberg) support
 *
 * @since 1.0.0
 */
if ( function_exists( 'register_block_type' ) ) {
    nosfir_require_file( NOSFIR_INC_DIR . '/blocks/class-nosfir-blocks.php' );
}

/**
 * Note: Do not add any custom code here. Please use a child theme or
 * a custom plugin so that your customizations aren't lost during updates.
 *
 * @link https://developer.wordpress.org/themes/advanced-topics/child-themes/
 */