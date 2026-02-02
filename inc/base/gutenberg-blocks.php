<?php
/**
 * Enfileirar estilos de blocos Gutenberg
 */
function nosfir_block_assets() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $rel    = '/assets/css/base/gutenberg-blocks' . $suffix . '.css';
    $path   = get_template_directory() . $rel;
    $uri    = file_exists( $path )
        ? get_template_directory_uri() . $rel
        : get_template_directory_uri() . '/assets/css/base/gutenberg-blocks.css';

    // Frontend e Editor
    wp_enqueue_style( 'nosfir-gutenberg-blocks', $uri, array(), NOSFIR_VERSION );
}
add_action( 'enqueue_block_assets', 'nosfir_block_assets' );

/**
 * Enfileirar estilos apenas no editor
 */
function nosfir_editor_assets() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    $rel    = '/assets/css/base/gutenberg-blocks' . $suffix . '.css';
    $path   = get_template_directory() . $rel;
    $uri    = file_exists( $path )
        ? get_template_directory_uri() . $rel
        : get_template_directory_uri() . '/assets/css/base/gutenberg-blocks.css';

    wp_enqueue_style( 'nosfir-editor-styles', $uri, array(), NOSFIR_VERSION );
}
add_action( 'enqueue_block_editor_assets', 'nosfir_editor_assets' );
