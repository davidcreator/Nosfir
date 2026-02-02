<?php
/**
 * Enfileirar estilos de ícones
 */
function nosfir_icon_styles() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $rel    = '/assets/css/base/icons' . $suffix . '.css';
    $path   = get_template_directory() . $rel;
    $uri    = file_exists( $path )
        ? get_template_directory_uri() . $rel
        : get_template_directory_uri() . '/assets/css/base/icons.css';

    wp_enqueue_style( 'nosfir-icons', $uri, array(), NOSFIR_VERSION );
}
add_action( 'wp_enqueue_scripts', 'nosfir_icon_styles', 5 );
