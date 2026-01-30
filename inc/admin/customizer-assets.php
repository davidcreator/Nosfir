<?php
/**
 * Nosfir Customizer Assets
 *
 * Enqueue styles and scripts for the Customizer.
 *
 * @package Nosfir
 * @subpackage Admin
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Customizer admin styles.
 *
 * @since 1.0.0
 * @return void
 */
function nosfir_customizer_admin_styles() {
    
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    
    wp_enqueue_style(
        'nosfir-customizer-admin',
        get_theme_file_uri( 'assets/css/admin/customizer/customizer' . $suffix . '.css' ),
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'customize_controls_enqueue_scripts', 'nosfir_customizer_admin_styles' );

/**
 * Enqueue Customizer preview styles.
 *
 * @since 1.0.0
 * @return void
 */
function nosfir_customizer_preview_styles() {
    
    wp_enqueue_style(
        'nosfir-customizer-preview',
        get_theme_file_uri( 'assets/css/admin/customizer/customizer-preview.css' ),
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'customize_preview_init', 'nosfir_customizer_preview_styles' );