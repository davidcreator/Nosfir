<?php
/**
 * Nosfir NUX Guided Tour Class
 *
 * Cria tours guiados interativos para ajudar usuários a conhecer
 * e configurar o tema através do Customizer e Admin.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_NUX_Guided_Tour')) :

    /**
     * Classe de Guided Tour
     */
    class Nosfir_NUX_Guided_Tour {

        /**
         * Instance única da classe
         *
         * @var Nosfir_NUX_Guided_Tour
         */
        private static $instance = null;

        /**
         * Tours disponíveis
         *
         * @var array
         */
        private $tours = array();

        /**
         * Tour ativo atual
         *
         * @var string
         */
        private $active_tour = '';

        /**
         * Steps do tour atual
         *
         * @var array
         */
        private $current_steps = array();

        /**
         * Configurações do tour
         *
         * @var array
         */
        private $tour_config = array();

        /**
         * Status dos tours
         *
         * @var array
         */
        private $tours_status = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_NUX_Guided_Tour
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
            $this->init();
            $this->setup_hooks();
        }

        /**
         * Inicialização
         */
        private function init() {
            // Carrega status dos tours
            $this->tours_status = get_option('nosfir_guided_tours_status', array());

            // Define tours disponíveis
            $this->tours = array(
                'customizer' => array(
                    'name' => __('Customizer Tour', 'nosfir'),
                    'description' => __('Learn how to customize your site appearance', 'nosfir'),
                    'location' => 'customizer',
                    'auto_start' => true,
                    'steps' => array($this, 'get_customizer_steps'),
                ),
                'dashboard' => array(
                    'name' => __('Dashboard Tour', 'nosfir'),
                    'description' => __('Explore the WordPress dashboard features', 'nosfir'),
                    'location' => 'admin',
                    'auto_start' => false,
                    'steps' => array($this, 'get_dashboard_steps'),
                ),
                'editor' => array(
                    'name' => __('Editor Tour', 'nosfir'),
                    'description' => __('Master the block editor', 'nosfir'),
                    'location' => 'editor',
                    'auto_start' => false,
                    'steps' => array($this, 'get_editor_steps'),
                ),
                'woocommerce' => array(
                    'name' => __('WooCommerce Tour', 'nosfir'),
                    'description' => __('Set up your online store', 'nosfir'),
                    'location' => 'admin',
                    'auto_start' => false,
                    'condition' => 'woocommerce_active',
                    'steps' => array($this, 'get_woocommerce_steps'),
                ),
                'theme-features' => array(
                    'name' => __('Theme Features Tour', 'nosfir'),
                    'description' => __('Discover Nosfir theme features', 'nosfir'),
                    'location' => 'admin',
                    'auto_start' => false,
                    'steps' => array($this, 'get_theme_features_steps'),
                ),
            );

            // Configurações padrão
            $this->tour_config = array(
                'overlay' => true,
                'keyboard_navigation' => true,
                'exit_on_esc' => true,
                'exit_on_overlay_click' => true,
                'show_step_numbers' => true,
                'show_progress' => true,
                'show_buttons' => true,
                'scroll_to_element' => true,
                'disable_interaction' => false,
                'animation' => 'fade',
                'position' => 'auto', // auto, top, bottom, left, right
            );

            // Permite filtrar tours
            $this->tours = apply_filters('nosfir_guided_tours', $this->tours);
            $this->tour_config = apply_filters('nosfir_guided_tour_config', $this->tour_config);
        }

        /**
         * Setup hooks
         */
        private function setup_hooks() {
            // Customizer tour
            add_action('customize_controls_init', array($this, 'maybe_start_customizer_tour'));
            add_action('customize_controls_enqueue_scripts', array($this, 'enqueue_customizer_tour'));
            add_action('customize_controls_print_footer_scripts', array($this, 'print_customizer_templates'));

            // Admin tours
            add_action('admin_init', array($this, 'maybe_start_admin_tour'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_tour'));
            add_action('admin_footer', array($this, 'print_admin_templates'));

            // Editor tour
            add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_tour'));

            // AJAX handlers
            add_action('wp_ajax_nosfir_tour_complete_step', array($this, 'ajax_complete_step'));
            add_action('wp_ajax_nosfir_tour_skip', array($this, 'ajax_skip_tour'));
            add_action('wp_ajax_nosfir_tour_complete', array($this, 'ajax_complete_tour'));
            add_action('wp_ajax_nosfir_tour_restart', array($this, 'ajax_restart_tour'));
            add_action('wp_ajax_nosfir_get_tour_steps', array($this, 'ajax_get_tour_steps'));

            // Menu items
            add_action('admin_menu', array($this, 'add_tour_menu'));

            // Admin bar
            add_action('admin_bar_menu', array($this, 'add_admin_bar_menu'), 100);
        }

        /**
         * Verifica se deve iniciar tour do Customizer
         */
        public function maybe_start_customizer_tour() {
            // Verifica parâmetros
            $start_tour = isset($_GET['nosfir_tour']) && $_GET['nosfir_tour'] === 'customizer';
            $from_wizard = isset($_GET['sf_starter_content']) || isset($_GET['sf_tasks']);
            
            // Inicia se vem do wizard ou se foi solicitado
            if (($start_tour || $from_wizard) && !$this->is_tour_completed('customizer')) {
                $this->active_tour = 'customizer';
            }
        }

        /**
         * Verifica se deve iniciar tour admin
         */
        public function maybe_start_admin_tour() {
            // Verifica se é primeira vez no admin após ativação
            if (get_option('nosfir_show_dashboard_tour', false) && !$this->is_tour_completed('dashboard')) {
                $this->active_tour = 'dashboard';
                delete_option('nosfir_show_dashboard_tour');
            }

            // Verifica parâmetro de URL
            if (isset($_GET['nosfir_tour'])) {
                $tour = sanitize_text_field($_GET['nosfir_tour']);
                if (isset($this->tours[$tour]) && $this->tours[$tour]['location'] === 'admin') {
                    $this->active_tour = $tour;
                }
            }
        }

        /**
         * Enqueue scripts do Customizer tour
         */
        public function enqueue_customizer_tour() {
            if (!$this->active_tour) {
                return;
            }

            $version = wp_get_theme()->get('Version');

            // CSS
            wp_enqueue_style(
                'nosfir-guided-tour',
                get_template_directory_uri() . '/assets/css/admin/guided-tour.css',
                array(),
                $version
            );

            // JavaScript principal
            wp_enqueue_script(
                'nosfir-guided-tour',
                get_template_directory_uri() . '/assets/js/admin/guided-tour.js',
                array('jquery', 'wp-backbone', 'customize-controls'),
                $version,
                true
            );

            // Driver.js para tours
            wp_enqueue_script(
                'driver-js',
                get_template_directory_uri() . '/assets/js/vendor/driver.min.js',
                array(),
                '0.9.8',
                true
            );

            wp_enqueue_style(
                'driver-js',
                get_template_directory_uri() . '/assets/css/vendor/driver.min.css',
                array(),
                '0.9.8'
            );

            // Localização
            wp_localize_script('nosfir-guided-tour', 'nosfirGuidedTour', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfir-guided-tour'),
                'tour' => $this->active_tour,
                'steps' => $this->get_tour_steps($this->active_tour),
                'config' => $this->tour_config,
                'strings' => $this->get_tour_strings(),
            ));
        }

        /**
         * Enqueue scripts do Admin tour
         */
        public function enqueue_admin_tour($hook) {
            if (!$this->active_tour) {
                return;
            }

            $version = wp_get_theme()->get('Version');

            // CSS
            wp_enqueue_style(
                'nosfir-guided-tour',
                get_template_directory_uri() . '/assets/css/admin/guided-tour.css',
                array(),
                $version
            );

            // JavaScript
            wp_enqueue_script(
                'nosfir-guided-tour',
                get_template_directory_uri() . '/assets/js/admin/guided-tour.js',
                array('jquery', 'wp-backbone'),
                $version,
                true
            );

            // Driver.js
            wp_enqueue_script(
                'driver-js',
                get_template_directory_uri() . '/assets/js/vendor/driver.min.js',
                array(),
                '0.9.8',
                true
            );

            wp_enqueue_style(
                'driver-js',
                get_template_directory_uri() . '/assets/css/vendor/driver.min.css',
                array(),
                '0.9.8'
            );

            // Shepherd.js como alternativa
            wp_enqueue_script(
                'shepherd-js',
                get_template_directory_uri() . '/assets/js/vendor/shepherd.min.js',
                array(),
                '8.0.0',
                true
            );

            wp_enqueue_style(
                'shepherd-js',
                get_template_directory_uri() . '/assets/css/vendor/shepherd.min.css',
                array(),
                '8.0.0'
            );

            // Localização
            wp_localize_script('nosfir-guided-tour', 'nosfirGuidedTour', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfir-guided-tour'),
                'tour' => $this->active_tour,
                'steps' => $this->get_tour_steps($this->active_tour),
                'config' => $this->tour_config,
                'strings' => $this->get_tour_strings(),
                'current_screen' => $hook,
            ));
        }

        /**
         * Enqueue scripts do Editor tour
         */
        public function enqueue_editor_tour() {
            if (isset($_GET['nosfir_tour']) && $_GET['nosfir_tour'] === 'editor') {
                $version = wp_get_theme()->get('Version');

                // CSS
                wp_enqueue_style(
                    'nosfir-guided-tour',
                    get_template_directory_uri() . '/assets/css/admin/guided-tour.css',
                    array(),
                    $version
                );

                // JavaScript
                wp_enqueue_script(
                    'nosfir-guided-tour-editor',
                    get_template_directory_uri() . '/assets/js/admin/guided-tour-editor.js',
                    array('wp-blocks', 'wp-element', 'wp-editor'),
                    $version,
                    true
                );

                // Localização
                wp_localize_script('nosfir-guided-tour-editor', 'nosfirGuidedTour', array(
                    'steps' => $this->get_tour_steps('editor'),
                    'config' => $this->tour_config,
                    'strings' => $this->get_tour_strings(),
                ));
            }
        }

        /**
         * Print templates do Customizer
         */
        public function print_customizer_templates() {
            if (!$this->active_tour) {
                return;
            }
            ?>
            <!-- Nosfir Guided Tour Templates -->
            <script type="text/html" id="tmpl-nosfir-guided-tour-step">
                <div class="nosfir-tour-step" data-step="{{ data.index }}">
                    <div class="nosfir-tour-header">
                        <# if (data.title) { #>
                            <h3 class="nosfir-tour-title">{{ data.title }}</h3>
                        <# } #>
                        <# if (data.show_progress) { #>
                            <div class="nosfir-tour-progress">
                                <span class="current">{{ data.index + 1 }}</span>
                                <span class="separator">/</span>
                                <span class="total">{{ data.total }}</span>
                            </div>
                        <# } #>
                    </div>
                    
                    <div class="nosfir-tour-content">
                        <# if (data.image) { #>
                            <img src="{{ data.image }}" alt="" class="nosfir-tour-image">
                        <# } #>
                        
                        <div class="nosfir-tour-message">
                            {{{ data.message }}}
                        </div>
                        
                        <# if (data.tip) { #>
                            <div class="nosfir-tour-tip">
                                <span class="dashicons dashicons-lightbulb"></span>
                                <span>{{{ data.tip }}}</span>
                            </div>
                        <# } #>
                    </div>
                    
                    <div class="nosfir-tour-footer">
                        <div class="nosfir-tour-actions">
                            <# if (!data.first_step) { #>
                                <button type="button" class="button nosfir-tour-prev">
                                    <?php _e('Previous', 'nosfir'); ?>
                                </button>
                            <# } #>
                            
                            <# if (data.last_step) { #>
                                <button type="button" class="button button-primary nosfir-tour-complete">
                                    <# if (data.button_text) { #>
                                        {{ data.button_text }}
                                    <# } else { #>
                                        <?php _e('Complete Tour', 'nosfir'); ?>
                                    <# } #>
                                </button>
                            <# } else { #>
                                <button type="button" class="button button-primary nosfir-tour-next">
                                    <# if (data.button_text) { #>
                                        {{ data.button_text }}
                                    <# } else { #>
                                        <?php _e('Next', 'nosfir'); ?>
                                    <# } #>
                                </button>
                            <# } #>
                        </div>
                        
                        <# if (!data.last_step) { #>
                            <a href="#" class="nosfir-tour-skip">
                                <?php _e('Skip tour', 'nosfir'); ?>
                            </a>
                        <# } #>
                    </div>
                </div>
            </script>

            <script type="text/html" id="tmpl-nosfir-tour-overlay">
                <div class="nosfir-tour-overlay">
                    <div class="nosfir-tour-spotlight"></div>
                </div>
            </script>

            <script type="text/html" id="tmpl-nosfir-tour-complete">
                <div class="nosfir-tour-complete">
                    <div class="nosfir-tour-complete-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <h3><?php _e('Tour Complete! 🎉', 'nosfir'); ?></h3>
                    <p><?php _e('Great job! You\'ve completed the tour.', 'nosfir'); ?></p>
                    <div class="nosfir-tour-complete-actions">
                        <button type="button" class="button button-primary nosfir-tour-close">
                            <?php _e('Close', 'nosfir'); ?>
                        </button>
                        <button type="button" class="button nosfir-tour-restart">
                            <?php _e('Restart Tour', 'nosfir'); ?>
                        </button>
                    </div>
                </div>
            </script>
            <?php
        }

        /**
         * Print templates admin
         */
        public function print_admin_templates() {
            if (!$this->active_tour) {
                return;
            }

            $this->print_customizer_templates(); // Reutiliza os mesmos templates
            ?>
            
            <!-- Additional Admin Templates -->
            <script type="text/html" id="tmpl-nosfir-tour-launcher">
                <div class="nosfir-tour-launcher">
                    <button type="button" class="button button-primary nosfir-start-tour">
                        <span class="dashicons dashicons-welcome-learn-more"></span>
                        <?php _e('Start Tour', 'nosfir'); ?>
                    </button>
                </div>
            </script>

            <script type="text/html" id="tmpl-nosfir-tour-tooltip">
                <div class="nosfir-tour-tooltip {{ data.position }}">
                    <div class="tooltip-arrow"></div>
                    <div class="tooltip-content">
                        <# if (data.title) { #>
                            <h4>{{ data.title }}</h4>
                        <# } #>
                        <p>{{{ data.content }}}</p>
                        <# if (data.action) { #>
                            <button type="button" class="button button-small {{ data.action.class }}">
                                {{ data.action.text }}
                            </button>
                        <# } #>
                    </div>
                </div>
            </script>
            <?php
        }

        /**
         * Obtém steps do Customizer
         */
        public function get_customizer_steps() {
            $steps = array();

            // Step 1: Welcome
            $steps[] = array(
                'title' => __('Welcome to the Customizer! 👋', 'nosfir'),
                'message' => __('This is where you can customize every aspect of your site\'s appearance. Let\'s take a quick tour to help you get started.', 'nosfir'),
                'element' => '#customize-info',
                'position' => 'right',
                'button_text' => __('Start Tour', 'nosfir'),
            );

            // Step 2: Site Identity
            if (!has_custom_logo()) {
                $steps[] = array(
                    'title' => __('Add Your Logo', 'nosfir'),
                    'message' => __('Click on "Site Identity" to upload your logo, change your site title and tagline.', 'nosfir'),
                    'element' => '#accordion-section-title_tagline',
                    'position' => 'right',
                    'tip' => __('Pro tip: Use a PNG with transparent background for best results.', 'nosfir'),
                );
            }

            // Step 3: Colors
            $steps[] = array(
                'title' => __('Customize Colors', 'nosfir'),
                'message' => __('Choose your brand colors here. These will be applied throughout your site.', 'nosfir'),
                'element' => '#accordion-panel-nosfir_colors_panel',
                'position' => 'right',
            );

            // Step 4: Typography
            $steps[] = array(
                'title' => __('Typography Settings', 'nosfir'),
                'message' => __('Select fonts that match your brand personality. You can choose different fonts for headings and body text.', 'nosfir'),
                'element' => '#accordion-panel-nosfir_typography_panel',
                'position' => 'right',
            );

            // Step 5: Layout
            $steps[] = array(
                'title' => __('Layout Options', 'nosfir'),
                'message' => __('Control the overall layout of your site, including sidebar positions and container widths.', 'nosfir'),
                'element' => '#accordion-panel-nosfir_layout_panel',
                'position' => 'right',
            );

            // Step 6: Header
            $steps[] = array(
                'title' => __('Header Settings', 'nosfir'),
                'message' => __('Customize your header layout, sticky behavior, and what elements to display.', 'nosfir'),
                'element' => '#accordion-section-nosfir_header',
                'position' => 'right',
            );

            // Step 7: Footer
            $steps[] = array(
                'title' => __('Footer Configuration', 'nosfir'),
                'message' => __('Set up your footer with widgets, copyright text, and social links.', 'nosfir'),
                'element' => '#accordion-section-nosfir_footer',
                'position' => 'right',
            );

            // Step 8: Menus
            $steps[] = array(
                'title' => __('Navigation Menus', 'nosfir'),
                'message' => __('Create and organize your navigation menus. You can have different menus for different locations.', 'nosfir'),
                'element' => '#accordion-panel-nav_menus',
                'position' => 'right',
            );

            // Step 9: Widgets
            $steps[] = array(
                'title' => __('Widget Areas', 'nosfir'),
                'message' => __('Add content to your sidebar and footer widget areas.', 'nosfir'),
                'element' => '#accordion-panel-widgets',
                'position' => 'right',
            );

            // Step 10: Homepage Settings
            $steps[] = array(
                'title' => __('Homepage Settings', 'nosfir'),
                'message' => __('Choose what to display on your homepage - latest posts or a static page.', 'nosfir'),
                'element' => '#accordion-section-static_front_page',
                'position' => 'right',
            );

            // Step 11: Additional CSS
            $steps[] = array(
                'title' => __('Custom CSS', 'nosfir'),
                'message' => __('Advanced users can add custom CSS here to further customize the appearance.', 'nosfir'),
                'element' => '#accordion-section-custom_css',
                'position' => 'right',
            );

            // Step 12: Save
            $steps[] = array(
                'title' => __('Save Your Changes', 'nosfir'),
                'message' => __('Remember to click "Publish" to save all your changes. You can also schedule changes for later!', 'nosfir'),
                'element' => '#save',
                'position' => 'bottom',
                'button_text' => __('Finish Tour', 'nosfir'),
            );

            return apply_filters('nosfir_customizer_tour_steps', $steps);
        }

        /**
         * Obtém steps do Dashboard
         */
        public function get_dashboard_steps() {
            $steps = array();

            $steps[] = array(
                'title' => __('Welcome to Your Dashboard! 🎉', 'nosfir'),
                'message' => __('This is your site\'s command center. Let me show you around!', 'nosfir'),
                'element' => '#wpcontent',
                'position' => 'center',
            );

            $steps[] = array(
                'title' => __('Admin Menu', 'nosfir'),
                'message' => __('This is your main navigation. All your site\'s features are accessible from here.', 'nosfir'),
                'element' => '#adminmenu',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Nosfir Theme', 'nosfir'),
                'message' => __('Access all theme-specific features and settings from this menu.', 'nosfir'),
                'element' => '#toplevel_page_nosfir-dashboard',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Quick Actions', 'nosfir'),
                'message' => __('The toolbar provides quick access to common actions and your profile.', 'nosfir'),
                'element' => '#wpadminbar',
                'position' => 'bottom',
            );

            $steps[] = array(
                'title' => __('Dashboard Widgets', 'nosfir'),
                'message' => __('These widgets show important information about your site. You can customize them using Screen Options.', 'nosfir'),
                'element' => '#dashboard-widgets',
                'position' => 'top',
            );

            return apply_filters('nosfir_dashboard_tour_steps', $steps);
        }

        /**
         * Obtém steps do Editor
         */
        public function get_editor_steps() {
            $steps = array();

            $steps[] = array(
                'title' => __('Block Editor Tour', 'nosfir'),
                'message' => __('Welcome to the WordPress Block Editor! Let\'s explore how to create amazing content.', 'nosfir'),
                'element' => '.edit-post-layout',
                'position' => 'center',
            );

            $steps[] = array(
                'title' => __('Add Blocks', 'nosfir'),
                'message' => __('Click the + button to add new blocks. Each block is a content element like a paragraph, image, or heading.', 'nosfir'),
                'element' => '.edit-post-header-toolbar__inserter-toggle',
                'position' => 'bottom',
            );

            $steps[] = array(
                'title' => __('Block Settings', 'nosfir'),
                'message' => __('When you select a block, its settings appear here. Customize colors, alignment, and more.', 'nosfir'),
                'element' => '.edit-post-sidebar',
                'position' => 'left',
            );

            $steps[] = array(
                'title' => __('Document Settings', 'nosfir'),
                'message' => __('Control page/post settings like categories, tags, and featured image from here.', 'nosfir'),
                'element' => '.edit-post-sidebar__panel-tabs',
                'position' => 'left',
            );

            return apply_filters('nosfir_editor_tour_steps', $steps);
        }

        /**
         * Obtém steps do WooCommerce
         */
        public function get_woocommerce_steps() {
            $steps = array();

            if (!class_exists('WooCommerce')) {
                return $steps;
            }

            $steps[] = array(
                'title' => __('WooCommerce Setup', 'nosfir'),
                'message' => __('Let\'s set up your online store! This tour will guide you through the essentials.', 'nosfir'),
                'element' => '#toplevel_page_woocommerce',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Products', 'nosfir'),
                'message' => __('Add and manage your products from here. You can create simple or variable products.', 'nosfir'),
                'element' => '#menu-posts-product',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Orders', 'nosfir'),
                'message' => __('View and manage customer orders. Process refunds and track shipments.', 'nosfir'),
                'element' => '.toplevel_page_woocommerce li:nth-child(2)',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Settings', 'nosfir'),
                'message' => __('Configure shipping, payments, taxes, and other store settings.', 'nosfir'),
                'element' => '.toplevel_page_woocommerce li:nth-child(6)',
                'position' => 'right',
            );

            return apply_filters('nosfir_woocommerce_tour_steps', $steps);
        }

        /**
         * Obtém steps de Theme Features
         */
        public function get_theme_features_steps() {
            $steps = array();

            $steps[] = array(
                'title' => __('Discover Nosfir Features', 'nosfir'),
                'message' => __('Let\'s explore the powerful features that make Nosfir special!', 'nosfir'),
                'element' => 'body',
                'position' => 'center',
            );

            $steps[] = array(
                'title' => __('Demo Import', 'nosfir'),
                'message' => __('Import professionally designed demos with just one click.', 'nosfir'),
                'element' => '#toplevel_page_nosfir-dashboard .wp-submenu a[href*="demo-import"]',
                'position' => 'right',
            );

            $steps[] = array(
                'title' => __('Theme Settings', 'nosfir'),
                'message' => __('Advanced theme settings for performance, SEO, and more.', 'nosfir'),
                'element' => '#toplevel_page_nosfir-dashboard .wp-submenu a[href*="settings"]',
                'position' => 'right',
            );

            return apply_filters('nosfir_theme_features_tour_steps', $steps);
        }

        /**
         * Adiciona menu de tours
         */
        public function add_tour_menu() {
            add_submenu_page(
                'nosfir-dashboard',
                __('Guided Tours', 'nosfir'),
                __('Tours', 'nosfir'),
                'manage_options',
                'nosfir-tours',
                array($this, 'render_tours_page')
            );
        }

        /**
         * Renderiza página de tours
         */
        public function render_tours_page() {
            ?>
            <div class="nosfir-admin-wrap">
                <header class="nosfir-dashboard-header">
                    <div class="nosfir-dashboard-brand">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="Nosfir">
                        <div class="nosfir-dashboard-titles">
                            <h1><?php _e('Guided Tours', 'nosfir'); ?></h1>
                        </div>
                    </div>
                    <div class="nosfir-dashboard-actions">
                        <a class="button" href="<?php echo esc_url($this->get_tour_start_url('dashboard')); ?>">
                            <?php _e('Start Dashboard Tour', 'nosfir'); ?>
                        </a>
                        <a class="button" href="<?php echo esc_url($this->get_tour_start_url('customizer')); ?>">
                            <?php _e('Start Customizer Tour', 'nosfir'); ?>
                        </a>
                    </div>
                </header>

                <section class="nosfir-dashboard-grid">
                    <?php foreach ($this->tours as $tour_id => $tour) : ?>
                        <?php
                        if (isset($tour['condition'])) {
                            if ($tour['condition'] === 'woocommerce_active' && !class_exists('WooCommerce')) {
                                continue;
                            }
                        }
                        $is_completed = $this->is_tour_completed($tour_id);
                        $tour_url = $this->get_tour_start_url($tour_id);
                        ?>
                        <article class="nosfir-card <?php echo $is_completed ? 'completed' : ''; ?>">
                            <h2><?php echo esc_html($tour['name']); ?></h2>
                            <p><?php echo esc_html($tour['description']); ?></p>
                            <?php if ($is_completed) : ?>
                                <p class="tour-status"><span class="dashicons dashicons-yes-alt"></span> <?php _e('Completed', 'nosfir'); ?></p>
                            <?php endif; ?>
                            <div class="nosfir-quick-actions">
                                <a href="<?php echo esc_url($tour_url); ?>" class="button button-primary">
                                    <?php echo $is_completed ? __('Restart Tour', 'nosfir') : __('Start Tour', 'nosfir'); ?>
                                </a>
                                <?php if ($is_completed) : ?>
                                    <button type="button" class="button nosfir-reset-tour" data-tour="<?php echo esc_attr($tour_id); ?>">
                                        <?php _e('Reset', 'nosfir'); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            </div>
            <?php
        }

        /**
         * Adiciona menu na admin bar
         */
        public function add_admin_bar_menu($wp_admin_bar) {
            if (!current_user_can('manage_options')) {
                return;
            }

            $wp_admin_bar->add_node(array(
                'id' => 'nosfir-tours',
                'title' => '<span class="ab-icon dashicons dashicons-welcome-learn-more"></span>' . __('Tours', 'nosfir'),
                'href' => admin_url('admin.php?page=nosfir-tours'),
                'meta' => array(
                    'title' => __('Guided Tours', 'nosfir'),
                ),
            ));

            foreach ($this->tours as $tour_id => $tour) {
                $wp_admin_bar->add_node(array(
                    'id' => 'nosfir-tour-' . $tour_id,
                    'parent' => 'nosfir-tours',
                    'title' => $tour['name'],
                    'href' => $this->get_tour_start_url($tour_id),
                ));
            }
        }

        /**
         * Helpers
         */

        /**
         * Obtém steps de um tour
         */
        private function get_tour_steps($tour_id) {
            if (!isset($this->tours[$tour_id])) {
                return array();
            }

            $tour = $this->tours[$tour_id];
            
            if (isset($tour['steps']) && is_callable($tour['steps'])) {
                return call_user_func($tour['steps']);
            }

            return array();
        }

        /**
         * Obtém strings do tour
         */
        private function get_tour_strings() {
            return array(
                'next' => __('Next', 'nosfir'),
                'previous' => __('Previous', 'nosfir'),
                'close' => __('Close', 'nosfir'),
                'skip' => __('Skip tour', 'nosfir'),
                'complete' => __('Complete', 'nosfir'),
                'restart' => __('Restart', 'nosfir'),
                'step' => __('Step', 'nosfir'),
                'of' => __('of', 'nosfir'),
            );
        }

        /**
         * Verifica se tour foi completado
         */
        private function is_tour_completed($tour_id) {
            return isset($this->tours_status[$tour_id]['completed']) && 
                   $this->tours_status[$tour_id]['completed'] === true;
        }

        /**
         * Obtém URL para iniciar tour
         */
        private function get_tour_start_url($tour_id) {
            if (!isset($this->tours[$tour_id])) {
                return '#';
            }

            $tour = $this->tours[$tour_id];
            
            if ($tour['location'] === 'customizer') {
                return admin_url('customize.php?nosfir_tour=' . $tour_id);
            } elseif ($tour['location'] === 'editor') {
                return admin_url('post-new.php?nosfir_tour=' . $tour_id);
            } else {
                return admin_url('index.php?nosfir_tour=' . $tour_id);
            }
        }

        /**
         * AJAX: Complete step
         */
        public function ajax_complete_step() {
            check_ajax_referer('nosfir-guided-tour', 'nonce');

            $tour_id = isset($_POST['tour']) ? sanitize_text_field($_POST['tour']) : '';
            $step = isset($_POST['step']) ? intval($_POST['step']) : 0;

            if (!$tour_id) {
                wp_send_json_error('Invalid tour');
            }

            // Salva progresso
            if (!isset($this->tours_status[$tour_id])) {
                $this->tours_status[$tour_id] = array();
            }

            $this->tours_status[$tour_id]['last_step'] = $step;
            update_option('nosfir_guided_tours_status', $this->tours_status);

            wp_send_json_success(array(
                'message' => 'Step completed',
                'tour' => $tour_id,
                'step' => $step,
            ));
        }

        /**
         * AJAX: Complete tour
         */
        public function ajax_complete_tour() {
            check_ajax_referer('nosfir-guided-tour', 'nonce');

            $tour_id = isset($_POST['tour']) ? sanitize_text_field($_POST['tour']) : '';

            if (!$tour_id) {
                wp_send_json_error('Invalid tour');
            }

            // Marca como completo
            $this->tours_status[$tour_id] = array(
                'completed' => true,
                'completed_at' => current_time('timestamp'),
            );

            update_option('nosfir_guided_tours_status', $this->tours_status);

            wp_send_json_success(array(
                'message' => 'Tour completed',
                'tour' => $tour_id,
            ));
        }

        /**
         * AJAX: Skip tour
         */
        public function ajax_skip_tour() {
            check_ajax_referer('nosfir-guided-tour', 'nonce');

            $tour_id = isset($_POST['tour']) ? sanitize_text_field($_POST['tour']) : '';

            if (!$tour_id) {
                wp_send_json_error('Invalid tour');
            }

            // Marca como pulado
            $this->tours_status[$tour_id] = array(
                'skipped' => true,
                'skipped_at' => current_time('timestamp'),
            );

            update_option('nosfir_guided_tours_status', $this->tours_status);

            wp_send_json_success();
        }

        /**
         * AJAX: Restart tour
         */
        public function ajax_restart_tour() {
            check_ajax_referer('nosfir-guided-tour', 'nonce');

            $tour_id = isset($_POST['tour']) ? sanitize_text_field($_POST['tour']) : '';

            if (!$tour_id) {
                wp_send_json_error('Invalid tour');
            }

            // Remove status do tour
            unset($this->tours_status[$tour_id]);
            update_option('nosfir_guided_tours_status', $this->tours_status);

            wp_send_json_success();
        }
    }

endif;

// Inicializa
return Nosfir_NUX_Guided_Tour::get_instance();
