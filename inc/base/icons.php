<?php
/**
 * Enfileirar estilos de ícones
 */
function nosfir_icon_styles() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'nosfir-icons',
        get_template_directory_uri() . '/assets/css/base/icons' . $suffix . '.css',
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'nosfir_icon_styles', 5 );