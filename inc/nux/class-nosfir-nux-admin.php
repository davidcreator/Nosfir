<?php
/**
 * Nosfir NUX Admin Class
 *
 * Gerencia toda a experiência de onboarding (New User Experience) do tema,
 * incluindo setup wizard, starter content, notices e guias interativos.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_NUX_Admin')) :

    /**
     * Classe principal NUX Admin
     */
    class Nosfir_NUX_Admin {

        /**
         * Instance única da classe
         *
         * @var Nosfir_NUX_Admin
         */
        private static $instance = null;

        /**
         * Status do onboarding
         *
         * @var array
         */
        private $onboarding_status = array();

        /**
         * Steps do setup wizard
         *
         * @var array
         */
        private $setup_steps = array();

        /**
         * Starter content disponível
         *
         * @var array
         */
        private $starter_content = array();

        /**
         * Configurações NUX
         *
         * @var array
         */
        private $nux_settings = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_NUX_Admin
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
            // Inicialização
            $this->init();

            // Hooks principais
            add_action('admin_init', array($this, 'maybe_redirect_to_wizard'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('admin_menu', array($this, 'add_wizard_menu'));
            add_action('admin_notices', array($this, 'admin_notices'), 99);
            add_filter('admin_body_class', array($this, 'admin_body_class'));

            // AJAX handlers
            add_action('wp_ajax_nosfir_dismiss_nux', array($this, 'ajax_dismiss_nux'));
            add_action('wp_ajax_nosfir_complete_step', array($this, 'ajax_complete_step'));
            add_action('wp_ajax_nosfir_skip_wizard', array($this, 'ajax_skip_wizard'));
            add_action('wp_ajax_nosfir_install_starter_content', array($this, 'ajax_install_starter_content'));
            add_action('wp_ajax_nosfir_save_wizard_settings', array($this, 'ajax_save_wizard_settings'));

            // Redirects e actions
            add_action('admin_post_nosfir_start_wizard', array($this, 'start_wizard'));
            add_action('admin_post_nosfir_complete_wizard', array($this, 'complete_wizard'));
            add_action('init', array($this, 'check_fresh_site'));
            add_action('after_switch_theme', array($this, 'theme_activation'), 10, 2);

            // Inbox messages se disponível
            if ($this->is_inbox_available()) {
                add_action('admin_init', array($this, 'admin_inbox_messages'));
            }

            // Guided tour
            add_action('admin_footer', array($this, 'guided_tour_markup'));
            
            // Tracking
            add_action('admin_init', array($this, 'track_onboarding_progress'));
        }

        /**
         * Inicialização
         */
        private function init() {
            // Status do onboarding
            $this->onboarding_status = get_option('nosfir_nux_status', array(
                'wizard_completed' => false,
                'dismissed' => false,
                'steps_completed' => array(),
                'fresh_site' => get_option('fresh_site', false),
                'activation_time' => get_option('nosfir_activation_time', 0),
                'last_step' => '',
                'starter_content_installed' => false,
            ));

            // Steps do wizard
            $this->setup_steps = array(
                'welcome' => array(
                    'name' => __('Welcome', 'nosfir'),
                    'view' => array($this, 'step_welcome'),
                    'handler' => array($this, 'save_welcome'),
                ),
                'theme-setup' => array(
                    'name' => __('Theme Setup', 'nosfir'),
                    'view' => array($this, 'step_theme_setup'),
                    'handler' => array($this, 'save_theme_setup'),
                ),
                'plugins' => array(
                    'name' => __('Plugins', 'nosfir'),
                    'view' => array($this, 'step_plugins'),
                    'handler' => array($this, 'save_plugins'),
                ),
                'content' => array(
                    'name' => __('Content', 'nosfir'),
                    'view' => array($this, 'step_content'),
                    'handler' => array($this, 'save_content'),
                ),
                'customize' => array(
                    'name' => __('Customize', 'nosfir'),
                    'view' => array($this, 'step_customize'),
                    'handler' => array($this, 'save_customize'),
                ),
                'ready' => array(
                    'name' => __('Ready!', 'nosfir'),
                    'view' => array($this, 'step_ready'),
                    'handler' => '',
                ),
            );

            // Starter content
            $this->starter_content = array(
                'homepage' => array(
                    'title' => __('Homepage', 'nosfir'),
                    'description' => __('Create a beautiful homepage with our pre-designed template', 'nosfir'),
                    'type' => 'page',
                    'template' => 'template-homepage.php',
                ),
                'blog' => array(
                    'title' => __('Blog Posts', 'nosfir'),
                    'description' => __('Add sample blog posts to see how your blog will look', 'nosfir'),
                    'type' => 'posts',
                    'count' => 6,
                ),
                'pages' => array(
                    'title' => __('Essential Pages', 'nosfir'),
                    'description' => __('Create About, Contact, and Services pages', 'nosfir'),
                    'type' => 'pages',
                    'pages' => array('about', 'contact', 'services'),
                ),
                'menus' => array(
                    'title' => __('Navigation Menus', 'nosfir'),
                    'description' => __('Setup primary and footer navigation menus', 'nosfir'),
                    'type' => 'menus',
                ),
                'widgets' => array(
                    'title' => __('Widgets', 'nosfir'),
                    'description' => __('Add sample widgets to sidebar and footer areas', 'nosfir'),
                    'type' => 'widgets',
                ),
            );

            // Se WooCommerce estiver ativo
            if (class_exists('WooCommerce')) {
                $this->starter_content['products'] = array(
                    'title' => __('Products', 'nosfir'),
                    'description' => __('Add sample products to your store', 'nosfir'),
                    'type' => 'products',
                    'count' => 12,
                );
            }

            // Configurações NUX
            $this->nux_settings = array(
                'show_wizard' => true,
                'show_notices' => true,
                'show_guided_tour' => true,
                'auto_install_plugins' => false,
                'auto_import_content' => false,
            );

            // Filtros para customização
            $this->setup_steps = apply_filters('nosfir_nux_setup_steps', $this->setup_steps);
            $this->starter_content = apply_filters('nosfir_starter_content', $this->starter_content);
            $this->nux_settings = apply_filters('nosfir_nux_settings', $this->nux_settings);
        }

        /**
         * Verifica se deve redirecionar para o wizard
         */
        public function maybe_redirect_to_wizard() {
            // Não redireciona se:
            // - Wizard já foi completado
            // - Foi dismissado
            // - Não é admin
            // - Está fazendo AJAX
            // - Está em processo de ativação de plugins
            
            if (
                $this->onboarding_status['wizard_completed'] ||
                $this->onboarding_status['dismissed'] ||
                !current_user_can('manage_options') ||
                wp_doing_ajax() ||
                isset($_GET['activate']) ||
                isset($_GET['activated'])
            ) {
                return;
            }

            // Verifica se é primeira vez (tema recém ativado)
            if (get_option('nosfir_activation_redirect', false)) {
                delete_option('nosfir_activation_redirect');
                
                // Redireciona para o wizard
                wp_safe_redirect(admin_url('admin.php?page=nosfir-wizard'));
                exit;
            }
        }

        /**
         * Adiciona menu do wizard
         */
        public function add_wizard_menu() {
            add_submenu_page(
                null, // Hidden from menu
                __('Theme Setup Wizard', 'nosfir'),
                __('Setup Wizard', 'nosfir'),
                'manage_options',
                'nosfir-wizard',
                array($this, 'setup_wizard')
            );
        }

        /**
         * Renderiza o Setup Wizard
         */
        public function setup_wizard() {
            // Obtém step atual
            $current_step = isset($_GET['step']) ? sanitize_key($_GET['step']) : 'welcome';
            
            if (!array_key_exists($current_step, $this->setup_steps)) {
                $current_step = 'welcome';
            }

            // Salva step atual
            $this->onboarding_status['last_step'] = $current_step;
            $this->update_onboarding_status();

            // Process form se enviado
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($this->setup_steps[$current_step]['handler'])) {
                call_user_func($this->setup_steps[$current_step]['handler']);
            }
            ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo('charset'); ?>">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title><?php _e('Nosfir Setup Wizard', 'nosfir'); ?></title>
                <?php wp_print_styles('nosfir-wizard'); ?>
                <?php do_action('admin_print_styles'); ?>
                <?php do_action('admin_head'); ?>
            </head>
            <body class="nosfir-wizard wp-core-ui">
                <div class="nosfir-wizard-container">
                    <div class="nosfir-wizard-header">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="Nosfir">
                        <h1><?php _e('Nosfir Setup Wizard', 'nosfir'); ?></h1>
                    </div>

                    <div class="nosfir-wizard-progress">
                        <ul class="nosfir-wizard-steps">
                            <?php
                            $step_index = 0;
                            foreach ($this->setup_steps as $step_key => $step) {
                                $step_index++;
                                $is_active = ($step_key === $current_step);
                                $is_completed = in_array($step_key, $this->onboarding_status['steps_completed']);
                                ?>
                                <li class="<?php echo $is_active ? 'active' : ($is_completed ? 'completed' : ''); ?>">
                                    <span class="step-number"><?php echo $step_index; ?></span>
                                    <span class="step-name"><?php echo esc_html($step['name']); ?></span>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <div class="nosfir-wizard-progress-bar">
                            <?php
                            $total_steps = count($this->setup_steps);
                            $completed_steps = count($this->onboarding_status['steps_completed']);
                            $progress = ($completed_steps / $total_steps) * 100;
                            ?>
                            <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                        </div>
                    </div>

                    <div class="nosfir-wizard-content">
                        <form method="post" class="nosfir-wizard-form">
                            <?php
                            wp_nonce_field('nosfir-wizard-' . $current_step);
                            call_user_func($this->setup_steps[$current_step]['view']);
                            ?>
                        </form>
                    </div>

                    <div class="nosfir-wizard-footer">
                        <a href="<?php echo esc_url(admin_url()); ?>" class="nosfir-wizard-skip">
                            <?php _e('Skip Setup', 'nosfir'); ?>
                        </a>
                        <span class="nosfir-wizard-help">
                            <?php
                            printf(
                                __('Need help? Check our %sdocumentation%s', 'nosfir'),
                                '<a href="https://docs.nosfir.com" target="_blank">',
                                '</a>'
                            );
                            ?>
                        </span>
                    </div>
                </div>

                <?php wp_print_scripts('nosfir-wizard'); ?>
                <?php do_action('admin_footer'); ?>
                <?php do_action('admin_print_footer_scripts'); ?>
            </body>
            </html>
            <?php
            exit;
        }

        /**
         * Step: Welcome
         */
        public function step_welcome() {
            ?>
            <div class="nosfir-wizard-step-welcome">
                <h2><?php _e('Welcome to Nosfir! 🎉', 'nosfir'); ?></h2>
                
                <p class="lead">
                    <?php _e('Thank you for choosing Nosfir - the most powerful and flexible WordPress theme. This quick setup wizard will help you configure your new theme and get you started in no time!', 'nosfir'); ?>
                </p>

                <div class="nosfir-wizard-features">
                    <div class="feature">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <h3><?php _e('Easy Customization', 'nosfir'); ?></h3>
                        <p><?php _e('Customize every aspect of your site with our intuitive options.', 'nosfir'); ?></p>
                    </div>
                    <div class="feature">
                        <span class="dashicons dashicons-layout"></span>
                        <h3><?php _e('Beautiful Demos', 'nosfir'); ?></h3>
                        <p><?php _e('Import professionally designed demos with one click.', 'nosfir'); ?></p>
                    </div>
                    <div class="feature">
                        <span class="dashicons dashicons-plugins-checked"></span>
                        <h3><?php _e('Plugin Compatible', 'nosfir'); ?></h3>
                        <p><?php _e('Works seamlessly with popular WordPress plugins.', 'nosfir'); ?></p>
                    </div>
                </div>

                <div class="nosfir-wizard-actions">
                    <button type="submit" class="button button-primary button-hero" name="save_step" value="welcome">
                        <?php _e('Let\'s Get Started →', 'nosfir'); ?>
                    </button>
                </div>

                <div class="nosfir-wizard-note">
                    <p>
                        <strong><?php _e('Note:', 'nosfir'); ?></strong>
                        <?php _e('This wizard will help you:', 'nosfir'); ?>
                    </p>
                    <ul>
                        <li><?php _e('Configure basic theme settings', 'nosfir'); ?></li>
                        <li><?php _e('Install recommended plugins', 'nosfir'); ?></li>
                        <li><?php _e('Import demo content (optional)', 'nosfir'); ?></li>
                        <li><?php _e('Set up your homepage and menus', 'nosfir'); ?></li>
                    </ul>
                </div>
            </div>
            <?php
        }

        /**
         * Step: Theme Setup
         */
        public function step_theme_setup() {
            ?>
            <div class="nosfir-wizard-step-theme-setup">
                <h2><?php _e('Basic Setup', 'nosfir'); ?></h2>
                
                <div class="form-group">
                    <label for="site_title"><?php _e('Site Title', 'nosfir'); ?></label>
                    <input type="text" id="site_title" name="site_title" value="<?php echo esc_attr(get_bloginfo('name')); ?>" />
                </div>

                <div class="form-group">
                    <label for="site_tagline"><?php _e('Site Tagline', 'nosfir'); ?></label>
                    <input type="text" id="site_tagline" name="site_tagline" value="<?php echo esc_attr(get_bloginfo('description')); ?>" />
                </div>

                <div class="form-group">
                    <label for="site_logo"><?php _e('Upload Logo', 'nosfir'); ?></label>
                    <div class="nosfir-media-upload">
                        <?php
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';
                        ?>
                        <div class="logo-preview">
                            <?php if ($logo_url) : ?>
                                <img src="<?php echo esc_url($logo_url); ?>" alt="Logo">
                            <?php else : ?>
                                <span class="placeholder"><?php _e('No logo selected', 'nosfir'); ?></span>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="site_logo" id="site_logo" value="<?php echo esc_attr($custom_logo_id); ?>">
                        <button type="button" class="button nosfir-upload-logo"><?php _e('Select Logo', 'nosfir'); ?></button>
                        <?php if ($logo_url) : ?>
                            <button type="button" class="button nosfir-remove-logo"><?php _e('Remove', 'nosfir'); ?></button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php _e('Site Type', 'nosfir'); ?></label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="site_type" value="business" checked>
                            <span><?php _e('Business', 'nosfir'); ?></span>
                        </label>
                        <label>
                            <input type="radio" name="site_type" value="blog">
                            <span><?php _e('Blog', 'nosfir'); ?></span>
                        </label>
                        <label>
                            <input type="radio" name="site_type" value="portfolio">
                            <span><?php _e('Portfolio', 'nosfir'); ?></span>
                        </label>
                        <label>
                            <input type="radio" name="site_type" value="shop">
                            <span><?php _e('Online Store', 'nosfir'); ?></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php _e('Color Scheme', 'nosfir'); ?></label>
                    <div class="color-schemes">
                        <?php
                        $schemes = array(
                            'default' => array('#2563eb', '#1f2937', '#f59e0b'),
                            'dark' => array('#1f2937', '#111827', '#f59e0b'),
                            'light' => array('#3b82f6', '#f3f4f6', '#10b981'),
                            'colorful' => array('#8b5cf6', '#ec4899', '#f59e0b'),
                        );
                        
                        foreach ($schemes as $scheme_key => $colors) :
                            ?>
                            <label class="color-scheme-option">
                                <input type="radio" name="color_scheme" value="<?php echo esc_attr($scheme_key); ?>" <?php checked($scheme_key, 'default'); ?>>
                                <span class="color-scheme-preview">
                                    <?php foreach ($colors as $color) : ?>
                                        <span class="color-swatch" style="background: <?php echo esc_attr($color); ?>"></span>
                                    <?php endforeach; ?>
                                </span>
                                <span class="scheme-name"><?php echo ucfirst($scheme_key); ?></span>
                            </label>
                            <?php
                        endforeach;
                        ?>
                    </div>
                </div>

                <div class="nosfir-wizard-actions">
                    <a href="<?php echo esc_url($this->get_step_url('welcome')); ?>" class="button">
                        <?php _e('← Previous', 'nosfir'); ?>
                    </a>
                    <button type="submit" class="button button-primary" name="save_step" value="theme-setup">
                        <?php _e('Continue →', 'nosfir'); ?>
                    </button>
                </div>
            </div>
            <?php
        }

        /**
         * Step: Plugins
         */
        public function step_plugins() {
            $plugins = $this->get_recommended_plugins();
            ?>
            <div class="nosfir-wizard-step-plugins">
                <h2><?php _e('Install Plugins', 'nosfir'); ?></h2>
                <p><?php _e('Select the plugins you want to install. You can always add or remove plugins later.', 'nosfir'); ?></p>

                <div class="plugins-list">
                    <?php foreach ($plugins as $plugin_slug => $plugin) : ?>
                        <div class="plugin-item">
                            <label>
                                <input type="checkbox" 
                                       name="plugins[]" 
                                       value="<?php echo esc_attr($plugin_slug); ?>"
                                       <?php echo $plugin['required'] ? 'checked disabled' : 'checked'; ?>>
                                <div class="plugin-info">
                                    <h4><?php echo esc_html($plugin['name']); ?></h4>
                                    <p><?php echo esc_html($plugin['description']); ?></p>
                                    <?php if ($plugin['required']) : ?>
                                        <span class="required"><?php _e('Required', 'nosfir'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="nosfir-wizard-actions">
                    <a href="<?php echo esc_url($this->get_step_url('theme-setup')); ?>" class="button">
                        <?php _e('← Previous', 'nosfir'); ?>
                    </a>
                    <button type="submit" class="button button-primary" name="save_step" value="plugins">
                        <span class="button-text"><?php _e('Install Plugins & Continue →', 'nosfir'); ?></span>
                        <span class="spinner"></span>
                    </button>
                </div>
            </div>
            <?php
        }

        /**
         * Step: Content
         */
        public function step_content() {
            ?>
            <div class="nosfir-wizard-step-content">
                <h2><?php _e('Import Content', 'nosfir'); ?></h2>
                <p><?php _e('Get started quickly by importing our demo content, or start with a blank slate.', 'nosfir'); ?></p>

                <div class="content-options">
                    <div class="option-card">
                        <input type="radio" name="content_option" value="demo" id="import_demo" checked>
                        <label for="import_demo">
                            <span class="option-icon dashicons dashicons-download"></span>
                            <h3><?php _e('Import Demo Content', 'nosfir'); ?></h3>
                            <p><?php _e('Import pages, posts, menus and widgets to get started quickly.', 'nosfir'); ?></p>
                        </label>
                    </div>

                    <div class="option-card">
                        <input type="radio" name="content_option" value="starter" id="starter_content">
                        <label for="starter_content">
                            <span class="option-icon dashicons dashicons-admin-page"></span>
                            <h3><?php _e('Basic Starter Content', 'nosfir'); ?></h3>
                            <p><?php _e('Create essential pages and menus only.', 'nosfir'); ?></p>
                        </label>
                    </div>

                    <div class="option-card">
                        <input type="radio" name="content_option" value="blank" id="blank_site">
                        <label for="blank_site">
                            <span class="option-icon dashicons dashicons-edit"></span>
                            <h3><?php _e('Start from Scratch', 'nosfir'); ?></h3>
                            <p><?php _e('I\'ll create my own content from a blank slate.', 'nosfir'); ?></p>
                        </label>
                    </div>
                </div>

                <div class="content-details" id="demo-options" style="display: block;">
                    <h4><?php _e('Select Demo', 'nosfir'); ?></h4>
                    <select name="demo_type" class="widefat">
                        <option value="business"><?php _e('Business Demo', 'nosfir'); ?></option>
                        <option value="blog"><?php _e('Blog Demo', 'nosfir'); ?></option>
                        <option value="portfolio"><?php _e('Portfolio Demo', 'nosfir'); ?></option>
                        <?php if (class_exists('WooCommerce')) : ?>
                            <option value="shop"><?php _e('Shop Demo', 'nosfir'); ?></option>
                        <?php endif; ?>
                    </select>

                    <div class="import-options">
                        <label>
                            <input type="checkbox" name="import_widgets" checked>
                            <?php _e('Import Widgets', 'nosfir'); ?>
                        </label>
                        <label>
                            <input type="checkbox" name="import_customizer" checked>
                            <?php _e('Import Customizer Settings', 'nosfir'); ?>
                        </label>
                        <label>
                            <input type="checkbox" name="import_media" checked>
                            <?php _e('Download and Import Media Files', 'nosfir'); ?>
                        </label>
                    </div>
                </div>

                <div class="content-details" id="starter-options" style="display: none;">
                    <h4><?php _e('Select Content to Create', 'nosfir'); ?></h4>
                    <?php foreach ($this->starter_content as $content_key => $content) : ?>
                        <label>
                            <input type="checkbox" name="starter_content[]" value="<?php echo esc_attr($content_key); ?>" checked>
                            <strong><?php echo esc_html($content['title']); ?></strong>
                            <span><?php echo esc_html($content['description']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="nosfir-wizard-actions">
                    <a href="<?php echo esc_url($this->get_step_url('plugins')); ?>" class="button">
                        <?php _e('← Previous', 'nosfir'); ?>
                    </a>
                    <button type="submit" class="button button-primary" name="save_step" value="content">
                        <span class="button-text"><?php _e('Import & Continue →', 'nosfir'); ?></span>
                        <span class="spinner"></span>
                    </button>
                </div>
            </div>
            <?php
        }

        /**
         * Step: Customize
         */
        public function step_customize() {
            ?>
            <div class="nosfir-wizard-step-customize">
                <h2><?php _e('Quick Customization', 'nosfir'); ?></h2>
                <p><?php _e('Set up some final options to personalize your site.', 'nosfir'); ?></p>

                <div class="customize-options">
                    <div class="form-group">
                        <label><?php _e('Homepage Display', 'nosfir'); ?></label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="show_on_front" value="posts" <?php checked(get_option('show_on_front'), 'posts'); ?>>
                                <span><?php _e('Latest Posts', 'nosfir'); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="show_on_front" value="page" <?php checked(get_option('show_on_front'), 'page'); ?>>
                                <span><?php _e('Static Page', 'nosfir'); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php _e('Header Layout', 'nosfir'); ?></label>
                        <div class="layout-options">
                            <?php
                            $header_layouts = array(
                                'layout-1' => __('Classic', 'nosfir'),
                                'layout-2' => __('Centered', 'nosfir'),
                                'layout-3' => __('Modern', 'nosfir'),
                            );
                            foreach ($header_layouts as $layout => $label) :
                                ?>
                                <label class="layout-option">
                                    <input type="radio" name="header_layout" value="<?php echo esc_attr($layout); ?>" <?php checked($layout, 'layout-1'); ?>>
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/headers/' . $layout . '.png'); ?>" alt="<?php echo esc_attr($label); ?>">
                                    <span><?php echo esc_html($label); ?></span>
                                </label>
                                <?php
                            endforeach;
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php _e('Footer Widgets', 'nosfir'); ?></label>
                        <select name="footer_widgets" class="widefat">
                            <option value="0"><?php _e('No Footer Widgets', 'nosfir'); ?></option>
                            <option value="1"><?php _e('1 Column', 'nosfir'); ?></option>
                            <option value="2"><?php _e('2 Columns', 'nosfir'); ?></option>
                            <option value="3" selected><?php _e('3 Columns', 'nosfir'); ?></option>
                            <option value="4"><?php _e('4 Columns', 'nosfir'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="enable_sticky_header" checked>
                            <?php _e('Enable Sticky Header', 'nosfir'); ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="enable_back_to_top" checked>
                            <?php _e('Enable Back to Top Button', 'nosfir'); ?>
                        </label>
                    </div>

                    <?php if (class_exists('WooCommerce')) : ?>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="enable_shop_sidebar">
                                <?php _e('Enable Shop Sidebar', 'nosfir'); ?>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="nosfir-wizard-actions">
                    <a href="<?php echo esc_url($this->get_step_url('content')); ?>" class="button">
                        <?php _e('← Previous', 'nosfir'); ?>
                    </a>
                    <button type="submit" class="button button-primary" name="save_step" value="customize">
                        <?php _e('Save & Continue →', 'nosfir'); ?>
                    </button>
                </div>
            </div>
            <?php
        }

        /**
         * Step: Ready
         */
        public function step_ready() {
            // Marca wizard como completo
            $this->onboarding_status['wizard_completed'] = true;
            $this->update_onboarding_status();
            ?>
            <div class="nosfir-wizard-step-ready">
                <div class="success-icon">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>

                <h2><?php _e('All Done! 🎊', 'nosfir'); ?></h2>
                
                <p class="lead">
                    <?php _e('Congratulations! Your site is now ready. Here are some next steps to help you get the most out of Nosfir:', 'nosfir'); ?>
                </p>

                <div class="next-steps">
                    <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="step-card">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <h3><?php _e('Customize Your Site', 'nosfir'); ?></h3>
                        <p><?php _e('Fine-tune colors, fonts, and layouts', 'nosfir'); ?></p>
                    </a>

                    <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="step-card">
                        <span class="dashicons dashicons-edit"></span>
                        <h3><?php _e('Create Content', 'nosfir'); ?></h3>
                        <p><?php _e('Start adding posts and pages', 'nosfir'); ?></p>
                    </a>

                    <a href="<?php echo esc_url(admin_url('admin.php?page=nosfir-dashboard')); ?>" class="step-card">
                        <span class="dashicons dashicons-dashboard"></span>
                        <h3><?php _e('Theme Dashboard', 'nosfir'); ?></h3>
                        <p><?php _e('Access theme options and features', 'nosfir'); ?></p>
                    </a>

                    <a href="https://docs.nosfir.com" target="_blank" class="step-card">
                        <span class="dashicons dashicons-book"></span>
                        <h3><?php _e('Documentation', 'nosfir'); ?></h3>
                        <p><?php _e('Learn more about using Nosfir', 'nosfir'); ?></p>
                    </a>
                </div>

                <div class="nosfir-wizard-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button" target="_blank">
                        <?php _e('View Your Site', 'nosfir'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url()); ?>" class="button button-primary">
                        <?php _e('Go to Dashboard', 'nosfir'); ?>
                    </a>
                </div>

                <div class="social-share">
                    <p><?php _e('Share your experience:', 'nosfir'); ?></p>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Just set up my new website with #Nosfir WordPress theme! 🚀'); ?>" target="_blank" class="twitter">
                            <span class="dashicons dashicons-twitter"></span>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(home_url()); ?>" target="_blank" class="facebook">
                            <span class="dashicons dashicons-facebook"></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Salva step Welcome
         */
        public function save_welcome() {
            check_admin_referer('nosfir-wizard-welcome');
            
            $this->mark_step_complete('welcome');
            wp_safe_redirect($this->get_step_url('theme-setup'));
            exit;
        }

        /**
         * Salva step Theme Setup
         */
        public function save_theme_setup() {
            check_admin_referer('nosfir-wizard-theme-setup');
            
            // Salva configurações
            if (isset($_POST['site_title'])) {
                update_option('blogname', sanitize_text_field($_POST['site_title']));
            }
            
            if (isset($_POST['site_tagline'])) {
                update_option('blogdescription', sanitize_text_field($_POST['site_tagline']));
            }
            
            if (isset($_POST['site_logo'])) {
                set_theme_mod('custom_logo', absint($_POST['site_logo']));
            }
            
            if (isset($_POST['site_type'])) {
                update_option('nosfir_site_type', sanitize_text_field($_POST['site_type']));
            }
            
            if (isset($_POST['color_scheme'])) {
                update_option('nosfir_color_scheme', sanitize_text_field($_POST['color_scheme']));
                $this->apply_color_scheme($_POST['color_scheme']);
            }
            
            $this->mark_step_complete('theme-setup');
            wp_safe_redirect($this->get_step_url('plugins'));
            exit;
        }

        /**
         * Enqueue scripts
         */
        public function enqueue_scripts($hook) {
            // Scripts do wizard
            if ($hook === 'admin_page_nosfir-wizard') {
                wp_enqueue_style(
                    'nosfir-wizard',
                    get_template_directory_uri() . '/assets/css/admin/wizard.css',
                    array('dashicons'),
                    wp_get_theme()->get('Version')
                );
                
                wp_enqueue_script(
                    'nosfir-wizard',
                    get_template_directory_uri() . '/assets/js/admin/wizard.js',
                    array('jquery', 'media-upload', 'media-views'),
                    wp_get_theme()->get('Version'),
                    true
                );
                
                wp_localize_script('nosfir-wizard', 'nosfir_wizard', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('nosfir-wizard'),
                    'strings' => array(
                        'installing' => __('Installing...', 'nosfir'),
                        'importing' => __('Importing...', 'nosfir'),
                        'activating' => __('Activating...', 'nosfir'),
                        'success' => __('Success!', 'nosfir'),
                        'error' => __('An error occurred', 'nosfir'),
                    ),
                ));
                
                wp_enqueue_media();
            }

            // Scripts do NUX admin
            if (!$this->onboarding_status['dismissed'] && !$this->onboarding_status['wizard_completed']) {
                wp_enqueue_style(
                    'nosfir-nux-admin',
                    get_template_directory_uri() . '/assets/css/admin/nux.css',
                    array(),
                    wp_get_theme()->get('Version')
                );
                
                wp_enqueue_script(
                    'nosfir-nux-admin',
                    get_template_directory_uri() . '/assets/js/admin/nux.js',
                    array('jquery'),
                    wp_get_theme()->get('Version'),
                    true
                );
                
                wp_localize_script('nosfir-nux-admin', 'nosfir_nux', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('nosfir-nux'),
                ));
            }
        }

        /**
         * Admin notices
         */
        public function admin_notices() {
            // Não mostra se wizard foi completado ou dismissado
            if ($this->onboarding_status['wizard_completed'] || $this->onboarding_status['dismissed']) {
                return;
            }

            // Não mostra em certas páginas
            $screen = get_current_screen();
            if (in_array($screen->id, array('themes', 'nosfir_page_nosfir-wizard'))) {
                return;
            }
            ?>
            <div class="notice notice-info nosfir-nux-notice is-dismissible">
                <div class="nosfir-notice-content">
                    <div class="nosfir-notice-logo">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-icon.png'); ?>" alt="Nosfir">
                    </div>
                    <div class="nosfir-notice-text">
                        <h2><?php _e('Welcome to Nosfir! 🎉', 'nosfir'); ?></h2>
                        <p><?php _e('Thank you for choosing Nosfir. Let\'s get your site set up in just a few steps!', 'nosfir'); ?></p>
                        
                        <?php if (class_exists('WooCommerce')) : ?>
                            <p><?php _e('We\'ve detected WooCommerce is installed. Our wizard will help you set up your online store.', 'nosfir'); ?></p>
                        <?php elseif (current_user_can('install_plugins')) : ?>
                            <p><?php _e('Want to create an online store? Our wizard can help you install WooCommerce.', 'nosfir'); ?></p>
                        <?php endif; ?>
                        
                        <div class="nosfir-notice-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=nosfir-wizard')); ?>" class="button button-primary">
                                <?php _e('Start Setup Wizard', 'nosfir'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button">
                                <?php _e('Go to Customizer', 'nosfir'); ?>
                            </a>
                            <a href="#" class="nosfir-nux-dismiss">
                                <?php _e('Dismiss', 'nosfir'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Admin body class
         */
        public function admin_body_class($classes) {
            if (!$this->onboarding_status['wizard_completed'] && !$this->onboarding_status['dismissed']) {
                $classes .= ' nosfir-nux-active ';
            }

            if (isset($_GET['page']) && $_GET['page'] === 'nosfir-wizard') {
                $classes .= ' nosfir-wizard-page ';
            }

            return $classes;
        }

        /**
         * Helpers
         */
        
        private function get_step_url($step) {
            return admin_url('admin.php?page=nosfir-wizard&step=' . $step);
        }

        private function mark_step_complete($step) {
            if (!in_array($step, $this->onboarding_status['steps_completed'])) {
                $this->onboarding_status['steps_completed'][] = $step;
                $this->update_onboarding_status();
            }
        }

        private function update_onboarding_status() {
            update_option('nosfir_nux_status', $this->onboarding_status);
        }

        private function get_recommended_plugins() {
            return array(
                'elementor' => array(
                    'name' => 'Elementor',
                    'description' => __('The most advanced frontend drag & drop page builder', 'nosfir'),
                    'required' => false,
                ),
                'woocommerce' => array(
                    'name' => 'WooCommerce',
                    'description' => __('The world\'s most customizable eCommerce platform', 'nosfir'),
                    'required' => false,
                ),
                'contact-form-7' => array(
                    'name' => 'Contact Form 7',
                    'description' => __('Just another contact form plugin. Simple but flexible', 'nosfir'),
                    'required' => false,
                ),
                'wordpress-seo' => array(
                    'name' => 'Yoast SEO',
                    'description' => __('Improve your WordPress SEO', 'nosfir'),
                    'required' => false,
                ),
            );
        }

        /**
         * Theme activation
         */
        public function theme_activation($old_theme_name, $old_theme) {
            // Marca para redirect
            add_option('nosfir_activation_redirect', true);
            add_option('nosfir_activation_time', time());
        }

        /**
         * Check fresh site
         */
        public function check_fresh_site() {
            if (null === get_option('nosfir_nux_fresh_site', null)) {
                update_option('nosfir_nux_fresh_site', get_option('fresh_site', false));
            }
        }

        /**
         * Apply color scheme
         */
        private function apply_color_scheme($scheme) {
            $schemes = array(
                'default' => array(
                    'primary_color' => '#2563eb',
                    'secondary_color' => '#1f2937',
                    'accent_color' => '#f59e0b',
                ),
                'dark' => array(
                    'primary_color' => '#1f2937',
                    'secondary_color' => '#111827',
                    'accent_color' => '#f59e0b',
                ),
                'light' => array(
                    'primary_color' => '#3b82f6',
                    'secondary_color' => '#f3f4f6',
                    'accent_color' => '#10b981',
                ),
                'colorful' => array(
                    'primary_color' => '#8b5cf6',
                    'secondary_color' => '#ec4899',
                    'accent_color' => '#f59e0b',
                ),
            );

            if (isset($schemes[$scheme])) {
                foreach ($schemes[$scheme] as $mod => $value) {
                    set_theme_mod('nosfir_' . $mod, $value);
                }
            }
        }

        /**
         * AJAX handlers
         */
        
        public function ajax_dismiss_nux() {
            check_ajax_referer('nosfir-nux', 'nonce');
            
            $this->onboarding_status['dismissed'] = true;
            $this->update_onboarding_status();
            
            wp_send_json_success();
        }

        // Mais métodos AJAX...
    }

endif;

// Inicializa
return Nosfir_NUX_Admin::get_instance();