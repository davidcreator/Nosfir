<?php
/**
 * Debug Header - REMOVER APÓS USO
 */

// Carrega WordPress
require_once dirname( __FILE__ ) . '/../../../wp-load.php';

echo '<h1>Diagnóstico do Header - Nosfir</h1>';

// 1. Verificar funções existentes
$functions_to_check = array(
    'nosfir_header_styles',
    'nosfir_header_container',
    'nosfir_skip_links',
    'nosfir_header_top_bar',
    'nosfir_site_branding',
    'nosfir_primary_navigation',
    'nosfir_header_search',
    'nosfir_header_cart',
    'nosfir_mobile_menu_toggle',
    'nosfir_header_container_close',
);

echo '<h2>1. Funções Verificadas:</h2>';
echo '<table border="1" cellpadding="5">';
echo '<tr><th>Função</th><th>Status</th></tr>';

foreach ( $functions_to_check as $func ) {
    $exists = function_exists( $func ) ? '✅ Existe' : '❌ NÃO EXISTE';
    $color = function_exists( $func ) ? 'green' : 'red';
    echo "<tr><td>{$func}</td><td style='color:{$color}'>{$exists}</td></tr>";
}
echo '</table>';

// 2. Verificar hooks registrados
echo '<h2>2. Hooks Registrados em nosfir_header:</h2>';
global $wp_filter;

if ( isset( $wp_filter['nosfir_header'] ) ) {
    echo '<table border="1" cellpadding="5">';
    echo '<tr><th>Prioridade</th><th>Função</th></tr>';
    
    foreach ( $wp_filter['nosfir_header']->callbacks as $priority => $callbacks ) {
        foreach ( $callbacks as $callback ) {
            $func_name = '';
            if ( is_array( $callback['function'] ) ) {
                if ( is_object( $callback['function'][0] ) ) {
                    $func_name = get_class( $callback['function'][0] ) . '->' . $callback['function'][1];
                } else {
                    $func_name = $callback['function'][0] . '::' . $callback['function'][1];
                }
            } else {
                $func_name = $callback['function'];
            }
            echo "<tr><td>{$priority}</td><td>{$func_name}</td></tr>";
        }
    }
    echo '</table>';
} else {
    echo '<p style="color:red; font-weight:bold;">❌ NENHUM HOOK REGISTRADO EM nosfir_header!</p>';
    echo '<p>Este é provavelmente o problema. Verifique se nosfir-template-hooks.php está carregando corretamente.</p>';
}

// 3. Verificar arquivos carregados
echo '<h2>3. Arquivos de Include:</h2>';
$files_to_check = array(
    '/inc/nosfir-template-hooks.php',
    '/inc/nosfir-template-functions.php',
    '/inc/class-nosfir.php',
);

echo '<table border="1" cellpadding="5">';
echo '<tr><th>Arquivo</th><th>Status</th></tr>';

foreach ( $files_to_check as $file ) {
    $full_path = get_template_directory() . $file;
    $exists = file_exists( $full_path ) ? '✅ Existe' : '❌ NÃO EXISTE';
    $color = file_exists( $full_path ) ? 'green' : 'red';
    echo "<tr><td>{$file}</td><td style='color:{$color}'>{$exists}</td></tr>";
}
echo '</table>';

// 4. Verificar menus registrados
echo '<h2>4. Menus Registrados:</h2>';
$menus = get_registered_nav_menus();
echo '<pre>';
print_r( $menus );
echo '</pre>';

echo '<h2>5. Theme Mods:</h2>';
echo '<pre>';
print_r( get_theme_mods() );
echo '</pre>';