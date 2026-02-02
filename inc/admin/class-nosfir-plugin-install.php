<?php
/**
 * Nosfir Plugin Installer Class
 *
 * Gerencia a instalação, ativação e atualização de plugins recomendados
 * com interface AJAX e feedback em tempo real.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_Plugin_Installer')) :

    /**
     * Classe de instalação de plugins do Nosfir
     */
    class Nosfir_Plugin_Installer {

        /**
         * Instance única da classe
         *
         * @var Nosfir_Plugin_Installer
         */
        private static $instance = null;

        /**
         * Lista de plugins requeridos
         *
         * @var array
         */
        private $required_plugins = array();

        /**
         * Lista de plugins recomendados
         *
         * @var array
         */
        private $recommended_plugins = array();

        /**
         * Lista de plugins premium
         *
         * @var array
         */
        private $premium_plugins = array();

        /**
         * Diretório de plugins
         *
         * @var string
         */
        private $plugins_dir;

        /**
         * Cache de status dos plugins
         *
         * @var array
         */
        private $plugins_status_cache = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_Plugin_Installer
         */
        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Construtor
         */
        public function __construct() {
            $this->plugins_dir = WP_PLUGIN_DIR;
            $this->init_plugins_list();
            $this->init_hooks();
        }

        /**
         * Inicializa lista de plugins
         */
        private function init_plugins_list() {
            // Plugins requeridos
            $this->required_plugins = array(
                'elementor' => array(
                    'name' => 'Elementor',
                    'slug' => 'elementor',
                    'file' => 'elementor/elementor.php',
                    'source' => 'wordpress',
                    'required' => true,
                    'version' => '3.0.0',
                    'description' => __('The most advanced frontend drag & drop page builder.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/elementor.png',
                    'info_url' => 'https://wordpress.org/plugins/elementor/',
                    'category' => 'page-builder'
                ),
                'nosfir-companion' => array(
                    'name' => 'Nosfir Companion',
                    'slug' => 'nosfir-companion',
                    'file' => 'nosfir-companion/nosfir-companion.php',
                    'source' => get_template_directory_uri() . '/inc/plugins/nosfir-companion.zip',
                    'required' => true,
                    'version' => '1.0.0',
                    'description' => __('Essential companion plugin for Nosfir theme with custom widgets and features.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/nosfir-companion.png',
                    'category' => 'theme'
                )
            );

            // Plugins recomendados
            $this->recommended_plugins = array(
                'woocommerce' => array(
                    'name' => 'WooCommerce',
                    'slug' => 'woocommerce',
                    'file' => 'woocommerce/woocommerce.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'version' => '5.0.0',
                    'description' => __('The world\'s most customizable eCommerce platform for building your online business.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/woocommerce.png',
                    'info_url' => 'https://wordpress.org/plugins/woocommerce/',
                    'category' => 'ecommerce'
                ),
                'contact-form-7' => array(
                    'name' => 'Contact Form 7',
                    'slug' => 'contact-form-7',
                    'file' => 'contact-form-7/wp-contact-form-7.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'description' => __('Just another contact form plugin. Simple but flexible.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/contact-form-7.png',
                    'info_url' => 'https://wordpress.org/plugins/contact-form-7/',
                    'category' => 'forms'
                ),
                'yoast-seo' => array(
                    'name' => 'Yoast SEO',
                    'slug' => 'wordpress-seo',
                    'file' => 'wordpress-seo/wp-seo.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'description' => __('The #1 WordPress SEO plugin. Improve your WordPress SEO and get more visitors.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/yoast-seo.png',
                    'info_url' => 'https://wordpress.org/plugins/wordpress-seo/',
                    'category' => 'seo'
                ),
                'mailchimp' => array(
                    'name' => 'Mailchimp for WordPress',
                    'slug' => 'mailchimp-for-wp',
                    'file' => 'mailchimp-for-wp/mailchimp-for-wp.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'description' => __('Mailchimp for WordPress, the #1 Mailchimp plugin.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/mailchimp.png',
                    'info_url' => 'https://wordpress.org/plugins/mailchimp-for-wp/',
                    'category' => 'marketing'
                ),
                'wordfence' => array(
                    'name' => 'Wordfence Security',
                    'slug' => 'wordfence',
                    'file' => 'wordfence/wordfence.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'description' => __('The most comprehensive WordPress security plugin.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/wordfence.png',
                    'info_url' => 'https://wordpress.org/plugins/wordfence/',
                    'category' => 'security'
                ),
                'wp-smushit' => array(
                    'name' => 'Smush',
                    'slug' => 'wp-smushit',
                    'file' => 'wp-smushit/wp-smush.php',
                    'source' => 'wordpress',
                    'required' => false,
                    'description' => __('Compress and optimize images with lazy load, WebP conversion, and more.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/smush.png',
                    'info_url' => 'https://wordpress.org/plugins/wp-smushit/',
                    'category' => 'optimization'
                )
            );

            // Plugins premium
            $this->premium_plugins = array(
                'elementor-pro' => array(
                    'name' => 'Elementor Pro',
                    'slug' => 'elementor-pro',
                    'file' => 'elementor-pro/elementor-pro.php',
                    'source' => 'external',
                    'external_url' => 'https://elementor.com/pro/',
                    'required' => false,
                    'description' => __('The most advanced website builder plugin for WordPress.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/elementor-pro.png',
                    'category' => 'page-builder',
                    'is_premium' => true
                ),
                'acf-pro' => array(
                    'name' => 'Advanced Custom Fields Pro',
                    'slug' => 'acf-pro',
                    'file' => 'advanced-custom-fields-pro/acf.php',
                    'source' => 'external',
                    'external_url' => 'https://www.advancedcustomfields.com/pro/',
                    'required' => false,
                    'description' => __('Customize WordPress with powerful, professional and intuitive fields.', 'nosfir'),
                    'thumbnail' => get_template_directory_uri() . '/assets/images/plugins/acf-pro.png',
                    'category' => 'development',
                    'is_premium' => true
                )
            );

            // Permite que outros plugins/temas modifiquem as listas
            $this->required_plugins = apply_filters('nosfir_required_plugins', $this->required_plugins);
            $this->recommended_plugins = apply_filters('nosfir_recommended_plugins', $this->recommended_plugins);
            $this->premium_plugins = apply_filters('nosfir_premium_plugins', $this->premium_plugins);
        }

        /**
         * Inicializa hooks
         */
        private function init_hooks() {
            // Scripts e estilos
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            
            // AJAX handlers
            add_action('wp_ajax_nosfir_install_plugin', array($this, 'ajax_install_plugin'));
            add_action('wp_ajax_nosfir_activate_plugin', array($this, 'ajax_activate_plugin'));
            add_action('wp_ajax_nosfir_deactivate_plugin', array($this, 'ajax_deactivate_plugin'));
            add_action('wp_ajax_nosfir_update_plugin', array($this, 'ajax_update_plugin'));
            add_action('wp_ajax_nosfir_bulk_install_plugins', array($this, 'ajax_bulk_install_plugins'));
            add_action('wp_ajax_nosfir_check_plugin_status', array($this, 'ajax_check_plugin_status'));
            
            // Admin notices
            add_action('admin_notices', array($this, 'required_plugins_notice'));
            
            // Adiciona link de ação nos plugins
            add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
            
            // Verifica atualizações de plugins bundled
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_bundled_plugins_updates'));
        }

        /**
         * Enfileira scripts e estilos
         */
        public function enqueue_scripts($hook_suffix) {
            // Apenas nas páginas relevantes
            $allowed_pages = array(
                'toplevel_page_nosfir-dashboard',
                'nosfir_page_nosfir-plugins',
                'nosfir_page_nosfir-demo-import',
                'plugins.php'
            );

            if (!in_array($hook_suffix, $allowed_pages)) {
                return;
            }

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            // CSS
            wp_enqueue_style(
                'nosfir-plugin-installer',
                get_template_directory_uri() . '/assets/css/admin/plugin-installer' . $suffix . '.css',
                array(),
                wp_get_theme()->get('Version')
            );

            // JavaScript
            wp_enqueue_script(
                'nosfir-plugin-installer',
                get_template_directory_uri() . '/assets/js/admin/plugin-installer' . $suffix . '.js',
                array('jquery', 'wp-util', 'updates'),
                wp_get_theme()->get('Version'),
                true
            );

            // Localização
            wp_localize_script('nosfir-plugin-installer', 'nosfir_plugin_installer', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfir-plugin-installer'),
                'strings' => array(
                    'installing' => __('Installing...', 'nosfir'),
                    'activating' => __('Activating...', 'nosfir'),
                    'deactivating' => __('Deactivating...', 'nosfir'),
                    'updating' => __('Updating...', 'nosfir'),
                    'activated' => __('Activated', 'nosfir'),
                    'deactivated' => __('Deactivated', 'nosfir'),
                    'updated' => __('Updated', 'nosfir'),
                    'install_failed' => __('Installation failed', 'nosfir'),
                    'activate_failed' => __('Activation failed', 'nosfir'),
                    'update_failed' => __('Update failed', 'nosfir'),
                    'install_now' => __('Install Now', 'nosfir'),
                    'activate_now' => __('Activate', 'nosfir'),
                    'deactivate_now' => __('Deactivate', 'nosfir'),
                    'update_now' => __('Update Now', 'nosfir'),
                    'error' => __('An error occurred. Please try again.', 'nosfir'),
                    'bulk_installing' => __('Installing plugins...', 'nosfir'),
                    'bulk_complete' => __('All plugins installed and activated!', 'nosfir'),
                    'confirm_deactivate' => __('Are you sure you want to deactivate this plugin?', 'nosfir')
                ),
                'plugins' => $this->get_all_plugins_data()
            ));
        }

        /**
         * Renderiza botão de instalação/ativação de plugin
         *
         * @param string $plugin_slug Slug do plugin
         * @param array  $plugin_data Dados do plugin
         * @param array  $classes Classes CSS adicionais
         * @param array  $texts Textos customizados para os botões
         * @return void
         */
        public static function render_plugin_button($plugin_slug, $plugin_data = array(), $classes = array(), $texts = array()) {
            if (!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
                return;
            }

            $instance = self::get_instance();
            $plugin_status = $instance->get_plugin_status($plugin_slug, $plugin_data);
            
            // Textos padrão
            $default_texts = array(
                'activated' => __('Active', 'nosfir'),
                'activate' => __('Activate', 'nosfir'),
                'deactivate' => __('Deactivate', 'nosfir'),
                'install' => __('Install Now', 'nosfir'),
                'update' => __('Update Now', 'nosfir'),
                'external' => __('Get Premium', 'nosfir'),
                'processing' => __('Processing...', 'nosfir')
            );
            
            $texts = wp_parse_args($texts, $default_texts);
            
            // Determina o estado e ação do botão
            $button = array();
            
            switch ($plugin_status['status']) {
                case 'active':
                    $button = array(
                        'text' => $texts['activated'],
                        'url' => '#',
                        'classes' => array('nosfir-plugin-active', 'button-disabled'),
                        'action' => '',
                        'disabled' => false
                    );
                    
                    // Se permitir desativação
                    if (!isset($plugin_data['required']) || !$plugin_data['required']) {
                        $button = array(
                            'text' => $texts['deactivate'],
                            'url' => '#',
                            'classes' => array('nosfir-plugin-deactivate', 'button-secondary'),
                            'action' => 'deactivate',
                            'disabled' => false
                        );
                    }
                    break;
                    
                case 'inactive':
                    $button = array(
                        'text' => $texts['activate'],
                        'url' => $plugin_status['activate_url'],
                        'classes' => array('nosfir-plugin-activate', 'button-primary'),
                        'action' => 'activate',
                        'disabled' => false
                    );
                    break;
                    
                case 'not_installed':
                    if (isset($plugin_data['source']) && $plugin_data['source'] === 'external') {
                        $button = array(
                            'text' => $texts['external'],
                            'url' => $plugin_data['external_url'],
                            'classes' => array('nosfir-plugin-external', 'button-primary'),
                            'action' => 'external',
                            'disabled' => false,
                            'target' => '_blank'
                        );
                    } else {
                        $button = array(
                            'text' => $texts['install'],
                            'url' => $plugin_status['install_url'],
                            'classes' => array('nosfir-plugin-install', 'button-primary'),
                            'action' => 'install',
                            'disabled' => false
                        );
                    }
                    break;
                    
                case 'update_available':
                    $button = array(
                        'text' => $texts['update'],
                        'url' => $plugin_status['update_url'],
                        'classes' => array('nosfir-plugin-update', 'button-primary'),
                        'action' => 'update',
                        'disabled' => false
                    );
                    break;
            }
            
            // Adiciona classes customizadas
            if (!empty($classes)) {
                $button['classes'] = array_merge($button['classes'], $classes);
            }
            
            // Adiciona classe button sempre
            $button['classes'][] = 'button';
            
            // Prepara atributos de dados
            $data_attrs = array(
                'slug' => $plugin_slug,
                'name' => isset($plugin_data['name']) ? $plugin_data['name'] : '',
                'action' => $button['action'],
                'nonce' => wp_create_nonce('nosfir-plugin-' . $button['action'] . '-' . $plugin_slug)
            );
            
            if (isset($plugin_data['file'])) {
                $data_attrs['file'] = $plugin_data['file'];
            }
            
            if (isset($plugin_data['source'])) {
                $data_attrs['source'] = $plugin_data['source'];
            }
            
            // Renderiza o botão
            ?>
            <span class="nosfir-plugin-action plugin-card-<?php echo esc_attr($plugin_slug); ?>">
                <a href="<?php echo esc_url($button['url']); ?>" 
                   class="<?php echo esc_attr(implode(' ', $button['classes'])); ?>"
                   <?php foreach ($data_attrs as $key => $value) : ?>
                       data-<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
                   <?php endforeach; ?>
                   <?php echo isset($button['target']) ? 'target="' . esc_attr($button['target']) . '"' : ''; ?>
                   <?php echo $button['disabled'] ? 'disabled' : ''; ?>
                   aria-label="<?php echo esc_attr($button['text'] . ' ' . (isset($plugin_data['name']) ? $plugin_data['name'] : '')); ?>">
                    <span class="button-text"><?php echo esc_html($button['text']); ?></span>
                    <span class="spinner"></span>
                </a>
                
                <?php if ($button['action'] !== 'external' && isset($plugin_data['info_url'])) : ?>
                    <span class="plugin-info-link">
                        <?php echo esc_html__('or', 'nosfir'); ?>
                        <a href="<?php echo esc_url($plugin_data['info_url']); ?>" target="_blank">
                            <?php esc_html_e('learn more', 'nosfir'); ?>
                        </a>
                    </span>
                <?php endif; ?>
            </span>
            <?php
        }

        /**
         * Obtém status de um plugin
         *
         * @param string $plugin_slug Slug do plugin
         * @param array  $plugin_data Dados do plugin
         * @return array
         */
        public function get_plugin_status($plugin_slug, $plugin_data = array()) {
            // Verifica cache
            if (isset($this->plugins_status_cache[$plugin_slug])) {
                return $this->plugins_status_cache[$plugin_slug];
            }
            
            $status = array(
                'status' => 'not_installed',
                'version' => '',
                'update_available' => false,
                'install_url' => '',
                'activate_url' => '',
                'deactivate_url' => '',
                'update_url' => ''
            );
            
            // Determina o arquivo do plugin
            $plugin_file = '';
            if (isset($plugin_data['file'])) {
                $plugin_file = $plugin_data['file'];
            } else {
                // Tenta descobrir o arquivo do plugin
                $plugin_file = $this->find_plugin_file($plugin_slug);
            }
            
            if ($plugin_file && file_exists($this->plugins_dir . '/' . $plugin_file)) {
                // Plugin está instalado
                if (is_plugin_active($plugin_file)) {
                    $status['status'] = 'active';
                } else {
                    $status['status'] = 'inactive';
                    $status['activate_url'] = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'activate',
                                'plugin' => $plugin_file,
                            ),
                            admin_url('plugins.php')
                        ),
                        'activate-plugin_' . $plugin_file
                    );
                }
                
                // Verifica versão e atualizações
                $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
                $status['version'] = $plugin_info['Version'];
                
                // Verifica se há atualizações disponíveis
                $updates = get_site_transient('update_plugins');
                if (isset($updates->response[$plugin_file])) {
                    $status['status'] = 'update_available';
                    $status['update_available'] = true;
                    $status['new_version'] = $updates->response[$plugin_file]->new_version;
                    $status['update_url'] = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'upgrade-plugin',
                                'plugin' => $plugin_file,
                            ),
                            admin_url('update.php')
                        ),
                        'upgrade-plugin_' . $plugin_file
                    );
                }
                
                // URL de desativação
                if ($status['status'] === 'active') {
                    $status['deactivate_url'] = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'deactivate',
                                'plugin' => $plugin_file,
                            ),
                            admin_url('plugins.php')
                        ),
                        'deactivate-plugin_' . $plugin_file
                    );
                }
            } else {
                // Plugin não está instalado
                if (isset($plugin_data['source'])) {
                    if ($plugin_data['source'] === 'wordpress') {
                        // Plugin do repositório WordPress
                        $status['install_url'] = wp_nonce_url(
                            add_query_arg(
                                array(
                                    'action' => 'install-plugin',
                                    'plugin' => $plugin_slug,
                                ),
                                admin_url('update.php')
                            ),
                            'install-plugin_' . $plugin_slug
                        );
                    } elseif ($plugin_data['source'] === 'external') {
                        // Plugin externo/premium
                        $status['status'] = 'external';
                        $status['external_url'] = $plugin_data['external_url'];
                    } else {
                        // Plugin bundled/local
                        $status['install_url'] = add_query_arg(
                            array(
                                'action' => 'nosfir_install_bundled_plugin',
                                'plugin' => $plugin_slug,
                                'nonce' => wp_create_nonce('nosfir-install-bundled-' . $plugin_slug)
                            ),
                            admin_url('admin-ajax.php')
                        );
                    }
                }
            }
            
            // Armazena no cache
            $this->plugins_status_cache[$plugin_slug] = $status;
            
            return $status;
        }

        /**
         * Encontra o arquivo principal de um plugin
         *
         * @param string $plugin_slug
         * @return string|false
         */
        private function find_plugin_file($plugin_slug) {
            // Verifica se o diretório do plugin existe
            if (!file_exists($this->plugins_dir . '/' . $plugin_slug)) {
                return false;
            }
            
            // Obtém todos os plugins
            $plugins = get_plugins('/' . $plugin_slug);
            
            if (!empty($plugins)) {
                // Retorna o primeiro arquivo de plugin encontrado
                $keys = array_keys($plugins);
                return $plugin_slug . '/' . $keys[0];
            }
            
            return false;
        }

        /**
         * AJAX: Instala um plugin
         */
        public function ajax_install_plugin() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            if (!current_user_can('install_plugins')) {
                wp_send_json_error(array('message' => __('You do not have permission to install plugins.', 'nosfir')));
            }
            
            $plugin_slug = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';
            $plugin_source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'wordpress';
            
            if (empty($plugin_slug)) {
                wp_send_json_error(array('message' => __('Plugin slug is required.', 'nosfir')));
            }
            
            // Obtém dados do plugin
            $plugin_data = $this->get_plugin_data($plugin_slug);
            
            if (!$plugin_data) {
                wp_send_json_error(array('message' => __('Plugin not found.', 'nosfir')));
            }
            
            // Inclui classes necessárias
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            
            // Desabilita output do upgrader
            ob_start();
            
            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader($skin);
            
            // Determina a fonte do plugin
            $source = '';
            if ($plugin_source === 'wordpress') {
                // Plugin do repositório WordPress
                $api = plugins_api('plugin_information', array(
                    'slug' => $plugin_slug,
                    'fields' => array('sections' => false)
                ));
                
                if (is_wp_error($api)) {
                    ob_end_clean();
                    wp_send_json_error(array('message' => $api->get_error_message()));
                }
                
                $source = $api->download_link;
            } else {
                // Plugin bundled
                $source = $plugin_data['source'];
            }
            
            // Instala o plugin
            $result = $upgrader->install($source);
            
            ob_end_clean();
            
            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            }
            
            if (!$result) {
                wp_send_json_error(array('message' => __('Plugin installation failed.', 'nosfir')));
            }
            
            // Ativa o plugin automaticamente se solicitado
            $activate = isset($_POST['activate']) ? filter_var($_POST['activate'], FILTER_VALIDATE_BOOLEAN) : true;
            
            if ($activate) {
                $plugin_file = $this->find_plugin_file($plugin_slug);
                
                if ($plugin_file) {
                    $activated = activate_plugin($plugin_file);
                    
                    if (is_wp_error($activated)) {
                        wp_send_json_success(array(
                            'message' => __('Plugin installed but not activated.', 'nosfir'),
                            'status' => 'installed',
                            'activate_url' => wp_nonce_url(
                                admin_url('plugins.php?action=activate&plugin=' . $plugin_file),
                                'activate-plugin_' . $plugin_file
                            )
                        ));
                    }
                }
            }
            
            wp_send_json_success(array(
                'message' => $activate ? __('Plugin installed and activated successfully!', 'nosfir') : __('Plugin installed successfully!', 'nosfir'),
                'status' => $activate ? 'active' : 'inactive'
            ));
        }

        /**
         * AJAX: Ativa um plugin
         */
        public function ajax_activate_plugin() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            if (!current_user_can('activate_plugins')) {
                wp_send_json_error(array('message' => __('You do not have permission to activate plugins.', 'nosfir')));
            }
            
            $plugin_file = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';
            
            if (empty($plugin_file)) {
                // Tenta encontrar pelo slug
                $plugin_slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
                if ($plugin_slug) {
                    $plugin_file = $this->find_plugin_file($plugin_slug);
                }
            }
            
            if (empty($plugin_file)) {
                wp_send_json_error(array('message' => __('Plugin file is required.', 'nosfir')));
            }
            
            // Ativa o plugin
            $result = activate_plugin($plugin_file);
            
            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            }
            
            wp_send_json_success(array(
                'message' => __('Plugin activated successfully!', 'nosfir'),
                'status' => 'active'
            ));
        }

        /**
         * AJAX: Desativa um plugin
         */
        public function ajax_deactivate_plugin() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            if (!current_user_can('activate_plugins')) {
                wp_send_json_error(array('message' => __('You do not have permission to deactivate plugins.', 'nosfir')));
            }
            
            $plugin_file = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';
            
            if (empty($plugin_file)) {
                // Tenta encontrar pelo slug
                $plugin_slug = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
                if ($plugin_slug) {
                    $plugin_file = $this->find_plugin_file($plugin_slug);
                }
            }
            
            if (empty($plugin_file)) {
                wp_send_json_error(array('message' => __('Plugin file is required.', 'nosfir')));
            }
            
            // Desativa o plugin
            deactivate_plugins($plugin_file);
            
            wp_send_json_success(array(
                'message' => __('Plugin deactivated successfully!', 'nosfir'),
                'status' => 'inactive'
            ));
        }

        /**
         * AJAX: Atualiza um plugin
         */
        public function ajax_update_plugin() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            if (!current_user_can('update_plugins')) {
                wp_send_json_error(array('message' => __('You do not have permission to update plugins.', 'nosfir')));
            }
            
            $plugin_file = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';
            
            if (empty($plugin_file)) {
                wp_send_json_error(array('message' => __('Plugin file is required.', 'nosfir')));
            }
            
            // Inclui classes necessárias
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/misc.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            
            // Desabilita output do upgrader
            ob_start();
            
            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader($skin);
            
            // Atualiza o plugin
            $result = $upgrader->upgrade($plugin_file);
            
            ob_end_clean();
            
            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            }
            
            if (!$result) {
                wp_send_json_error(array('message' => __('Plugin update failed.', 'nosfir')));
            }
            
            wp_send_json_success(array(
                'message' => __('Plugin updated successfully!', 'nosfir'),
                'status' => is_plugin_active($plugin_file) ? 'active' : 'inactive'
            ));
        }

        /**
         * AJAX: Instalação em massa de plugins
         */
        public function ajax_bulk_install_plugins() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            if (!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
                wp_send_json_error(array('message' => __('You do not have sufficient permissions.', 'nosfir')));
            }
            
            $plugins = isset($_POST['plugins']) ? (array) $_POST['plugins'] : array();
            
            if (empty($plugins)) {
                wp_send_json_error(array('message' => __('No plugins selected.', 'nosfir')));
            }
            
            $results = array();
            $success_count = 0;
            $error_count = 0;
            
            foreach ($plugins as $plugin_slug) {
                $plugin_data = $this->get_plugin_data($plugin_slug);
                
                if (!$plugin_data) {
                    $results[$plugin_slug] = array(
                        'status' => 'error',
                        'message' => __('Plugin not found.', 'nosfir')
                    );
                    $error_count++;
                    continue;
                }
                
                // Verifica status do plugin
                $status = $this->get_plugin_status($plugin_slug, $plugin_data);
                
                if ($status['status'] === 'active') {
                    $results[$plugin_slug] = array(
                        'status' => 'success',
                        'message' => __('Already active.', 'nosfir')
                    );
                    $success_count++;
                    continue;
                }
                
                // Instala se necessário
                if ($status['status'] === 'not_installed') {
                    $_POST['plugin'] = $plugin_slug;
                    $_POST['source'] = isset($plugin_data['source']) ? $plugin_data['source'] : 'wordpress';
                    $_POST['activate'] = false;
                    
                    ob_start();
                    $this->ajax_install_plugin();
                    $response = ob_get_clean();
                    $response = json_decode($response, true);
                    
                    if (!$response || !$response['success']) {
                        $results[$plugin_slug] = array(
                            'status' => 'error',
                            'message' => isset($response['data']['message']) ? $response['data']['message'] : __('Installation failed.', 'nosfir')
                        );
                        $error_count++;
                        continue;
                    }
                }
                
                // Ativa o plugin
                $plugin_file = $this->find_plugin_file($plugin_slug);
                if ($plugin_file) {
                    $activated = activate_plugin($plugin_file);
                    
                    if (is_wp_error($activated)) {
                        $results[$plugin_slug] = array(
                            'status' => 'warning',
                            'message' => __('Installed but not activated.', 'nosfir')
                        );
                    } else {
                        $results[$plugin_slug] = array(
                            'status' => 'success',
                            'message' => __('Installed and activated.', 'nosfir')
                        );
                        $success_count++;
                    }
                } else {
                    $results[$plugin_slug] = array(
                        'status' => 'error',
                        'message' => __('Plugin file not found after installation.', 'nosfir')
                    );
                    $error_count++;
                }
            }
            
            wp_send_json_success(array(
                'results' => $results,
                'summary' => array(
                    'total' => count($plugins),
                    'success' => $success_count,
                    'errors' => $error_count
                ),
                'message' => sprintf(
                    __('Bulk installation complete: %d successful, %d failed.', 'nosfir'),
                    $success_count,
                    $error_count
                )
            ));
        }

        /**
         * AJAX: Verifica status de um plugin
         */
        public function ajax_check_plugin_status() {
            check_ajax_referer('nosfir-plugin-installer', 'nonce');
            
            $plugin_slug = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';
            
            if (empty($plugin_slug)) {
                wp_send_json_error(array('message' => __('Plugin slug is required.', 'nosfir')));
            }
            
            $plugin_data = $this->get_plugin_data($plugin_slug);
            $status = $this->get_plugin_status($plugin_slug, $plugin_data);
            
            wp_send_json_success($status);
        }

        /**
         * Notice para plugins requeridos
         */
        public function required_plugins_notice() {
            if (!current_user_can('install_plugins')) {
                return;
            }
            
            // Verifica se o notice foi dispensado
            if (get_user_meta(get_current_user_id(), 'nosfir_dismissed_required_plugins_notice', true)) {
                return;
            }
            
            $missing_plugins = array();
            
            foreach ($this->required_plugins as $plugin_slug => $plugin_data) {
                $status = $this->get_plugin_status($plugin_slug, $plugin_data);
                if ($status['status'] !== 'active') {
                    $missing_plugins[] = $plugin_data['name'];
                }
            }
            
            if (!empty($missing_plugins)) {
                ?>
                <div class="notice notice-warning is-dismissible nosfir-required-plugins-notice">
                    <p>
                        <strong><?php _e('Nosfir Theme:', 'nosfir'); ?></strong>
                        <?php
                        printf(
                            __('The following required plugins are not active: %s', 'nosfir'),
                            '<strong>' . implode(', ', $missing_plugins) . '</strong>'
                        );
                        ?>
                    </p>
                    <p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=nosfir-plugins')); ?>" class="button button-primary">
                            <?php _e('Install Required Plugins', 'nosfir'); ?>
                        </a>
                        <button type="button" class="button" data-dismiss="nosfir-required-plugins">
                            <?php _e('Dismiss', 'nosfir'); ?>
                        </button>
                    </p>
                </div>
                <?php
            }
        }

        /**
         * Adiciona links de ação nos plugins
         */
        public function plugin_action_links($links, $plugin_file) {
            // Adiciona link para configurações do tema nos plugins relacionados
            $nosfir_plugins = array(
                'nosfir-companion/nosfir-companion.php'
            );
            
            if (in_array($plugin_file, $nosfir_plugins)) {
                $settings_link = sprintf(
                    '<a href="%s">%s</a>',
                    admin_url('admin.php?page=nosfir-dashboard'),
                    __('Theme Settings', 'nosfir')
                );
                array_unshift($links, $settings_link);
            }
            
            return $links;
        }

        /**
         * Verifica atualizações de plugins bundled
         */
        public function check_bundled_plugins_updates($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }
            
            foreach ($this->required_plugins as $plugin_slug => $plugin_data) {
                // Apenas para plugins bundled
                if (!isset($plugin_data['source']) || $plugin_data['source'] === 'wordpress') {
                    continue;
                }
                
                $plugin_file = $this->find_plugin_file($plugin_slug);
                if (!$plugin_file) {
                    continue;
                }
                
                // Verifica versão remota (implementar conforme necessário)
                $remote_version = $this->get_remote_plugin_version($plugin_slug);
                
                if ($remote_version) {
                    $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
                    
                    if (version_compare($remote_version, $plugin_info['Version'], '>')) {
                        $transient->response[$plugin_file] = (object) array(
                            'slug' => $plugin_slug,
                            'new_version' => $remote_version,
                            'package' => $plugin_data['source'],
                            'url' => isset($plugin_data['info_url']) ? $plugin_data['info_url'] : ''
                        );
                    }
                }
            }
            
            return $transient;
        }

        /**
         * Obtém versão remota de um plugin bundled
         */
        private function get_remote_plugin_version($plugin_slug) {
            // Implementar verificação de versão remota
            // Por exemplo, via API do seu servidor
            return false;
        }

        /**
         * Obtém dados de um plugin
         */
        private function get_plugin_data($plugin_slug) {
            $all_plugins = $this->get_all_plugins_data();
            return isset($all_plugins[$plugin_slug]) ? $all_plugins[$plugin_slug] : false;
        }

        /**
         * Obtém dados de todos os plugins
         */
        private function get_all_plugins_data() {
            return array_merge(
                $this->required_plugins,
                $this->recommended_plugins,
                $this->premium_plugins
            );
        }

        /**
         * Getters públicos
         */
        public function get_required_plugins() {
            return $this->required_plugins;
        }

        public function get_recommended_plugins() {
            return $this->recommended_plugins;
        }

        public function get_premium_plugins() {
            return $this->premium_plugins;
        }
    }

endif;

// Inicializa a classe
return Nosfir_Plugin_Installer::get_instance();