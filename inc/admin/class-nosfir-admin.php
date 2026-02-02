<?php
/**
 * Nosfir Admin Class
 *
 * Gerencia todas as funcionalidades administrativas do tema Nosfir,
 * incluindo página de boas-vindas, configurações, atualizações e integrações.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_Admin')) :

    /**
     * Classe principal de administração do tema Nosfir
     */
    class Nosfir_Admin {

        /**
         * Versão do tema
         *
         * @var string
         */
        private $version;

        /**
         * URL base do tema
         *
         * @var string
         */
        private $theme_url;

        /**
         * Caminho base do tema
         *
         * @var string
         */
        private $theme_path;

        /**
         * Dados do tema
         *
         * @var WP_Theme
         */
        private $theme;

        /**
         * Opções do admin
         *
         * @var array
         */
        private $options;

        /**
         * Instance única da classe
         *
         * @var Nosfir_Admin
         */
        private static $instance = null;

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_Admin
         */
        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Construtor da classe
         *
         * @since 1.0.0
         */
        public function __construct() {
            $this->theme = wp_get_theme('nosfir');
            $this->version = $this->theme->get('Version');
            $this->theme_url = get_template_directory_uri();
            $this->theme_path = get_template_directory();
            
            $this->init_hooks();
            $this->load_dependencies();
        }

        /**
         * Inicializa os hooks
         *
         * @since 1.0.0
         */
        private function init_hooks() {
            // Menu admin
            add_action('admin_menu', array($this, 'register_admin_menu'));
            
            // Scripts e estilos admin
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
            
            // Notices admin
            add_action('admin_notices', array($this, 'admin_notices'));
            
            // AJAX handlers
            add_action('wp_ajax_nosfir_dismiss_notice', array($this, 'dismiss_notice'));
            add_action('wp_ajax_nosfir_import_demo', array($this, 'ajax_import_demo'));
            
            // Customização do admin
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_head', array($this, 'admin_head'));
            add_filter('admin_footer_text', array($this, 'admin_footer_text'));
            
            // Dashboard widgets
            add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
            
            // Atualizações do tema
            add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_updates'));
            
            // Redirect após ativação
            add_action('after_switch_theme', array($this, 'redirect_on_activation'));
            
            // Adiciona metaboxes
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            
            // Salva metaboxes
            add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
        }

        /**
         * Carrega dependências
         *
         * @since 1.0.0
         */
        private function load_dependencies() {
            // Carrega classes auxiliares se existirem
            $dependencies = array(
                'class-nosfir-dashboard.php',
                'class-nosfir-plugin-install.php',
                'class-nosfir-demo-importer.php',
                'class-nosfir-system-status.php',
                'class-nosfir-theme-updater.php'
            );

            foreach ($dependencies as $file) {
                $filepath = $this->theme_path . '/inc/admin/' . $file;
                if (file_exists($filepath)) {
                    require_once $filepath;
                }
            }
        }

        /**
         * Inicialização do admin
         *
         * @since 1.0.0
         */
        public function admin_init() {
            // Registra configurações
            $this->register_settings();
            
            // Verifica requisitos do sistema
            $this->check_system_requirements();
            
            // Adiciona capacidades customizadas
            $this->add_theme_caps();
        }

        /**
         * Registra configurações do tema
         *
         * @since 1.0.0
         */
        public function register_settings() {
            register_setting('nosfir_options_group', 'nosfir_options');
            
            add_settings_section(
                'nosfir_general_section',
                __('General Settings', 'nosfir'),
                array($this, 'general_section_callback'),
                'nosfir-settings'
            );
            
            add_settings_field(
                'nosfir_field_example',
                __('Example Field', 'nosfir'),
                array($this, 'field_example_callback'),
                'nosfir-settings',
                'nosfir_general_section'
            );
        }

        /**
         * Callback da seção geral
         */
        public function general_section_callback() {
            echo '<p>' . __('General settings for the Nosfir theme.', 'nosfir') . '</p>';
        }

        /**
         * Callback do campo de exemplo
         */
        public function field_example_callback() {
            $options = get_option('nosfir_options');
            $value = isset($options['example_field']) ? $options['example_field'] : '';
            echo '<input type="text" id="example_field" name="nosfir_options[example_field]" value="' . esc_attr($value) . '" />';
        }

        /**
         * Verifica requisitos do sistema
         *
         * @since 1.0.0
         */
        private function check_system_requirements() {
            // Implementação básica
            return true;
        }

        /**
         * Adiciona capacidades customizadas
         *
         * @since 1.0.0
         */
        private function add_theme_caps() {
            // Implementação básica
            return true;
        }

        /**
         * Registra menus administrativos
         *
         * @since 1.0.0
         */
        public function register_admin_menu() {
            // Menu principal do tema
            add_menu_page(
                __('Nosfir Theme', 'nosfir'),
                __('Nosfir', 'nosfir'),
                'manage_options',
                'nosfir-dashboard',
                array($this, 'render_dashboard_page'),
                $this->get_menu_icon(),
                2
            );

            // Dashboard
            add_submenu_page(
                'nosfir-dashboard',
                __('Dashboard', 'nosfir'),
                __('Dashboard', 'nosfir'),
                'manage_options',
                'nosfir-dashboard',
                array($this, 'render_dashboard_page')
            );

            // Página de boas-vindas
            add_submenu_page(
                'nosfir-dashboard',
                __('Getting Started', 'nosfir'),
                __('Getting Started', 'nosfir'),
                'manage_options',
                'nosfir-welcome',
                array($this, 'render_welcome_page')
            );

            // Importador de demos
            add_submenu_page(
                'nosfir-dashboard',
                __('Demo Import', 'nosfir'),
                __('Demo Import', 'nosfir'),
                'manage_options',
                'nosfir-demo-import',
                array($this, 'render_demo_import_page')
            );

            // Plugins recomendados
            add_submenu_page(
                'nosfir-dashboard',
                __('Recommended Plugins', 'nosfir'),
                __('Plugins', 'nosfir'),
                'manage_options',
                'nosfir-plugins',
                array($this, 'render_plugins_page')
            );

            // Configurações do tema
            add_submenu_page(
                'nosfir-dashboard',
                __('Theme Settings', 'nosfir'),
                __('Settings', 'nosfir'),
                'manage_options',
                'nosfir-settings',
                array($this, 'render_settings_page')
            );

            // Status do sistema
            add_submenu_page(
                'nosfir-dashboard',
                __('System Status', 'nosfir'),
                __('System Status', 'nosfir'),
                'manage_options',
                'nosfir-system-status',
                array($this, 'render_system_status_page')
            );

            // Suporte
            add_submenu_page(
                'nosfir-dashboard',
                __('Support', 'nosfir'),
                __('Support', 'nosfir'),
                'manage_options',
                'nosfir-support',
                array($this, 'render_support_page')
            );

            // Licença removida

            // Hook para adicionar menus customizados
            do_action('nosfir_admin_menu', 'nosfir-dashboard');
        }

        /**
         * Enfileira assets do admin
         *
         * @param string $hook_suffix
         * @since 1.0.0
         */
        public function enqueue_admin_assets($hook_suffix) {
            // Verifica se estamos em uma página do Nosfir
            if (!$this->is_nosfir_admin_page($hook_suffix)) {
                return;
            }

            // CSS principal do admin
            wp_enqueue_style(
                'nosfir-admin',
                $this->theme_url . '/assets/css/admin/admin.css',
                array(),
                $this->version
            );

            $page_styles = array(
                'nosfir-dashboard'     => 'dashboard.css',
                'nosfir-welcome'       => 'dashboard.css',
                'nosfir-demo-import'   => 'dashboard.css',
                'nosfir-plugins'       => 'plugin-install.css',
                'nosfir-settings'      => 'dashboard.css',
                'nosfir-system-status' => 'dashboard.css',
                'nosfir-support'       => 'dashboard.css',
                'nosfir-tours'         => 'dashboard.css'
            );

            $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
            
            if (isset($page_styles[$current_page])) {
                wp_enqueue_style(
                    'nosfir-admin-' . $current_page,
                    $this->theme_url . '/assets/css/admin/' . $page_styles[$current_page],
                    array('nosfir-admin'),
                    $this->version
                );
            }

            // JavaScript principal do admin
            wp_enqueue_script(
                'nosfir-admin',
                $this->theme_url . '/assets/js/admin/admin.js',
                array('jquery', 'wp-util'),
                $this->version,
                true
            );

            $page_scripts = array(
                'nosfir-plugins' => 'plugin-install.js'
            );

            if (isset($page_scripts[$current_page])) {
                wp_enqueue_script(
                    'nosfir-admin-' . $current_page,
                    $this->theme_url . '/assets/js/admin/' . $page_scripts[$current_page],
                    array('nosfir-admin'),
                    $this->version,
                    true
                );
            }

            // Localização para JavaScript
            wp_localize_script('nosfir-admin', 'nosfir_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfir-admin-nonce'),
                'theme_url' => $this->theme_url,
                'version' => $this->version,
                'strings' => array(
                    'installing' => __('Installing...', 'nosfir'),
                    'activating' => __('Activating...', 'nosfir'),
                    'activated' => __('Activated', 'nosfir'),
                    'error' => __('Error', 'nosfir'),
                    'success' => __('Success', 'nosfir'),
                    'importing' => __('Importing demo content...', 'nosfir'),
                    'imported' => __('Demo imported successfully!', 'nosfir'),
                    'confirm_import' => __('Are you sure you want to import this demo? This will overwrite existing content.', 'nosfir'),
                    'confirm_reset' => __('Are you sure you want to reset all theme settings?', 'nosfir')
                ),
                'api' => array(
                    'endpoints' => array(
                        'check_updates' => rest_url('nosfir/v1/check-updates'),
                        'get_demos' => rest_url('nosfir/v1/demos'),
                        'system_info' => rest_url('nosfir/v1/system-info')
                    )
                )
            ));

            // Media uploader para páginas que precisam
            if (in_array($current_page, array('nosfir-settings', 'nosfir-demo-import'))) {
                wp_enqueue_media();
            }

            // RTL support
            wp_style_add_data('nosfir-admin', 'rtl', 'replace');
        }

        /**
         * Renderiza página do dashboard
         *
         * @since 1.0.0
         */
        public function render_admin_page($page) {
            if ($page === 'dashboard') {
                ?>
                <div class="nosfir-admin-wrap">
                    <header class="nosfir-dashboard-header">
                        <div class="nosfir-dashboard-brand">
                            <img src="<?php echo esc_url($this->theme_url . '/assets/images/logo.png'); ?>" alt="Nosfir">
                            <div class="nosfir-dashboard-titles">
                                <h1>Nosfir</h1>
                                <span class="nosfir-version">v<?php echo esc_html($this->version); ?></span>
                            </div>
                        </div>
                        <div class="nosfir-dashboard-actions">
                            <a class="button button-primary" href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php _e('Customize', 'nosfir'); ?></a>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>"><?php _e('Plugins', 'nosfir'); ?></a>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-demo-import')); ?>"><?php _e('Import Demo', 'nosfir'); ?></a>
                        </div>
                    </header>
                    
                    <section class="nosfir-dashboard-grid">
                        <article class="nosfir-card nosfir-card--wide">
                            <h2><?php _e('Visão Geral do Tema', 'nosfir'); ?></h2>
                            <div class="nosfir-features-grid">
                                <?php foreach ($this->get_theme_features() as $feature) : ?>
                                    <div class="nosfir-feature-item">
                                        <span class="dashicons dashicons-<?php echo esc_attr($feature['icon']); ?>"></span>
                                        <div>
                                            <h3><?php echo esc_html($feature['title']); ?></h3>
                                            <p><?php echo esc_html($feature['description']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>
                        
                        <article class="nosfir-card">
                            <h2><?php _e('Status do Sistema', 'nosfir'); ?></h2>
                            <?php $sys = $this->get_system_info(); ?>
                            <ul class="nosfir-status-list">
                                <li><strong>WordPress:</strong> <?php echo esc_html($sys['wp_version']); ?></li>
                                <li><strong>PHP:</strong> <?php echo esc_html($sys['php_version']); ?></li>
                                <li><strong>Memória:</strong> <?php echo esc_html($sys['memory_limit']); ?></li>
                                <li><strong>Execução Máx.:</strong> <?php echo esc_html($sys['max_execution_time']); ?>s</li>
                            </ul>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-system-status')); ?>"><?php _e('Ver Detalhes', 'nosfir'); ?></a>
                        </article>
                        
                        <article class="nosfir-card">
                            <h2><?php _e('Integrações', 'nosfir'); ?></h2>
                            <ul class="nosfir-integrations">
                                <li>WooCommerce: <?php echo class_exists('WooCommerce') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                                <li>Jetpack: <?php echo class_exists('Jetpack') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                                <li>Elementor: <?php echo class_exists('\\Elementor\\Plugin') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                            </ul>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>"><?php _e('Gerenciar Plugins', 'nosfir'); ?></a>
                        </article>
                        
                        <article class="nosfir-card">
                            <h2><?php _e('Ações Rápidas', 'nosfir'); ?></h2>
                            <div class="nosfir-quick-actions">
                                <a class="button button-primary" href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php _e('Abrir Customizer', 'nosfir'); ?></a>
                                <a class="button" href="<?php echo esc_url(admin_url('post-new.php')); ?>"><?php _e('Novo Post', 'nosfir'); ?></a>
                                <a class="button" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>"><?php _e('Nova Página', 'nosfir'); ?></a>
                            </div>
                        </article>
                        
                        <article class="nosfir-card nosfir-card--wide">
                            <h2><?php _e('Recursos e Suporte', 'nosfir'); ?></h2>
                            <div class="nosfir-resources-grid">
                                <a class="nosfir-resource" href="<?php echo esc_url($this->get_documentation_url()); ?>" target="_blank">
                                    <span class="dashicons dashicons-book"></span>
                                    <div>
                                        <h3><?php _e('Documentação', 'nosfir'); ?></h3>
                                        <p><?php _e('Guias detalhados para cada recurso do tema.', 'nosfir'); ?></p>
                                    </div>
                                </a>
                                <a class="nosfir-resource" href="<?php echo esc_url($this->get_videos_url()); ?>" target="_blank">
                                    <span class="dashicons dashicons-video-alt3"></span>
                                    <div>
                                        <h3><?php _e('Tutoriais em Vídeo', 'nosfir'); ?></h3>
                                        <p><?php _e('Passo a passo visual para configurar o site.', 'nosfir'); ?></p>
                                    </div>
                                </a>
                                <a class="nosfir-resource" href="<?php echo esc_url($this->get_support_url()); ?>" target="_blank">
                                    <span class="dashicons dashicons-sos"></span>
                                    <div>
                                        <h3><?php _e('Suporte', 'nosfir'); ?></h3>
                                        <p><?php _e('Acesso ao suporte e comunidade.', 'nosfir'); ?></p>
                                    </div>
                                </a>
                                <a class="nosfir-resource" href="<?php echo esc_url($this->get_changelog_url()); ?>" target="_blank">
                                    <span class="dashicons dashicons-clipboard"></span>
                                    <div>
                                        <h3><?php _e('Changelog', 'nosfir'); ?></h3>
                                        <p><?php _e('Histórico de versões e melhorias.', 'nosfir'); ?></p>
                                    </div>
                                </a>
                            </div>
                        </article>
                    </section>
                </div>
                <?php
            }
        }
        public function render_dashboard_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <header class="nosfir-dashboard-header">
                    <div class="nosfir-dashboard-brand">
                        <img src="<?php echo esc_url($this->theme_url . '/assets/images/logo.png'); ?>" alt="Nosfir">
                        <div class="nosfir-dashboard-titles">
                            <h1>Nosfir</h1>
                            <span class="nosfir-version">v<?php echo esc_html($this->version); ?></span>
                        </div>
                    </div>
                    <div class="nosfir-dashboard-actions">
                        <a class="button button-primary" href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php _e('Customize', 'nosfir'); ?></a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>"><?php _e('Plugins', 'nosfir'); ?></a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-demo-import')); ?>"><?php _e('Import Demo', 'nosfir'); ?></a>
                    </div>
                </header>
                
                <section class="nosfir-dashboard-grid">
                    <article class="nosfir-card nosfir-card--wide">
                        <h2><?php _e('Visão Geral do Tema', 'nosfir'); ?></h2>
                        <div class="nosfir-features-grid">
                            <?php foreach ($this->get_theme_features() as $feature) : ?>
                                <div class="nosfir-feature-item">
                                    <span class="dashicons dashicons-<?php echo esc_attr($feature['icon']); ?>"></span>
                                    <div>
                                        <h3><?php echo esc_html($feature['title']); ?></h3>
                                        <p><?php echo esc_html($feature['description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                    
                    <article class="nosfir-card">
                        <h2><?php _e('Status do Sistema', 'nosfir'); ?></h2>
                        <?php $sys = $this->get_system_info(); ?>
                        <ul class="nosfir-status-list">
                            <li><strong>WordPress:</strong> <?php echo esc_html($sys['wp_version']); ?></li>
                            <li><strong>PHP:</strong> <?php echo esc_html($sys['php_version']); ?></li>
                            <li><strong>Memória:</strong> <?php echo esc_html($sys['memory_limit']); ?></li>
                            <li><strong>Execução Máx.:</strong> <?php echo esc_html($sys['max_execution_time']); ?>s</li>
                        </ul>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-system-status')); ?>"><?php _e('Ver Detalhes', 'nosfir'); ?></a>
                    </article>
                    
                    <article class="nosfir-card">
                        <h2><?php _e('Integrações', 'nosfir'); ?></h2>
                        <ul class="nosfir-integrations">
                            <li>WooCommerce: <?php echo class_exists('WooCommerce') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                            <li>Jetpack: <?php echo class_exists('Jetpack') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                            <li>Elementor: <?php echo class_exists('\\Elementor\\Plugin') ? '<span class="ok">Ativo</span>' : '<span class="warn">Inativo</span>'; ?></li>
                        </ul>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>"><?php _e('Gerenciar Plugins', 'nosfir'); ?></a>
                    </article>
                    
                    <article class="nosfir-card">
                        <h2><?php _e('Ações Rápidas', 'nosfir'); ?></h2>
                        <div class="nosfir-quick-actions">
                            <a class="button button-primary" href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php _e('Abrir Customizer', 'nosfir'); ?></a>
                            <a class="button" href="<?php echo esc_url(admin_url('post-new.php')); ?>"><?php _e('Novo Post', 'nosfir'); ?></a>
                            <a class="button" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>"><?php _e('Nova Página', 'nosfir'); ?></a>
                        </div>
                    </article>
                    
                    <article class="nosfir-card nosfir-card--wide">
                        <h2><?php _e('Recursos e Suporte', 'nosfir'); ?></h2>
                        <div class="nosfir-resources-grid">
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_documentation_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-book"></span>
                                <div>
                                    <h3><?php _e('Documentação', 'nosfir'); ?></h3>
                                    <p><?php _e('Guias detalhados para cada recurso do tema.', 'nosfir'); ?></p>
                                </div>
                            </a>
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_videos_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-video-alt3"></span>
                                <div>
                                    <h3><?php _e('Tutoriais em Vídeo', 'nosfir'); ?></h3>
                                    <p><?php _e('Passo a passo visual para configurar o site.', 'nosfir'); ?></p>
                                </div>
                            </a>
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_support_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-sos"></span>
                                <div>
                                    <h3><?php _e('Suporte', 'nosfir'); ?></h3>
                                    <p><?php _e('Acesso ao suporte e comunidade.', 'nosfir'); ?></p>
                                </div>
                            </a>
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_changelog_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-clipboard"></span>
                                <div>
                                    <h3><?php _e('Changelog', 'nosfir'); ?></h3>
                                    <p><?php _e('Histórico de versões e melhorias.', 'nosfir'); ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </section>
            </div>
            <?php
        }

        /**
         * Renderiza página de boas-vindas
         *
         * @since 1.0.0
         */
        public function render_welcome_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <header class="nosfir-dashboard-header">
                    <div class="nosfir-dashboard-brand">
                        <img src="<?php echo esc_url($this->theme_url . '/assets/images/logo.png'); ?>" alt="Nosfir">
                        <div class="nosfir-dashboard-titles">
                            <h1>Nosfir</h1>
                            <span class="nosfir-version">v<?php echo esc_html($this->version); ?></span>
                        </div>
                    </div>
                    <div class="nosfir-dashboard-actions">
                        <a class="button button-primary" href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php _e('Customize', 'nosfir'); ?></a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>"><?php _e('Plugins', 'nosfir'); ?></a>
                        <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=nosfir-demo-import')); ?>"><?php _e('Import Demo', 'nosfir'); ?></a>
                    </div>
                </header>

                <section class="nosfir-dashboard-grid">
                    <article class="nosfir-card">
                        <h2><?php _e('Quick Start', 'nosfir'); ?></h2>
                        <div class="nosfir-steps">
                            <div class="nosfir-step">
                                <div class="nosfir-step__number">1</div>
                                <div class="nosfir-step__content">
                                    <h3><?php _e('Install Required Plugins', 'nosfir'); ?></h3>
                                    <p><?php _e('Install and activate the required and recommended plugins.', 'nosfir'); ?></p>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>" class="button button-primary"><?php _e('Install Plugins', 'nosfir'); ?></a>
                                </div>
                            </div>
                            <div class="nosfir-step">
                                <div class="nosfir-step__number">2</div>
                                <div class="nosfir-step__content">
                                    <h3><?php _e('Import Demo Content', 'nosfir'); ?></h3>
                                    <p><?php _e('Import pre-built demos with just one click.', 'nosfir'); ?></p>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=nosfir-demo-import')); ?>" class="button button-primary"><?php _e('Import Demo', 'nosfir'); ?></a>
                                </div>
                            </div>
                            <div class="nosfir-step">
                                <div class="nosfir-step__number">3</div>
                                <div class="nosfir-step__content">
                                    <h3><?php _e('Customize Your Site', 'nosfir'); ?></h3>
                                    <p><?php _e('Use the WordPress Customizer to personalize your site.', 'nosfir'); ?></p>
                                    <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-primary"><?php _e('Open Customizer', 'nosfir'); ?></a>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="nosfir-card">
                        <h2><?php _e('Theme Features', 'nosfir'); ?></h2>
                        <div class="nosfir-features-grid">
                            <?php foreach ($this->get_theme_features() as $feature) : ?>
                                <div class="nosfir-feature-item">
                                    <span class="dashicons dashicons-<?php echo esc_attr($feature['icon']); ?>"></span>
                                    <div>
                                        <h3><?php echo esc_html($feature['title']); ?></h3>
                                        <p><?php echo esc_html($feature['description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>

                    <article class="nosfir-card nosfir-card--wide">
                        <h2><?php _e('Resources', 'nosfir'); ?></h2>
                        <div class="nosfir-resources-grid">
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_documentation_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-book"></span>
                                <div>
                                    <h3><?php _e('Documentation', 'nosfir'); ?></h3>
                                    <p><?php _e('Guides to use the theme.', 'nosfir'); ?></p>
                                </div>
                            </a>
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_videos_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-video-alt3"></span>
                                <div>
                                    <h3><?php _e('Video Tutorials', 'nosfir'); ?></h3>
                                    <p><?php _e('Step-by-step visual guides.', 'nosfir'); ?></p>
                                </div>
                            </a>
                            <a class="nosfir-resource" href="<?php echo esc_url($this->get_support_url()); ?>" target="_blank">
                                <span class="dashicons dashicons-sos"></span>
                                <div>
                                    <h3><?php _e('Support', 'nosfir'); ?></h3>
                                    <p><?php _e('Get help from our team.', 'nosfir'); ?></p>
                                </div>
                            </a>
                        </div>
                    </article>
                </section>

                <footer class="nosfir-welcome-footer">
                    <div class="nosfir-footer-info">
                        <p>
                            <?php
                            printf(
                                __('Made with %s by %s', 'nosfir'),
                                '<span class="dashicons dashicons-heart"></span>',
                                '<a href="' . esc_url('https://davidcreator.com') . '" target="_blank">David Creator</a>'
                            );
                            ?>
                        </p>
                    </div>
                    <div class="nosfir-footer-social">
                        <a href="<?php echo esc_url($this->get_social_url('facebook')); ?>" target="_blank"><span class="dashicons dashicons-facebook"></span></a>
                        <a href="<?php echo esc_url($this->get_social_url('twitter')); ?>" target="_blank"><span class="dashicons dashicons-twitter"></span></a>
                        <a href="<?php echo esc_url($this->get_social_url('instagram')); ?>" target="_blank"><span class="dashicons dashicons-instagram"></span></a>
                    </div>
                </footer>
            </div>
            <?php
        }

        /**
         * Renderiza página de importação de demos
         *
         * @since 1.0.0
         */
        public function render_demo_import_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <div class="nosfir-page-header">
                    <h1><?php _e('Demo Import', 'nosfir'); ?></h1>
                    <p><?php _e('Import any of our professionally designed demos with just one click.', 'nosfir'); ?></p>
                </div>

                <div class="nosfir-demos-filter">
                    <div class="nosfir-filter-tabs">
                        <button class="nosfir-filter-tab active" data-filter="all">
                            <?php _e('All', 'nosfir'); ?>
                        </button>
                        <button class="nosfir-filter-tab" data-filter="business">
                            <?php _e('Business', 'nosfir'); ?>
                        </button>
                        <button class="nosfir-filter-tab" data-filter="portfolio">
                            <?php _e('Portfolio', 'nosfir'); ?>
                        </button>
                        <button class="nosfir-filter-tab" data-filter="blog">
                            <?php _e('Blog', 'nosfir'); ?>
                        </button>
                        <button class="nosfir-filter-tab" data-filter="shop">
                            <?php _e('Shop', 'nosfir'); ?>
                        </button>
                    </div>
                    <div class="nosfir-filter-search">
                        <input type="search" placeholder="<?php esc_attr_e('Search demos...', 'nosfir'); ?>" class="nosfir-demo-search">
                    </div>
                </div>

                <div class="nosfir-demos-grid">
                    <?php
                    $demos = $this->get_available_demos();
                    foreach ($demos as $demo_id => $demo) : ?>
                        <div class="nosfir-demo-item" data-category="<?php echo esc_attr($demo['category']); ?>" data-name="<?php echo esc_attr($demo['name']); ?>">
                            <div class="nosfir-demo-preview">
                                <img src="<?php echo esc_url($demo['preview']); ?>" alt="<?php echo esc_attr($demo['name']); ?>">
                                <div class="nosfir-demo-overlay">
                                    <a href="<?php echo esc_url($demo['preview_url']); ?>" class="nosfir-demo-preview-btn" target="_blank">
                                        <?php _e('Preview', 'nosfir'); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="nosfir-demo-info">
                                <h3><?php echo esc_html($demo['name']); ?></h3>
                                <div class="nosfir-demo-meta">
                                    <?php if ($demo['is_premium'] && !$this->is_premium()) : ?>
                                        <span class="nosfir-demo-badge nosfir-demo-badge--premium">
                                            <?php _e('Premium', 'nosfir'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($demo['is_new']) : ?>
                                        <span class="nosfir-demo-badge nosfir-demo-badge--new">
                                            <?php _e('New', 'nosfir'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="nosfir-demo-actions">
                                    <?php if ($demo['is_premium'] && !$this->is_premium()) : ?>
                                        <a href="<?php echo esc_url($this->get_premium_url()); ?>" class="button button-primary" target="_blank">
                                            <?php _e('Get Premium', 'nosfir'); ?>
                                        </a>
                                    <?php else : ?>
                                        <button class="button button-primary nosfir-import-demo" 
                                                data-demo-id="<?php echo esc_attr($demo_id); ?>"
                                                data-demo-name="<?php echo esc_attr($demo['name']); ?>">
                                            <?php _e('Import', 'nosfir'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Import Progress Modal -->
                <div class="nosfir-import-modal" id="nosfir-import-modal" style="display:none;">
                    <div class="nosfir-import-modal-content">
                        <h2><?php _e('Importing Demo Content', 'nosfir'); ?></h2>
                        <div class="nosfir-import-progress">
                            <div class="nosfir-progress-bar">
                                <div class="nosfir-progress-bar-fill" style="width: 0;"></div>
                            </div>
                            <div class="nosfir-import-status">
                                <p class="nosfir-import-message"><?php _e('Preparing import...', 'nosfir'); ?></p>
                            </div>
                        </div>
                        <div class="nosfir-import-log">
                            <h4><?php _e('Import Log', 'nosfir'); ?></h4>
                            <div class="nosfir-log-content"></div>
                        </div>
                        <div class="nosfir-import-actions" style="display:none;">
                            <button class="button button-primary" onclick="location.reload();">
                                <?php _e('Close', 'nosfir'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Renderiza página de plugins recomendados
         *
         * @since 1.0.0
         */
        public function render_plugins_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <div class="nosfir-page-header">
                    <h1><?php _e('Recommended Plugins', 'nosfir'); ?></h1>
                    <p><?php _e('Enhance your website functionality with these carefully selected plugins.', 'nosfir'); ?></p>
                </div>

                <div class="nosfir-plugins-tabs">
                    <nav class="nosfir-tabs-nav">
                        <a href="#required" class="nosfir-tab-link active"><?php _e('Required', 'nosfir'); ?></a>
                        <a href="#recommended" class="nosfir-tab-link"><?php _e('Recommended', 'nosfir'); ?></a>
                        <a href="#premium" class="nosfir-tab-link"><?php _e('Premium', 'nosfir'); ?></a>
                    </nav>

                    <!-- Required Plugins -->
                    <div id="required" class="nosfir-tab-content active">
                        <div class="nosfir-plugins-grid">
                            <?php
                            $required_plugins = $this->get_required_plugins();
                            foreach ($required_plugins as $plugin) :
                                $this->render_plugin_card($plugin);
                            endforeach;
                            ?>
                        </div>
                    </div>

                    <!-- Recommended Plugins -->
                    <div id="recommended" class="nosfir-tab-content">
                        <div class="nosfir-plugins-grid">
                            <?php
                            $recommended_plugins = $this->get_recommended_plugins();
                            foreach ($recommended_plugins as $plugin) :
                                $this->render_plugin_card($plugin);
                            endforeach;
                            ?>
                        </div>
                    </div>

                    <!-- Premium Plugins -->
                    <div id="premium" class="nosfir-tab-content">
                        <div class="nosfir-plugins-grid">
                            <?php
                            $premium_plugins = $this->get_premium_plugins();
                            foreach ($premium_plugins as $plugin) :
                                $this->render_plugin_card($plugin);
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Renderiza card de plugin
         *
         * @param array $plugin
         * @since 1.0.0
         */
        private function render_plugin_card($plugin) {
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $plugin_file = isset($plugin['file']) ? $plugin['file'] : '';
            if ($plugin_file && strpos($plugin_file, '/') === false) {
                $plugin_file = $plugin['slug'] . '/' . $plugin_file;
            }
            $plugins = get_plugins();
            $is_installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_file) || isset($plugins[$plugin_file]);
            $is_active = $is_installed && $plugin_file && is_plugin_active($plugin_file);
            ?>
            <div class="nosfir-plugin-card">
                <?php if (!empty($plugin['thumbnail'])) : ?>
                    <div class="nosfir-plugin-thumbnail">
                        <img src="<?php echo esc_url($plugin['thumbnail']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="nosfir-plugin-info">
                    <h3><?php echo esc_html($plugin['name']); ?></h3>
                    
                    <?php if (!empty($plugin['version'])) : ?>
                        <span class="nosfir-plugin-version">v<?php echo esc_html($plugin['version']); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($plugin['author'])) : ?>
                        <span class="nosfir-plugin-author">
                            <?php printf(__('by %s', 'nosfir'), esc_html($plugin['author'])); ?>
                        </span>
                    <?php endif; ?>
                    
                    <p class="nosfir-plugin-description">
                        <?php echo esc_html($plugin['description']); ?>
                    </p>
                    
                    <?php if (!empty($plugin['required'])) : ?>
                        <span class="nosfir-plugin-badge nosfir-plugin-badge--required">
                            <?php _e('Required', 'nosfir'); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="nosfir-plugin-actions">
                    <?php if ($is_active) : ?>
                        <button class="button button-disabled" disabled>
                            <?php _e('Active', 'nosfir'); ?>
                        </button>
                    <?php elseif ($is_installed) : ?>
                        <button class="button button-primary nosfir-activate-plugin" 
                                data-plugin="<?php echo esc_attr($plugin_file); ?>"
                                data-slug="<?php echo esc_attr($plugin['slug']); ?>">
                            <?php _e('Activate', 'nosfir'); ?>
                        </button>
                    <?php else : ?>
                        <button class="button button-primary nosfir-install-plugin" 
                                data-slug="<?php echo esc_attr($plugin['slug']); ?>"
                                data-file="<?php echo esc_attr($plugin_file ?: ($plugin['slug'] . '/' . ($plugin['file'] ?? $plugin['slug'] . '.php'))); ?>">
                            <?php _e('Install & Activate', 'nosfir'); ?>
                        </button>
                    <?php endif; ?>
                    
                    <?php if (!empty($plugin['info_url'])) : ?>
                        <a href="<?php echo esc_url($plugin['info_url']); ?>" class="button" target="_blank">
                            <?php _e('More Info', 'nosfir'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }

        /**
         * Renderiza página de configurações
         *
         * @since 1.0.0
         */
        public function render_settings_page() {
            // Salva configurações se enviadas
            if (isset($_POST['nosfir_save_settings']) && wp_verify_nonce($_POST['nosfir_settings_nonce'], 'nosfir_save_settings')) {
                $this->save_settings();
            }

            $settings = get_option('nosfir_settings', array());
            ?>
            <div class="nosfir-admin-wrap">
                <div class="nosfir-page-header">
                    <h1><?php _e('Theme Settings', 'nosfir'); ?></h1>
                    <p><?php _e('Configure advanced theme settings and options.', 'nosfir'); ?></p>
                </div>

                <form method="post" action="" class="nosfir-settings-form">
                    <?php wp_nonce_field('nosfir_save_settings', 'nosfir_settings_nonce'); ?>
                    
                    <div class="nosfir-settings-tabs">
                        <nav class="nosfir-tabs-nav">
                            <a href="#general" class="nosfir-tab-link active"><?php _e('General', 'nosfir'); ?></a>
                            <a href="#performance" class="nosfir-tab-link"><?php _e('Performance', 'nosfir'); ?></a>
                            <a href="#advanced" class="nosfir-tab-link"><?php _e('Advanced', 'nosfir'); ?></a>
                            <a href="#tools" class="nosfir-tab-link"><?php _e('Tools', 'nosfir'); ?></a>
                        </nav>

                        <!-- General Settings -->
                        <div id="general" class="nosfir-tab-content active">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e('Enable Preloader', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[preloader]" value="1" 
                                                   <?php checked(isset($settings['preloader']) ? $settings['preloader'] : 0, 1); ?>>
                                            <?php _e('Show loading animation when page loads', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Smooth Scroll', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[smooth_scroll]" value="1" 
                                                   <?php checked(isset($settings['smooth_scroll']) ? $settings['smooth_scroll'] : 0, 1); ?>>
                                            <?php _e('Enable smooth scrolling effect', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Back to Top Button', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[back_to_top]" value="1" 
                                                   <?php checked(isset($settings['back_to_top']) ? $settings['back_to_top'] : 0, 1); ?>>
                                            <?php _e('Show back to top button', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Enable Animations', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[animations]" value="1" 
                                                   <?php checked(isset($settings['animations']) ? $settings['animations'] : 0, 1); ?>>
                                            <?php _e('Enable scroll animations', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Performance Settings -->
                        <div id="performance" class="nosfir-tab-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e('Lazy Load Images', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[lazy_load]" value="1" 
                                                   <?php checked(isset($settings['lazy_load']) ? $settings['lazy_load'] : 0, 1); ?>>
                                            <?php _e('Enable lazy loading for images', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Minify CSS', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[minify_css]" value="1" 
                                                   <?php checked(isset($settings['minify_css']) ? $settings['minify_css'] : 0, 1); ?>>
                                            <?php _e('Minify theme CSS files', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Minify JavaScript', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[minify_js]" value="1" 
                                                   <?php checked(isset($settings['minify_js']) ? $settings['minify_js'] : 0, 1); ?>>
                                            <?php _e('Minify theme JavaScript files', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Disable Emoji', 'nosfir'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="nosfir_settings[disable_emoji]" value="1" 
                                                   <?php checked(isset($settings['disable_emoji']) ? $settings['disable_emoji'] : 0, 1); ?>>
                                            <?php _e('Disable WordPress emoji scripts', 'nosfir'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Advanced Settings -->
                        <div id="advanced" class="nosfir-tab-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e('Custom CSS', 'nosfir'); ?></th>
                                    <td>
                                        <textarea name="nosfir_settings[custom_css]" rows="10" cols="50" class="large-text code"><?php 
                                            echo esc_textarea(isset($settings['custom_css']) ? $settings['custom_css'] : ''); 
                                        ?></textarea>
                                        <p class="description"><?php _e('Add custom CSS code here.', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Custom JavaScript', 'nosfir'); ?></th>
                                    <td>
                                        <textarea name="nosfir_settings[custom_js]" rows="10" cols="50" class="large-text code"><?php 
                                            echo esc_textarea(isset($settings['custom_js']) ? $settings['custom_js'] : ''); 
                                        ?></textarea>
                                        <p class="description"><?php _e('Add custom JavaScript code here.', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Google Analytics', 'nosfir'); ?></th>
                                    <td>
                                        <input type="text" name="nosfir_settings[google_analytics]" value="<?php 
                                            echo esc_attr(isset($settings['google_analytics']) ? $settings['google_analytics'] : ''); 
                                        ?>" class="regular-text" placeholder="UA-XXXXXXXXX-X">
                                        <p class="description"><?php _e('Enter your Google Analytics tracking ID.', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Tools -->
                        <div id="tools" class="nosfir-tab-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e('Export Settings', 'nosfir'); ?></th>
                                    <td>
                                        <button type="button" class="button nosfir-export-settings">
                                            <?php _e('Export Settings', 'nosfir'); ?>
                                        </button>
                                        <p class="description"><?php _e('Export theme settings to a JSON file.', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Import Settings', 'nosfir'); ?></th>
                                    <td>
                                        <input type="file" name="import_settings" accept=".json">
                                        <p class="description"><?php _e('Import settings from a JSON file.', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Reset Settings', 'nosfir'); ?></th>
                                    <td>
                                        <button type="button" class="button button-link-delete nosfir-reset-settings">
                                            <?php _e('Reset All Settings', 'nosfir'); ?>
                                        </button>
                                        <p class="description"><?php _e('Reset all theme settings to default. This action cannot be undone!', 'nosfir'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <p class="submit">
                        <input type="submit" name="nosfir_save_settings" class="button-primary" value="<?php esc_attr_e('Save Settings', 'nosfir'); ?>">
                    </p>
                </form>
            </div>
            <?php
        }

        /**
         * Renderiza página de status do sistema
         *
         * @since 1.0.0
         */
        public function render_system_status_page() {
            $system_info = $this->get_system_info();
            ?>
            <div class="nosfir-admin-wrap">
                <div class="nosfir-page-header">
                    <h1><?php _e('System Status', 'nosfir'); ?></h1>
                    <p><?php _e('View important information about your WordPress installation and server environment.', 'nosfir'); ?></p>
                </div>

                <div class="nosfir-system-status">
                    <!-- WordPress Environment -->
                    <div class="nosfir-status-section">
                        <h2><?php _e('WordPress Environment', 'nosfir'); ?></h2>
                        <table class="nosfir-status-table">
                            <tr>
                                <td><?php _e('Home URL', 'nosfir'); ?></td>
                                <td><?php echo esc_url(home_url()); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Site URL', 'nosfir'); ?></td>
                                <td><?php echo esc_url(site_url()); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('WordPress Version', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['wp_version']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('WordPress Multisite', 'nosfir'); ?></td>
                                <td><?php echo is_multisite() ? __('Yes', 'nosfir') : __('No', 'nosfir'); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('WordPress Debug Mode', 'nosfir'); ?></td>
                                <td><?php echo WP_DEBUG ? __('Enabled', 'nosfir') : __('Disabled', 'nosfir'); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Language', 'nosfir'); ?></td>
                                <td><?php echo esc_html(get_locale()); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Server Environment -->
                    <div class="nosfir-status-section">
                        <h2><?php _e('Server Environment', 'nosfir'); ?></h2>
                        <table class="nosfir-status-table">
                            <tr>
                                <td><?php _e('Server Info', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['server_info']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('PHP Version', 'nosfir'); ?></td>
                                <td>
                                    <?php 
                                    echo esc_html($system_info['php_version']);
                                    if (version_compare($system_info['php_version'], '7.4', '<')) {
                                        echo ' <span class="nosfir-status-warning">' . __('(Minimum 7.4 recommended)', 'nosfir') . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e('PHP Memory Limit', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['memory_limit']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('PHP Max Execution Time', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['max_execution_time']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('PHP Max Input Vars', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['max_input_vars']); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Max Upload Size', 'nosfir'); ?></td>
                                <td><?php echo esc_html($system_info['upload_max_filesize']); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Active Theme -->
                    <div class="nosfir-status-section">
                        <h2><?php _e('Active Theme', 'nosfir'); ?></h2>
                        <table class="nosfir-status-table">
                            <tr>
                                <td><?php _e('Name', 'nosfir'); ?></td>
                                <td><?php echo esc_html($this->theme->get('Name')); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Version', 'nosfir'); ?></td>
                                <td><?php echo esc_html($this->version); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Author', 'nosfir'); ?></td>
                                <td><?php echo esc_html($this->theme->get('Author')); ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Child Theme', 'nosfir'); ?></td>
                                <td><?php echo is_child_theme() ? __('Yes', 'nosfir') : __('No', 'nosfir'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Active Plugins -->
                    <div class="nosfir-status-section">
                        <h2><?php _e('Active Plugins', 'nosfir'); ?> (<?php echo count($system_info['active_plugins']); ?>)</h2>
                        <table class="nosfir-status-table">
                            <?php foreach ($system_info['active_plugins'] as $plugin) : ?>
                                <tr>
                                    <td><?php echo esc_html($plugin['name']); ?></td>
                                    <td><?php echo esc_html($plugin['version']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <!-- Actions -->
                    <div class="nosfir-status-actions">
                        <button class="button button-primary nosfir-copy-system-info">
                            <?php _e('Copy System Info', 'nosfir'); ?>
                        </button>
                        <button class="button nosfir-download-system-info">
                            <?php _e('Download System Info', 'nosfir'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Renderiza página de suporte
         *
         * @since 1.0.0
         */
        public function render_support_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <div class="nosfir-page-header">
                    <h1><?php _e('Support', 'nosfir'); ?></h1>
                    <p><?php _e('Get help and support for Nosfir theme.', 'nosfir'); ?></p>
                </div>

                <div class="nosfir-support-content">
                    <div class="nosfir-support-boxes">
                        <!-- Documentation -->
                        <div class="nosfir-support-box">
                            <div class="nosfir-support-icon">
                                <span class="dashicons dashicons-book-alt"></span>
                            </div>
                            <h3><?php _e('Documentation', 'nosfir'); ?></h3>
                            <p><?php _e('Comprehensive guides and tutorials to help you get the most out of Nosfir.', 'nosfir'); ?></p>
                            <a href="<?php echo esc_url($this->get_documentation_url()); ?>" class="button button-primary" target="_blank">
                                <?php _e('View Documentation', 'nosfir'); ?>
                            </a>
                        </div>

                        <!-- Support Forum -->
                        <div class="nosfir-support-box">
                            <div class="nosfir-support-icon">
                                <span class="dashicons dashicons-groups"></span>
                            </div>
                            <h3><?php _e('Support Forum', 'nosfir'); ?></h3>
                            <p><?php _e('Join our community forum to get help from other users and our support team.', 'nosfir'); ?></p>
                            <a href="<?php echo esc_url($this->get_support_url()); ?>" class="button button-primary" target="_blank">
                                <?php _e('Visit Forum', 'nosfir'); ?>
                            </a>
                        </div>

                        <!-- Video Tutorials -->
                        <div class="nosfir-support-box">
                            <div class="nosfir-support-icon">
                                <span class="dashicons dashicons-video-alt3"></span>
                            </div>
                            <h3><?php _e('Video Tutorials', 'nosfir'); ?></h3>
                            <p><?php _e('Watch step-by-step video tutorials on how to use and customize Nosfir.', 'nosfir'); ?></p>
                            <a href="<?php echo esc_url($this->get_videos_url()); ?>" class="button button-primary" target="_blank">
                                <?php _e('Watch Videos', 'nosfir'); ?>
                            </a>
                        </div>

                        <!-- Contact Support -->
                        <?php if ($this->is_premium()) : ?>
                            <div class="nosfir-support-box">
                                <div class="nosfir-support-icon">
                                    <span class="dashicons dashicons-email-alt"></span>
                                </div>
                                <h3><?php _e('Premium Support', 'nosfir'); ?></h3>
                                <p><?php _e('Get priority support directly from our development team.', 'nosfir'); ?></p>
                                <a href="<?php echo esc_url($this->get_contact_url()); ?>" class="button button-primary" target="_blank">
                                    <?php _e('Contact Support', 'nosfir'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- FAQs -->
                    <div class="nosfir-support-faqs">
                        <h2><?php _e('Frequently Asked Questions', 'nosfir'); ?></h2>
                        <?php
                        $faqs = $this->get_faqs();
                        foreach ($faqs as $faq) : ?>
                            <div class="nosfir-faq-item">
                                <h4 class="nosfir-faq-question">
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    <?php echo esc_html($faq['question']); ?>
                                </h4>
                                <div class="nosfir-faq-answer">
                                    <?php echo wp_kses_post($faq['answer']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Helpers e métodos auxiliares
         */

        /**
         * Verifica se é uma página admin do Nosfir
         *
         * @param string $hook_suffix
         * @return bool
         */
        private function is_nosfir_admin_page($hook_suffix) {
            $nosfir_pages = array(
                'toplevel_page_nosfir-dashboard',
                'nosfir_page_nosfir-welcome',
                'nosfir_page_nosfir-demo-import',
                'nosfir_page_nosfir-plugins',
                'nosfir_page_nosfir-settings',
                'nosfir_page_nosfir-system-status',
                'nosfir_page_nosfir-support',
                'nosfir_page_nosfir-tours'
            );

            return in_array($hook_suffix, $nosfir_pages);
        }

        /**
         * Obtém ícone do menu
         *
         * @return string
         */
        private function get_menu_icon() {
            return 'data:image/svg+xml;base64,' . base64_encode(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a0a5aa">
                    <path d="M10 2C5.58 2 2 5.58 2 10s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                    <circle cx="10" cy="10" r="3"/>
                </svg>'
            );
        }

        /**
         * Obtém features do tema
         *
         * @return array
         */
        private function get_theme_features() {
            return array(
                array(
                    'icon' => 'admin-customizer',
                    'title' => __('Live Customizer', 'nosfir'),
                    'description' => __('Customize your site in real-time with our powerful theme customizer.', 'nosfir')
                ),
                array(
                    'icon' => 'layout',
                    'title' => __('Multiple Layouts', 'nosfir'),
                    'description' => __('Choose from various layouts for pages, posts, and archives.', 'nosfir')
                ),
                array(
                    'icon' => 'smartphone',
                    'title' => __('Mobile Responsive', 'nosfir'),
                    'description' => __('Fully responsive design that looks great on all devices.', 'nosfir')
                ),
                array(
                    'icon' => 'performance',
                    'title' => __('Optimized Performance', 'nosfir'),
                    'description' => __('Lightning-fast loading times with optimized code.', 'nosfir')
                ),
                array(
                    'icon' => 'translation',
                    'title' => __('Translation Ready', 'nosfir'),
                    'description' => __('Fully translatable and RTL language support.', 'nosfir')
                ),
                array(
                    'icon' => 'cart',
                    'title' => __('WooCommerce Ready', 'nosfir'),
                    'description' => __('Full compatibility with WooCommerce for online stores.', 'nosfir')
                )
            );
        }

        // Métodos auxiliares adicionais...

        private function get_available_demos() {
            // Retorna demos disponíveis
            return array(
                'demo1' => array(
                    'name' => 'Business',
                    'category' => 'business',
                    'preview' => $this->theme_url . '/assets/images/demos/demo1.jpg',
                    'preview_url' => 'https://demo.nosfir.com/business',
                    'is_premium' => false,
                    'is_new' => true
                ),
                // Mais demos...
            );
        }

        private function get_required_plugins() {
            return array(
                array(
                    'name' => 'Elementor',
                    'slug' => 'elementor',
                    'file' => 'elementor.php',
                    'required' => true,
                    'description' => 'The most advanced frontend drag & drop page builder.',
                    'thumbnail' => $this->theme_url . '/assets/images/plugins/elementor.png'
                )
            );
        }

        private function get_recommended_plugins() {
            return array(
                array(
                    'name' => 'WooCommerce',
                    'slug' => 'woocommerce',
                    'file' => 'woocommerce.php',
                    'description' => 'The world\'s most popular eCommerce solution.',
                    'thumbnail' => $this->theme_url . '/assets/images/plugins/woocommerce.png'
                )
            );
        }

        private function get_premium_plugins() {
            return array();
        }

        private function get_system_info() {
            return array(
                'wp_version' => get_bloginfo('version'),
                'php_version' => PHP_VERSION,
                'server_info' => $_SERVER['SERVER_SOFTWARE'],
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'max_input_vars' => ini_get('max_input_vars'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'active_plugins' => $this->get_active_plugins_info()
            );
        }

        private function get_active_plugins_info() {
            $plugins = array();
            $active_plugins = get_option('active_plugins');
            
            foreach ($active_plugins as $plugin) {
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $plugins[] = array(
                    'name' => $plugin_data['Name'],
                    'version' => $plugin_data['Version']
                );
            }
            
            return $plugins;
        }

        private function get_faqs() {
            return array(
                array(
                    'question' => __('How do I install the theme?', 'nosfir'),
                    'answer' => __('You can install the theme through WordPress admin panel. Go to Appearance > Themes > Add New > Upload Theme.', 'nosfir')
                ),
                array(
                    'question' => __('Is the theme compatible with page builders?', 'nosfir'),
                    'answer' => __('Yes, Nosfir is fully compatible with popular page builders like Elementor, Beaver Builder, and Visual Composer.', 'nosfir')
                )
            );
        }

        // URLs helpers
        private function get_documentation_url() {
            return 'https://docs.nosfir.com';
        }

        private function get_support_url() {
            return 'https://support.nosfir.com';
        }

        private function get_videos_url() {
            return 'https://youtube.com/nosfir';
        }

        private function get_premium_url() {
            return 'https://nosfir.com/premium';
        }

        private function get_changelog_url() {
            return 'https://nosfir.com/changelog';
        }

        private function get_contact_url() {
            return 'https://nosfir.com/contact';
        }

        private function get_social_url($network) {
            $urls = array(
                'facebook' => 'https://facebook.com/nosfir',
                'twitter' => 'https://twitter.com/nosfir',
                'instagram' => 'https://instagram.com/nosfir'
            );
            return isset($urls[$network]) ? $urls[$network] : '#';
        }

        /**
         * Verifica se é versão premium
         */
        private function is_premium() {
            return false;
        }

        /**
         * Admin notices
         */
        public function admin_notices() {
            // Implementar notices
        }

        /**
         * Footer text
         */
        public function admin_footer_text($text) {
            if ($this->is_nosfir_admin_page(get_current_screen()->id)) {
                return sprintf(
                    __('Thank you for using %s theme. Created with ❤ by %s', 'nosfir'),
                    '<strong>Nosfir</strong>',
                    '<a href="https://davidcreator.com" target="_blank">David Creator</a>'
                );
            }
            return $text;
        }

        /**
         * Executa no head do admin
         */
        public function admin_head() {
            // Código customizado para o head do admin
        }

        /**
         * Dismiss admin notice
         */
        public function dismiss_notice() {
            check_ajax_referer('nosfir-admin-nonce', 'nonce');
            // Logic to dismiss notice
            wp_send_json_success();
        }

        /**
         * AJAX import demo
         */
        public function ajax_import_demo() {
            check_ajax_referer('nosfir-admin-nonce', 'nonce');
            // Logic to import demo
            wp_send_json_success();
        }

        /**
         * Add dashboard widgets
         */
        public function add_dashboard_widgets() {
            // Logic to add dashboard widgets
        }

        /**
         * Check for updates
         */
        public function check_for_updates($transient) {
            // Logic to check for updates
            return $transient;
        }

        /**
         * Redirect on activation
         */
        public function redirect_on_activation() {
            // Logic to redirect after activation
        }

        /**
         * Add meta boxes
         */
        public function add_meta_boxes() {
            // Logic to add meta boxes
        }

        /**
         * Save meta boxes
         */
        public function save_meta_boxes($post_id, $post) {
            // Logic to save meta boxes
        }

        // Mais métodos conforme necessário...
    }

endif;

// Inicializa a classe
return Nosfir_Admin::get_instance();
