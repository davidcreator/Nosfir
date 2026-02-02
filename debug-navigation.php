<?php
/**
 * Navigation Debug Tool
 * REMOVER APÓS USO
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Load WordPress
    require_once dirname( __FILE__ ) . '/../../../wp-load.php';
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Nav Debug</title>';
echo '<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .error { color: red; font-weight: bold; }
    .success { color: green; }
    .warning { color: orange; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background: #333; color: white; }
    pre { background: #1e1e1e; color: #d4d4d4; padding: 15px; overflow-x: auto; }
</style></head><body>';

echo '<h1>🔍 Diagnóstico de Navegação - Nosfir Theme</h1>';

// 1. Verificar Menus Registrados
echo '<div class="section">';
echo '<h2>1. Menus Registrados</h2>';
$menus = get_registered_nav_menus();
if ( empty( $menus ) ) {
    echo '<p class="error">❌ NENHUM MENU REGISTRADO! Este é um problema crítico.</p>';
    echo '<p>Adicione em functions.php ou class-nosfir.php:</p>';
    echo '<pre>register_nav_menus( array(
    \'primary\'   => __( \'Primary Menu\', \'nosfir\' ),
    \'secondary\' => __( \'Secondary Menu\', \'nosfir\' ),
    \'mobile\'    => __( \'Mobile Menu\', \'nosfir\' ),
    \'footer\'    => __( \'Footer Menu\', \'nosfir\' ),
    \'social\'    => __( \'Social Menu\', \'nosfir\' ),
) );</pre>';
} else {
    echo '<table>';
    echo '<tr><th>Location</th><th>Label</th><th>Menu Atribuído</th></tr>';
    $locations = get_nav_menu_locations();
    foreach ( $menus as $location => $label ) {
        $menu_id = isset( $locations[ $location ] ) ? $locations[ $location ] : 0;
        $menu_obj = $menu_id ? wp_get_nav_menu_object( $menu_id ) : null;
        $menu_name = $menu_obj ? $menu_obj->name : '<span class="warning">Nenhum menu atribuído</span>';
        echo "<tr><td>{$location}</td><td>{$label}</td><td>{$menu_name}</td></tr>";
    }
    echo '</table>';
}
echo '</div>';

// 2. Verificar Hooks de Navegação
echo '<div class="section">';
echo '<h2>2. Hooks de Navegação Registrados</h2>';

$nav_hooks = array(
    'nosfir_header',
    'nosfir_after_header',
    'nosfir_before_header',
    'nosfir_footer',
);

global $wp_filter;

foreach ( $nav_hooks as $hook ) {
    echo "<h3>Hook: {$hook}</h3>";
    if ( isset( $wp_filter[ $hook ] ) ) {
        echo '<table>';
        echo '<tr><th>Prioridade</th><th>Função</th><th>Status</th></tr>';
        foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
            foreach ( $callbacks as $callback ) {
                $func_name = '';
                if ( is_array( $callback['function'] ) ) {
                    if ( is_object( $callback['function'][0] ) ) {
                        $func_name = get_class( $callback['function'][0] ) . '->' . $callback['function'][1];
                    } else {
                        $func_name = $callback['function'][0] . '::' . $callback['function'][1];
                    }
                } elseif ( is_string( $callback['function'] ) ) {
                    $func_name = $callback['function'];
                }
                
                $exists = is_callable( $callback['function'] );
                $status = $exists ? '<span class="success">✅ OK</span>' : '<span class="error">❌ NÃO EXISTE</span>';
                
                echo "<tr><td>{$priority}</td><td>{$func_name}</td><td>{$status}</td></tr>";
            }
        }
        echo '</table>';
    } else {
        echo '<p class="warning">⚠️ Nenhum callback registrado neste hook</p>';
    }
}
echo '</div>';

// 3. Verificar Funções de Navegação
echo '<div class="section">';
echo '<h2>3. Funções de Navegação</h2>';

$nav_functions = array(
    'nosfir_primary_navigation',
    'nosfir_secondary_navigation',
    'nosfir_mobile_navigation',
    'nosfir_footer_navigation',
    'nosfir_site_branding',
    'nosfir_header_container',
    'nosfir_header_container_close',
    'nosfir_skip_links',
    'nosfir_header_search',
    'nosfir_header_account',
    'nosfir_header_cart',
);

echo '<table>';
echo '<tr><th>Função</th><th>Status</th><th>Arquivo</th></tr>';
foreach ( $nav_functions as $func ) {
    $exists = function_exists( $func );
    $status = $exists ? '<span class="success">✅ Existe</span>' : '<span class="error">❌ NÃO EXISTE</span>';
    
    $file = 'N/A';
    if ( $exists ) {
        try {
            $ref = new ReflectionFunction( $func );
            $file = str_replace( get_template_directory(), '', $ref->getFileName() );
            $file .= ':' . $ref->getStartLine();
        } catch ( Exception $e ) {
            $file = 'Não foi possível determinar';
        }
    }
    
    echo "<tr><td>{$func}</td><td>{$status}</td><td>{$file}</td></tr>";
}
echo '</table>';
echo '</div>';

// 4. Verificar Arquivos CSS de Navegação
echo '<div class="section">';
echo '<h2>4. Arquivos CSS Relacionados</h2>';

$css_files = array(
    '/assets/css/style.css',
    '/assets/css/navigation.css',
    '/assets/css/header.css',
    '/assets/css/responsive.css',
    '/assets/css/mobile.css',
    '/assets/css/menu.css',
    '/style.css',
);

echo '<table>';
echo '<tr><th>Arquivo</th><th>Existe</th><th>Tamanho</th></tr>';
foreach ( $css_files as $file ) {
    $full_path = get_template_directory() . $file;
    $exists = file_exists( $full_path );
    $status = $exists ? '<span class="success">✅ Sim</span>' : '<span class="warning">⚠️ Não</span>';
    $size = $exists ? size_format( filesize( $full_path ) ) : '-';
    
    echo "<tr><td>{$file}</td><td>{$status}</td><td>{$size}</td></tr>";
}
echo '</table>';
echo '</div>';

// 5. Verificar Arquivos JS de Navegação
echo '<div class="section">';
echo '<h2>5. Arquivos JavaScript Relacionados</h2>';

$js_files = array(
    '/assets/js/navigation.js',
    '/assets/js/mobile-menu.js',
    '/assets/js/main.js',
    '/assets/js/header.js',
    '/assets/js/menu.js',
);

echo '<table>';
echo '<tr><th>Arquivo</th><th>Existe</th><th>Tamanho</th></tr>';
foreach ( $js_files as $file ) {
    $full_path = get_template_directory() . $file;
    $exists = file_exists( $full_path );
    $status = $exists ? '<span class="success">✅ Sim</span>' : '<span class="warning">⚠️ Não</span>';
    $size = $exists ? size_format( filesize( $full_path ) ) : '-';
    
    echo "<tr><td>{$file}</td><td>{$status}</td><td>{$size}</td></tr>";
}
echo '</table>';
echo '</div>';

// 6. Verificar HTML gerado pela navegação
echo '<div class="section">';
echo '<h2>6. HTML Gerado pela Navegação Principal</h2>';
echo '<p>Inspecione se há tags não fechadas ou estrutura incorreta:</p>';

ob_start();
if ( function_exists( 'nosfir_primary_navigation' ) ) {
    nosfir_primary_navigation();
} else {
    echo '<!-- Função nosfir_primary_navigation não existe -->';
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( array( 'theme_location' => 'primary' ) );
    } else {
        echo '<!-- Nenhum menu atribuído à localização primary -->';
    }
}
$nav_html = ob_get_clean();

echo '<pre>' . htmlspecialchars( $nav_html ) . '</pre>';

// Verificar tags não fechadas
$open_tags = preg_match_all( '/<(div|nav|ul|li|a|span|button)[^>]*>/i', $nav_html );
$close_tags = preg_match_all( '/<\/(div|nav|ul|li|a|span|button)>/i', $nav_html );

if ( $open_tags !== $close_tags ) {
    echo '<p class="error">⚠️ ALERTA: Possíveis tags não fechadas! Abertas: ' . $open_tags . ', Fechadas: ' . $close_tags . '</p>';
} else {
    echo '<p class="success">✅ Contagem de tags parece correta</p>';
}
echo '</div>';

// 7. Verificar Enqueue de Scripts/Styles
echo '<div class="section">';
echo '<h2>7. Scripts e Styles Enfileirados</h2>';

global $wp_scripts, $wp_styles;

echo '<h3>Styles (CSS):</h3>';
echo '<ul>';
if ( isset( $wp_styles->registered ) ) {
    foreach ( $wp_styles->registered as $handle => $style ) {
        if ( strpos( $handle, 'nosfir' ) !== false || strpos( $style->src, 'nosfir' ) !== false ) {
            echo "<li><strong>{$handle}</strong>: {$style->src}</li>";
        }
    }
}
echo '</ul>';

echo '<h3>Scripts (JS):</h3>';
echo '<ul>';
if ( isset( $wp_scripts->registered ) ) {
    foreach ( $wp_scripts->registered as $handle => $script ) {
        if ( strpos( $handle, 'nosfir' ) !== false || strpos( $script->src, 'nosfir' ) !== false ) {
            echo "<li><strong>{$handle}</strong>: {$script->src}</li>";
        }
    }
}
echo '</ul>';
echo '</div>';

// 8. Verificar Walker personalizado
echo '<div class="section">';
echo '<h2>8. Walker de Menu Personalizado</h2>';

$walker_classes = array(
    'Nosfir_Walker_Nav_Menu',
    'Nosfir_Nav_Walker',
    'Nosfir_Menu_Walker',
    'Nosfir_Primary_Walker',
);

$found_walker = false;
foreach ( $walker_classes as $class ) {
    if ( class_exists( $class ) ) {
        echo '<p class="success">✅ Walker encontrado: ' . $class . '</p>';
        $found_walker = true;
    }
}

if ( ! $found_walker ) {
    echo '<p class="warning">⚠️ Nenhum Walker personalizado encontrado (usando padrão do WordPress)</p>';
}
echo '</div>';

echo '</body></html>';