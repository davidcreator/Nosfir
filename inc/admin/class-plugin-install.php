<?php
/**
 * Enfileirar estilos de plugin install
 */
function nosfir_admin_plugin_install_styles( $hook ) {
    // Carregar apenas nas páginas relevantes
    if ( 'plugins.php' !== $hook && 
         'plugin-install.php' !== $hook && 
         'themes.php' !== $hook &&
         'appearance_page_nosfir-plugins' !== $hook ) {
        return;
    }

    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'nosfir-plugin-install',
        get_template_directory_uri() . '/assets/css/admin/plugin-install' . $suffix . '.css',
        array(),
        NOSFIR_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'nosfir_admin_plugin_install_styles' );