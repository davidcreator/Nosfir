<?php
/**
 * Enfileirar estilos de blocos Gutenberg
 */
function nosfir_block_assets() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    // Frontend e Editor
    wp_enqueue_style(
        'nosfir-gutenberg-blocks',
        get_template_directory_uri() . '/assets/css/base/gutenberg-blocks' . $suffix . '.css',
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'enqueue_block_assets', 'nosfir_block_assets' );

/**
 * Enfileirar estilos apenas no editor
 */
function nosfir_editor_assets() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'nosfir-editor-styles',
        get_template_directory_uri() . '/assets/css/base/gutenberg-blocks' . $suffix . '.css',
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'enqueue_block_editor_assets', 'nosfir_editor_assets' );