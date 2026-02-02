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
    
    return $version ? $version : '1.0.0';
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
        error_log( sprintf( '[Nosfir Theme] Required file not found: %s', $file ) );
    }
    
    return false;
}

/**
 * Safely require multiple files from inc directory
 *
 * @since 1.0.0
 * @param array $files Array of file paths relative to inc/.
 */
function nosfir_require_files( array $files ) {
    foreach ( $files as $file ) {
        $full_path = NOSFIR_INC_DIR . $file;
        
        if ( file_exists( $full_path ) ) {
            require_once $full_path;
        } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( sprintf( '[Nosfir Theme] File not found: %s', $full_path ) );
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
);

// Load main class
$main_class = NOSFIR_INC_DIR . '/class-nosfir.php';
if ( file_exists( $main_class ) ) {
    $nosfir->main = require_once $main_class;
}

// Load customizer class
$customizer_class = NOSFIR_INC_DIR . '/customizer/class-nosfir-customizer.php';
if ( file_exists( $customizer_class ) ) {
    $nosfir->customizer = require_once $customizer_class;
}

/**
 * ============================================
 * CUSTOMIZER SCRIPTS - CORRIGIDO
 * ============================================
 */

/**
 * Enqueue Customizer Controls Scripts (Admin Side)
 */
function nosfir_customize_controls_enqueue() {
    $version = NOSFIR_VERSION;
    $assets_uri = NOSFIR_ASSETS_URI;
    $assets_dir = NOSFIR_DIR . '/assets';
    
    // Radio Image Control CSS
    $radio_css = $assets_dir . '/css/customizer/radio-image-control.css';
    if ( file_exists( $radio_css ) ) {
        wp_enqueue_style(
            'nosfir-radio-image-control',
            $assets_uri . '/css/customizer/radio-image-control.css',
            array(),
            $version
        );
    }
    
    // Customizer Controls JS
    $controls_js = $assets_dir . '/js/customizer/customizer-controls.js';
    if ( file_exists( $controls_js ) ) {
        wp_enqueue_script(
            'nosfir-customizer-controls',
            $assets_uri . '/js/customizer/customizer-controls.js',
            array( 'jquery', 'customize-controls' ),
            $version,
            true
        );
    }
    
    // Radio Image Control JS
    $radio_js = $assets_dir . '/js/customizer/radio-image-control.js';
    if ( file_exists( $radio_js ) ) {
        wp_enqueue_script(
            'nosfir-radio-image-control',
            $assets_uri . '/js/customizer/radio-image-control.js',
            array( 'jquery', 'customize-controls' ),
            $version,
            true
        );
    }
    
    // WooCommerce Customizer (only if WooCommerce is active)
    if ( class_exists( 'WooCommerce' ) ) {
        $wc_js = $assets_dir . '/js/customizer/woocommerce-customizer.js';
        if ( file_exists( $wc_js ) ) {
            wp_enqueue_script(
                'nosfir-woocommerce-customizer',
                $assets_uri . '/js/customizer/woocommerce-customizer.js',
                array( 'jquery', 'customize-controls' ),
                $version,
                true
            );
        }
    }
}
add_action( 'customize_controls_enqueue_scripts', 'nosfir_customize_controls_enqueue' );

/**
 * Enqueue Customizer Preview Scripts (Frontend Preview)
 */
function nosfir_customize_preview_enqueue() {
    $version = NOSFIR_VERSION;
    $assets_uri = NOSFIR_ASSETS_URI;
    $assets_dir = NOSFIR_DIR . '/assets';
    
    // Customizer Preview JS
    $preview_js = $assets_dir . '/js/customizer/customizer-preview.js';
    if ( file_exists( $preview_js ) ) {
        wp_enqueue_script(
            'nosfir-customizer-preview',
            $assets_uri . '/js/customizer/customizer-preview.js',
            array( 'jquery', 'customize-preview' ),
            $version,
            true
        );
    }
}
add_action( 'customize_preview_init', 'nosfir_customize_preview_enqueue' );

/**
 * ============================================
 * LOAD CORE THEME FILES
 * ============================================
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
 */
if ( class_exists( 'Jetpack' ) ) {
    nosfir_require_file( NOSFIR_INC_DIR . '/jetpack/class-nosfir-jetpack.php' );
}

/**
 * WooCommerce compatibility
 * CORRIGIDO: Caminho estava duplicado (/inc/woocommerce dentro de NOSFIR_INC_DIR que já é /inc)
 */
if ( class_exists( 'WooCommerce' ) ) {
    // Carregar classe principal WooCommerce
    nosfir_require_file( NOSFIR_INC_DIR . '/woocommerce/class-nosfir-woocommerce.php' );
    
    // Carregar arquivos de template e funções
    nosfir_require_files( array(
        '/woocommerce/nosfir-woocommerce-template-hooks.php',
        '/woocommerce/nosfir-woocommerce-template-functions.php',
        '/woocommerce/nosfir-woocommerce-functions.php',
        '/woocommerce/nosfir-woocommerce-hooks.php',
    ) );
}

/**
 * Admin specific functionality
 */
if ( is_admin() ) {
    nosfir_require_file( NOSFIR_INC_DIR . '/admin/class-nosfir-admin.php' );
    nosfir_require_file( NOSFIR_INC_DIR . '/admin/class-nosfir-plugin-install.php' );
}

/**
 * Starter Content & Guided Tour
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
 */
nosfir_require_file( NOSFIR_INC_DIR . '/widgets/class-nosfir-widgets.php' );

/**
 * Block Editor (Gutenberg) support
 */
if ( function_exists( 'register_block_type' ) ) {
    nosfir_require_file( NOSFIR_INC_DIR . '/blocks/class-nosfir-blocks.php' );
}

/**
 * Note: Do not add any custom code here. Please use a child theme or
 * a custom plugin so that your customizations aren't lost during updates.
 */