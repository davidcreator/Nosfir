<?php
/**
 * Enfileirar estilos do editor Gutenberg
 */
function nosfir_gutenberg_editor_styles() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    // Adicionar suporte a editor styles
    add_theme_support( 'editor-styles' );

    // Enfileirar o arquivo de estilos do editor
    add_editor_style( 'assets/css/base/gutenberg-editor' . $suffix . '.css' );
}
add_action( 'after_setup_theme', 'nosfir_gutenberg_editor_styles' );

/**
 * Alternativa: Enfileirar diretamente nos assets do editor
 */
function nosfir_enqueue_block_editor_assets() {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'nosfir-gutenberg-editor',
        get_template_directory_uri() . '/assets/css/base/gutenberg-editor' . $suffix . '.css',
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'enqueue_block_editor_assets', 'nosfir_enqueue_block_editor_assets' );