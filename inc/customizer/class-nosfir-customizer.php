<?php
/**
 * Nosfir Customizer Class
 *
 * Gerencia todas as funcionalidades do Customizer do tema,
 * incluindo panels, sections, controls e live preview.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_Customizer')) :

    /**
     * Classe principal do Customizer do Nosfir
     */
    class Nosfir_Customizer {

        /**
         * Instance única da classe
         *
         * @var Nosfir_Customizer
         */
        private static $instance = null;

        /**
         * Prefixo para settings
         *
         * @var string
         */
        private $prefix = 'nosfir_';

        /**
         * Valores padrão dos settings
         *
         * @var array
         */
        private $defaults = array();

        /**
         * Panels registrados
         *
         * @var array
         */
        private $panels = array();

        /**
         * Sections registradas
         *
         * @var array
         */
        private $sections = array();

        /**
         * Controls customizados carregados
         *
         * @var array
         */
        private $custom_controls = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_Customizer
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
            // Define valores padrão
            $this->set_defaults();
            
            // Hooks principais
            add_action('customize_register', array($this, 'register_customize_sections'), 10);
            add_action('customize_register', array($this, 'load_custom_controls'), 5);
            add_action('customize_register', array($this, 'register_settings'), 15);
            add_action('customize_register', array($this, 'edit_default_controls'), 99);
            
            // Selective refresh
            add_action('customize_register', array($this, 'selective_refresh'), 20);
            
            // Preview scripts
            add_action('customize_preview_init', array($this, 'customize_preview_js'));
            add_action('customize_controls_enqueue_scripts', array($this, 'customize_controls_js'));
            add_action('customize_controls_print_styles', array($this, 'customize_controls_css'));
            
            // Front-end CSS
            add_action('wp_enqueue_scripts', array($this, 'customizer_css'), 130);
            add_action('enqueue_block_assets', array($this, 'block_editor_css'));
            
            // Body classes
            add_filter('body_class', array($this, 'body_classes'));
            
            // Default values
            add_action('init', array($this, 'default_theme_mod_values'), 10);
            
            // Export/Import
            add_action('customize_register', array($this, 'register_export_import'), 100);
            
            // AJAX handlers
            add_action('wp_ajax_nosfir_reset_customizer', array($this, 'ajax_reset_customizer'));
            add_action('wp_ajax_nosfir_export_customizer', array($this, 'ajax_export_customizer'));
            add_action('wp_ajax_nosfir_import_customizer', array($this, 'ajax_import_customizer'));
        }

        /**
         * Define valores padrão para o tema
         */
        private function set_defaults() {
            $this->defaults = apply_filters('nosfir_customizer_defaults', array(
                // Colors - General
                'nosfir_primary_color' => '#2563eb',
                'nosfir_secondary_color' => '#64748b',
                'nosfir_accent_color' => '#f59e0b',
                'nosfir_success_color' => '#10b981',
                'nosfir_warning_color' => '#f59e0b',
                'nosfir_danger_color' => '#ef4444',
                'nosfir_info_color' => '#3b82f6',
                'nosfir_dark_color' => '#1f2937',
                'nosfir_light_color' => '#f9fafb',
                
                // Typography
                'nosfir_body_font_family' => 'Inter, system-ui, sans-serif',
                'nosfir_heading_font_family' => 'Inter, system-ui, sans-serif',
                'nosfir_body_font_size' => '16px',
                'nosfir_body_line_height' => '1.6',
                'nosfir_heading_color' => '#111827',
                'nosfir_text_color' => '#4b5563',
                'nosfir_link_color' => '#2563eb',
                'nosfir_link_hover_color' => '#1d4ed8',
                
                // Header
                'nosfir_header_layout' => 'layout-1',
                'nosfir_header_width' => 'container',
                'nosfir_header_background' => '#ffffff',
                'nosfir_header_text_color' => '#111827',
                'nosfir_header_link_color' => '#4b5563',
                'nosfir_header_sticky' => true,
                'nosfir_header_transparent' => false,
                'nosfir_header_search' => true,
                'nosfir_header_cart' => true,
                'nosfir_header_account' => true,
                
                // Navigation
                'nosfir_nav_background' => '#ffffff',
                'nosfir_nav_link_color' => '#4b5563',
                'nosfir_nav_link_hover_color' => '#2563eb',
                'nosfir_nav_dropdown_background' => '#ffffff',
                'nosfir_nav_dropdown_link_color' => '#4b5563',
                'nosfir_mobile_menu_breakpoint' => '1024',
                
                // Footer
                'nosfir_footer_layout' => 'layout-1',
                'nosfir_footer_widgets' => 4,
                'nosfir_footer_background' => '#111827',
                'nosfir_footer_text_color' => '#9ca3af',
                'nosfir_footer_heading_color' => '#ffffff',
                'nosfir_footer_link_color' => '#d1d5db',
                'nosfir_footer_copyright' => '© ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.',
                
                // Layout
                'nosfir_site_layout' => 'wide',
                'nosfir_container_width' => '1280px',
                'nosfir_sidebar_layout' => 'right',
                'nosfir_sidebar_width' => '30',
                'nosfir_blog_layout' => 'grid',
                'nosfir_blog_columns' => '3',
                'nosfir_single_layout' => 'right-sidebar',
                'nosfir_page_layout' => 'no-sidebar',
                
                // Buttons
                'nosfir_button_background' => '#2563eb',
                'nosfir_button_text_color' => '#ffffff',
                'nosfir_button_hover_background' => '#1d4ed8',
                'nosfir_button_hover_text_color' => '#ffffff',
                'nosfir_button_border_radius' => '6px',
                'nosfir_button_padding' => '12px 24px',
                
                // Forms
                'nosfir_form_field_background' => '#ffffff',
                'nosfir_form_field_border' => '#d1d5db',
                'nosfir_form_field_text' => '#111827',
                'nosfir_form_field_focus_border' => '#2563eb',
                'nosfir_form_field_radius' => '6px',
                
                // Blog
                'nosfir_blog_excerpt_length' => 30,
                'nosfir_blog_read_more_text' => __('Read More', 'nosfir'),
                'nosfir_blog_meta_author' => true,
                'nosfir_blog_meta_date' => true,
                'nosfir_blog_meta_category' => true,
                'nosfir_blog_meta_comments' => true,
                'nosfir_blog_featured_image' => true,
                'nosfir_blog_pagination_type' => 'numbers',
                
                // Shop (WooCommerce)
                'nosfir_shop_layout' => 'grid',
                'nosfir_shop_columns' => 4,
                'nosfir_shop_products_per_page' => 12,
                'nosfir_shop_sidebar' => 'left',
                'nosfir_product_gallery_zoom' => true,
                'nosfir_product_gallery_lightbox' => true,
                'nosfir_product_gallery_slider' => true,
                
                // Performance
                'nosfir_lazy_load_images' => true,
                'nosfir_lazy_load_videos' => true,
                'nosfir_preload_fonts' => true,
                'nosfir_minify_css' => false,
                'nosfir_minify_js' => false,
                
                // Advanced
                'nosfir_custom_css' => '',
                'nosfir_custom_js' => '',
                'nosfir_google_analytics' => '',
                'nosfir_google_fonts_api_key' => '',
                
                // Background
                'background_color' => '#ffffff',
            ));
        }

        /**
         * Obtém valores padrão
         */
        public function get_defaults() {
            return $this->defaults;
        }

        /**
         * Obtém um valor padrão específico
         */
        public function get_default($setting) {
            return isset($this->defaults[$setting]) ? $this->defaults[$setting] : '';
        }

        /**
         * Carrega controles customizados
         */
        public function load_custom_controls($wp_customize) {
            // Caminho base dos controles
            $controls_path = get_template_directory() . '/inc/customizer/';
            
            // Lista de controles customizados
            $custom_controls = array(
                'arbitrary' => 'class-nosfir-customizer-control-arbitrary.php',
                'more' => 'class-nosfir-customizer-control-more.php',
                'radio-image' => 'class-nosfir-customizer-control-radio-image.php',
                'range' => 'class-nosfir-customizer-control-range.php',
                'toggle' => 'class-nosfir-customizer-control-toggle.php',
                'select2' => 'class-nosfir-customizer-control-select2.php',
                'typography' => 'class-nosfir-customizer-control-typography.php',
                'gradient' => 'class-nosfir-customizer-control-gradient.php',
                'dimensions' => 'class-nosfir-customizer-control-dimensions.php',
                'sortable' => 'class-nosfir-customizer-control-sortable.php',
            );
            
            foreach ($custom_controls as $control => $file) {
                $file_path = $controls_path . $file;
                if (file_exists($file_path)) {
                    require_once $file_path;
                    $this->custom_controls[$control] = true;
                }
            }
            
            // Permite que outros plugins/temas adicionem controles
            do_action('nosfir_load_custom_controls', $wp_customize);
        }

        /**
         * Registra panels e sections
         */
        public function register_customize_sections($wp_customize) {
            // Remove sections desnecessárias
            $wp_customize->remove_section('colors');
            $wp_customize->remove_section('header_image');
            $wp_customize->remove_section('background_image');
            
            // Panel Principal - Nosfir Options
            $wp_customize->add_panel('nosfir_panel', array(
                'title' => __('Nosfir Options', 'nosfir'),
                'priority' => 10,
                'capability' => 'edit_theme_options',
            ));
            
            // Panel - Typography
            $wp_customize->add_panel('nosfir_typography_panel', array(
                'title' => __('Typography', 'nosfir'),
                'priority' => 20,
                'capability' => 'edit_theme_options',
            ));
            
            // Panel - Colors
            $wp_customize->add_panel('nosfir_colors_panel', array(
                'title' => __('Colors', 'nosfir'),
                'priority' => 25,
                'capability' => 'edit_theme_options',
            ));
            
            // Panel - Layout
            $wp_customize->add_panel('nosfir_layout_panel', array(
                'title' => __('Layout', 'nosfir'),
                'priority' => 30,
                'capability' => 'edit_theme_options',
            ));
            
            // Section - General Settings
            $wp_customize->add_section('nosfir_general', array(
                'title' => __('General Settings', 'nosfir'),
                'panel' => 'nosfir_panel',
                'priority' => 10,
            ));
            
            // Section - Header
            $wp_customize->add_section('nosfir_header', array(
                'title' => __('Header', 'nosfir'),
                'panel' => 'nosfir_panel',
                'priority' => 20,
            ));
            
            // Section - Navigation
            $wp_customize->add_section('nosfir_navigation', array(
                'title' => __('Navigation', 'nosfir'),
                'panel' => 'nosfir_panel',
                'priority' => 25,
            ));
            
            // Section - Footer
            $wp_customize->add_section('nosfir_footer', array(
                'title' => __('Footer', 'nosfir'),
                'panel' => 'nosfir_panel',
                'priority' => 30,
            ));
            
            // Section - Blog
            $wp_customize->add_section('nosfir_blog', array(
                'title' => __('Blog', 'nosfir'),
                'panel' => 'nosfir_panel',
                'priority' => 35,
            ));
            
            // Section - Typography - Body
            $wp_customize->add_section('nosfir_typography_body', array(
                'title' => __('Body Typography', 'nosfir'),
                'panel' => 'nosfir_typography_panel',
                'priority' => 10,
            ));
            
            // Section - Typography - Headings
            $wp_customize->add_section('nosfir_typography_headings', array(
                'title' => __('Headings Typography', 'nosfir'),
                'panel' => 'nosfir_typography_panel',
                'priority' => 20,
            ));
            
            // Section - Colors - Global
            $wp_customize->add_section('nosfir_colors_global', array(
                'title' => __('Global Colors', 'nosfir'),
                'panel' => 'nosfir_colors_panel',
                'priority' => 10,
            ));
            
            // Section - Colors - Header
            $wp_customize->add_section('nosfir_colors_header', array(
                'title' => __('Header Colors', 'nosfir'),
                'panel' => 'nosfir_colors_panel',
                'priority' => 20,
            ));
            
            // Section - Colors - Footer
            $wp_customize->add_section('nosfir_colors_footer', array(
                'title' => __('Footer Colors', 'nosfir'),
                'panel' => 'nosfir_colors_panel',
                'priority' => 30,
            ));
            
            // Section - Layout - Site
            $wp_customize->add_section('nosfir_layout_site', array(
                'title' => __('Site Layout', 'nosfir'),
                'panel' => 'nosfir_layout_panel',
                'priority' => 10,
            ));
            
            // Section - Layout - Sidebar
            $wp_customize->add_section('nosfir_layout_sidebar', array(
                'title' => __('Sidebar Layout', 'nosfir'),
                'panel' => 'nosfir_layout_panel',
                'priority' => 20,
            ));
            
            // WooCommerce sections se ativo
            if (class_exists('WooCommerce')) {
                $wp_customize->add_section('nosfir_woocommerce', array(
                    'title' => __('Shop Settings', 'nosfir'),
                    'panel' => 'nosfir_panel',
                    'priority' => 40,
                ));
            }
            
            // Section - Performance
            $wp_customize->add_section('nosfir_performance', array(
                'title' => __('Performance', 'nosfir'),
                'priority' => 100,
            ));
            
            // Section - Advanced
            $wp_customize->add_section('nosfir_advanced', array(
                'title' => __('Advanced Settings', 'nosfir'),
                'priority' => 110,
            ));
            
            // Section - More/Upsell
            if (apply_filters('nosfir_show_pro_section', true)) {
                $wp_customize->add_section('nosfir_more', array(
                    'title' => __('More Features', 'nosfir'),
                    'priority' => 999,
                ));
            }
        }

        /**
         * Registra settings e controls
         */
        public function register_settings($wp_customize) {
            // Inclui arquivos de settings específicos
            $settings_files = array(
                'general',
                'colors',
                'typography',
                'header',
                'navigation',
                'footer',
                'layout',
                'blog',
                'performance',
                'advanced'
            );
            
            foreach ($settings_files as $file) {
                $file_path = get_template_directory() . '/inc/customizer/settings/' . $file . '.php';
                if (file_exists($file_path)) {
                    require_once $file_path;
                }
            }
            
            // Exemplo de settings inline (os outros estariam nos arquivos separados)
            $this->register_general_settings($wp_customize);
            $this->register_color_settings($wp_customize);
            $this->register_typography_settings($wp_customize);
            $this->register_header_settings($wp_customize);
            $this->register_navigation_settings($wp_customize);
            $this->register_footer_settings($wp_customize);
            $this->register_layout_settings($wp_customize);
            
            // WooCommerce settings
            if (class_exists('WooCommerce')) {
                $this->register_woocommerce_settings($wp_customize);
            }
            
            // More/Upsell section
            $this->register_more_section($wp_customize);
        }

        /**
         * Registra settings gerais
         */
        private function register_general_settings($wp_customize) {
            // Site Layout
            $wp_customize->add_setting('nosfir_site_layout', array(
                'default' => $this->get_default('nosfir_site_layout'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control(new Nosfir_Customizer_Control_Radio_Image(
                $wp_customize,
                'nosfir_site_layout',
                array(
                    'label' => __('Site Layout', 'nosfir'),
                    'section' => 'nosfir_general',
                    'choices' => array(
                        'wide' => get_template_directory_uri() . '/assets/images/customizer/layout-wide.svg',
                        'boxed' => get_template_directory_uri() . '/assets/images/customizer/layout-boxed.svg',
                        'framed' => get_template_directory_uri() . '/assets/images/customizer/layout-framed.svg',
                    ),
                    'priority' => 10,
                )
            ));
            
            // Container Width
            $wp_customize->add_setting('nosfir_container_width', array(
                'default' => $this->get_default('nosfir_container_width'),
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control('nosfir_container_width', array(
                'label' => __('Container Width', 'nosfir'),
                'section' => 'nosfir_general',
                'type' => 'text',
                'priority' => 20,
            ));
        }

        /**
         * Registra settings de cores
         */
        private function register_color_settings($wp_customize) {
            // Primary Color
            $wp_customize->add_setting('nosfir_primary_color', array(
                'default' => $this->get_default('nosfir_primary_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_primary_color',
                array(
                    'label' => __('Primary Color', 'nosfir'),
                    'section' => 'nosfir_colors_global',
                    'priority' => 10,
                )
            ));
            
            // Secondary Color
            $wp_customize->add_setting('nosfir_secondary_color', array(
                'default' => $this->get_default('nosfir_secondary_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_secondary_color',
                array(
                    'label' => __('Secondary Color', 'nosfir'),
                    'section' => 'nosfir_colors_global',
                    'priority' => 20,
                )
            ));
            
            // Accent Color
            $wp_customize->add_setting('nosfir_accent_color', array(
                'default' => $this->get_default('nosfir_accent_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_accent_color',
                array(
                    'label' => __('Accent Color', 'nosfir'),
                    'section' => 'nosfir_colors_global',
                    'priority' => 30,
                )
            ));
        }

        /**
         * Registra settings de tipografia
         */
        private function register_typography_settings($wp_customize) {
            // Body Font Family
            $wp_customize->add_setting('nosfir_body_font_family', array(
                'default' => $this->get_default('nosfir_body_font_family'),
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control('nosfir_body_font_family', array(
                'label' => __('Body Font Family', 'nosfir'),
                'section' => 'nosfir_typography_body',
                'type' => 'select',
                'choices' => $this->get_font_choices(),
                'priority' => 10,
            ));
            
            // Body Font Size
            $wp_customize->add_setting('nosfir_body_font_size', array(
                'default' => $this->get_default('nosfir_body_font_size'),
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control('nosfir_body_font_size', array(
                'label' => __('Body Font Size', 'nosfir'),
                'section' => 'nosfir_typography_body',
                'type' => 'text',
                'priority' => 20,
            ));
        }

        /**
         * Registra settings do header
         */
        private function register_header_settings($wp_customize) {
            // Header Layout
            $wp_customize->add_setting('nosfir_header_layout', array(
                'default' => $this->get_default('nosfir_header_layout'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control(new Nosfir_Customizer_Control_Radio_Image(
                $wp_customize,
                'nosfir_header_layout',
                array(
                    'label' => __('Header Layout', 'nosfir'),
                    'section' => 'nosfir_header',
                    'choices' => array(
                        'layout-1' => get_template_directory_uri() . '/assets/images/customizer/header-1.svg',
                        'layout-2' => get_template_directory_uri() . '/assets/images/customizer/header-2.svg',
                        'layout-3' => get_template_directory_uri() . '/assets/images/customizer/header-3.svg',
                    ),
                    'priority' => 10,
                )
            ));
            
            // Sticky Header
            $wp_customize->add_setting('nosfir_header_sticky', array(
                'default' => $this->get_default('nosfir_header_sticky'),
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control('nosfir_header_sticky', array(
                'label' => __('Enable Sticky Header', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'checkbox',
                'priority' => 20,
            ));

            // Header Width
            $wp_customize->add_setting('nosfir_header_width', array(
                'default' => $this->get_default('nosfir_header_width'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_header_width', array(
                'label' => __('Header Width', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'select',
                'choices' => array(
                    'container' => __('Contained', 'nosfir'),
                    'full' => __('Full Width', 'nosfir'),
                ),
                'priority' => 30,
            ));

            // Transparent Header
            $wp_customize->add_setting('nosfir_header_transparent', array(
                'default' => $this->get_default('nosfir_header_transparent'),
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_header_transparent', array(
                'label' => __('Transparent Header', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'checkbox',
                'priority' => 40,
            ));

            // Header Colors
            $wp_customize->add_setting('nosfir_header_background', array(
                'default' => $this->get_default('nosfir_header_background'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_header_background',
                array(
                    'label' => __('Header Background', 'nosfir'),
                    'section' => 'nosfir_header',
                    'priority' => 50,
                )
            ));

            $wp_customize->add_setting('nosfir_header_text_color', array(
                'default' => $this->get_default('nosfir_header_text_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_header_text_color',
                array(
                    'label' => __('Header Text Color', 'nosfir'),
                    'section' => 'nosfir_header',
                    'priority' => 55,
                )
            ));

            $wp_customize->add_setting('nosfir_header_link_color', array(
                'default' => $this->get_default('nosfir_header_link_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_header_link_color',
                array(
                    'label' => __('Header Link Color', 'nosfir'),
                    'section' => 'nosfir_header',
                    'priority' => 60,
                )
            ));

            // Header Components
            $wp_customize->add_setting('nosfir_header_search', array(
                'default' => $this->get_default('nosfir_header_search'),
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_header_search', array(
                'label' => __('Show Header Search', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'checkbox',
                'priority' => 70,
            ));

            $wp_customize->add_setting('nosfir_header_cart', array(
                'default' => $this->get_default('nosfir_header_cart'),
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_header_cart', array(
                'label' => __('Show Header Cart', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'checkbox',
                'priority' => 75,
            ));

            $wp_customize->add_setting('nosfir_header_account', array(
                'default' => $this->get_default('nosfir_header_account'),
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_header_account', array(
                'label' => __('Show Header Account', 'nosfir'),
                'section' => 'nosfir_header',
                'type' => 'checkbox',
                'priority' => 80,
            ));
        }

        /**
         * Registra settings do footer
         */
        private function register_footer_settings($wp_customize) {
            // Footer Layout
            $wp_customize->add_setting('nosfir_footer_layout', array(
                'default' => $this->get_default('nosfir_footer_layout'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control(new Nosfir_Customizer_Control_Radio_Image(
                $wp_customize,
                'nosfir_footer_layout',
                array(
                    'label' => __('Footer Layout', 'nosfir'),
                    'section' => 'nosfir_footer',
                    'choices' => array(
                        'layout-1' => get_template_directory_uri() . '/assets/images/customizer/footer-1.svg',
                        'layout-2' => get_template_directory_uri() . '/assets/images/customizer/footer-2.svg',
                        'layout-3' => get_template_directory_uri() . '/assets/images/customizer/footer-3.svg',
                    ),
                    'priority' => 10,
                )
            ));
            
            // Footer Widgets
            $wp_customize->add_setting('nosfir_footer_widgets', array(
                'default' => $this->get_default('nosfir_footer_widgets'),
                'sanitize_callback' => 'absint',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control('nosfir_footer_widgets', array(
                'label' => __('Number of Footer Widget Areas', 'nosfir'),
                'section' => 'nosfir_footer',
                'type' => 'select',
                'choices' => array(
                    '0' => __('None', 'nosfir'),
                    '1' => __('1 Column', 'nosfir'),
                    '2' => __('2 Columns', 'nosfir'),
                    '3' => __('3 Columns', 'nosfir'),
                    '4' => __('4 Columns', 'nosfir'),
                ),
                'priority' => 20,
            ));
            
            // Copyright Text
            $wp_customize->add_setting('nosfir_footer_copyright', array(
                'default' => $this->get_default('nosfir_footer_copyright'),
                'sanitize_callback' => 'wp_kses_post',
                'transport' => 'postMessage',
            ));
            
            $wp_customize->add_control('nosfir_footer_copyright', array(
                'label' => __('Copyright Text', 'nosfir'),
                'section' => 'nosfir_footer',
                'type' => 'textarea',
                'priority' => 30,
            ));

            // Footer Colors
            $wp_customize->add_setting('nosfir_footer_background', array(
                'default' => $this->get_default('nosfir_footer_background'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_footer_background',
                array(
                    'label' => __('Footer Background', 'nosfir'),
                    'section' => 'nosfir_footer',
                    'priority' => 40,
                )
            ));

            $wp_customize->add_setting('nosfir_footer_text_color', array(
                'default' => $this->get_default('nosfir_footer_text_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_footer_text_color',
                array(
                    'label' => __('Footer Text Color', 'nosfir'),
                    'section' => 'nosfir_footer',
                    'priority' => 45,
                )
            ));

            $wp_customize->add_setting('nosfir_footer_heading_color', array(
                'default' => $this->get_default('nosfir_footer_heading_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_footer_heading_color',
                array(
                    'label' => __('Footer Heading Color', 'nosfir'),
                    'section' => 'nosfir_footer',
                    'priority' => 50,
                )
            ));

            $wp_customize->add_setting('nosfir_footer_link_color', array(
                'default' => $this->get_default('nosfir_footer_link_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_footer_link_color',
                array(
                    'label' => __('Footer Link Color', 'nosfir'),
                    'section' => 'nosfir_footer',
                    'priority' => 55,
                )
            ));
        }

        /**
         * Registra settings de navegação
         */
        private function register_navigation_settings($wp_customize) {
            // Navigation Background
            $wp_customize->add_setting('nosfir_nav_background', array(
                'default' => $this->get_default('nosfir_nav_background'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_nav_background',
                array(
                    'label' => __('Navigation Background', 'nosfir'),
                    'section' => 'nosfir_navigation',
                    'priority' => 10,
                )
            ));

            // Navigation Link Color
            $wp_customize->add_setting('nosfir_nav_link_color', array(
                'default' => $this->get_default('nosfir_nav_link_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_nav_link_color',
                array(
                    'label' => __('Navigation Link Color', 'nosfir'),
                    'section' => 'nosfir_navigation',
                    'priority' => 15,
                )
            ));

            // Navigation Link Hover Color
            $wp_customize->add_setting('nosfir_nav_link_hover_color', array(
                'default' => $this->get_default('nosfir_nav_link_hover_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_nav_link_hover_color',
                array(
                    'label' => __('Navigation Link Hover Color', 'nosfir'),
                    'section' => 'nosfir_navigation',
                    'priority' => 20,
                )
            ));

            // Dropdown Background
            $wp_customize->add_setting('nosfir_nav_dropdown_background', array(
                'default' => $this->get_default('nosfir_nav_dropdown_background'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_nav_dropdown_background',
                array(
                    'label' => __('Dropdown Background', 'nosfir'),
                    'section' => 'nosfir_navigation',
                    'priority' => 25,
                )
            ));

            // Dropdown Link Color
            $wp_customize->add_setting('nosfir_nav_dropdown_link_color', array(
                'default' => $this->get_default('nosfir_nav_dropdown_link_color'),
                'sanitize_callback' => 'sanitize_hex_color',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control(
                $wp_customize,
                'nosfir_nav_dropdown_link_color',
                array(
                    'label' => __('Dropdown Link Color', 'nosfir'),
                    'section' => 'nosfir_navigation',
                    'priority' => 30,
                )
            ));

            // Mobile Breakpoint
            $wp_customize->add_setting('nosfir_mobile_menu_breakpoint', array(
                'default' => $this->get_default('nosfir_mobile_menu_breakpoint'),
                'sanitize_callback' => 'absint',
                'transport' => 'refresh',
            ));
            $wp_customize->add_control('nosfir_mobile_menu_breakpoint', array(
                'label' => __('Mobile Menu Breakpoint (px)', 'nosfir'),
                'section' => 'nosfir_navigation',
                'type' => 'number',
                'priority' => 35,
            ));
        }

        /**
         * Registra settings de layout
         */
        private function register_layout_settings($wp_customize) {
            // Sidebar Layout
            $wp_customize->add_setting('nosfir_sidebar_layout', array(
                'default' => $this->get_default('nosfir_sidebar_layout'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control(new Nosfir_Customizer_Control_Radio_Image(
                $wp_customize,
                'nosfir_sidebar_layout',
                array(
                    'label' => __('Default Sidebar Layout', 'nosfir'),
                    'section' => 'nosfir_layout_sidebar',
                    'choices' => array(
                        'right' => get_template_directory_uri() . '/assets/images/customizer/2cr.png',
                        'left' => get_template_directory_uri() . '/assets/images/customizer/2cl.png',
                        'none' => get_template_directory_uri() . '/assets/images/customizer/1c.png',
                    ),
                    'priority' => 10,
                )
            ));
        }

        /**
         * Registra settings do WooCommerce
         */
        private function register_woocommerce_settings($wp_customize) {
            // Shop Layout
            $wp_customize->add_setting('nosfir_shop_layout', array(
                'default' => $this->get_default('nosfir_shop_layout'),
                'sanitize_callback' => 'sanitize_key',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control('nosfir_shop_layout', array(
                'label' => __('Shop Layout', 'nosfir'),
                'section' => 'nosfir_woocommerce',
                'type' => 'select',
                'choices' => array(
                    'grid' => __('Grid', 'nosfir'),
                    'list' => __('List', 'nosfir'),
                ),
                'priority' => 10,
            ));
            
            // Products per page
            $wp_customize->add_setting('nosfir_shop_products_per_page', array(
                'default' => $this->get_default('nosfir_shop_products_per_page'),
                'sanitize_callback' => 'absint',
                'transport' => 'refresh',
            ));
            
            $wp_customize->add_control('nosfir_shop_products_per_page', array(
                'label' => __('Products Per Page', 'nosfir'),
                'section' => 'nosfir_woocommerce',
                'type' => 'number',
                'priority' => 20,
            ));
        }

        /**
         * Registra section More/Upsell
         */
        private function register_more_section($wp_customize) {
            if (isset($this->custom_controls['more'])) {
                $wp_customize->add_setting('nosfir_more', array(
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ));
                
                
            }
        }

        /**
         * Edita controls padrão do WordPress
         */
        public function edit_default_controls($wp_customize) {
            // Move background color para nossa section
            if ($wp_customize->get_control('background_color')) {
                $wp_customize->get_control('background_color')->section = 'nosfir_colors_global';
                $wp_customize->get_control('background_color')->priority = 40;
            }
            
            // Melhora labels
            if ($wp_customize->get_section('title_tagline')) {
                $wp_customize->get_section('title_tagline')->title = __('Site Identity', 'nosfir');
                $wp_customize->get_section('title_tagline')->priority = 5;
            }
            
            // Remove controls não usados
            $wp_customize->remove_control('display_header_text');
            $wp_customize->remove_control('header_textcolor');
        }

        /**
         * Configura Selective Refresh
         */
        public function selective_refresh($wp_customize) {
            // Verifica se selective refresh está disponível
            if (!isset($wp_customize->selective_refresh)) {
                return;
            }
            
            // Site title
            $wp_customize->get_setting('blogname')->transport = 'postMessage';
            $wp_customize->selective_refresh->add_partial('blogname', array(
                'selector' => '.site-title a',
                'render_callback' => function() {
                    return get_bloginfo('name', 'display');
                },
            ));
            
            // Site description
            $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
            $wp_customize->selective_refresh->add_partial('blogdescription', array(
                'selector' => '.site-description',
                'render_callback' => function() {
                    return get_bloginfo('description', 'display');
                },
            ));
            
            // Logo
            $wp_customize->selective_refresh->add_partial('custom_logo', array(
                'selector' => '.site-branding',
                'render_callback' => array($this, 'render_site_branding'),
            ));
            
            // Footer copyright
            $wp_customize->selective_refresh->add_partial('nosfir_footer_copyright', array(
                'selector' => '.site-info',
                'render_callback' => function() {
                    return wp_kses_post(get_theme_mod('nosfir_footer_copyright', $this->get_default('nosfir_footer_copyright')));
                },
            ));
        }

        /**
         * Scripts do preview
         */
        public function customize_preview_js() {
            wp_enqueue_script(
                'nosfir-customizer-preview',
                get_template_directory_uri() . '/assets/js/customizer-preview.js',
                array('customize-preview', 'jquery'),
                wp_get_theme()->get('Version'),
                true
            );
            
            wp_localize_script('nosfir-customizer-preview', 'nosfir_customizer_preview', array(
                'defaults' => $this->defaults,
            ));
        }

        /**
         * Scripts dos controles
         */
        public function customize_controls_js() {
            wp_enqueue_script(
                'nosfir-customizer-controls',
                get_template_directory_uri() . '/assets/js/customizer-controls.js',
                array('customize-controls', 'jquery'),
                wp_get_theme()->get('Version'),
                true
            );
            
            wp_localize_script('nosfir-customizer-controls', 'nosfir_customizer', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nosfir-customizer'),
                'defaults' => $this->defaults,
                'strings' => array(
                    'reset_confirm' => __('Are you sure you want to reset all customizer settings?', 'nosfir'),
                    'import_confirm' => __('This will override your current settings. Continue?', 'nosfir'),
                    'export_name' => __('nosfir-customizer-export', 'nosfir'),
                ),
            ));
        }

        /**
         * CSS dos controles
         */
        public function customize_controls_css() {
            ?>
            <style type="text/css">
                /* Nosfir Customizer Controls CSS */
                .customize-control-nosfir-heading {
                    margin-bottom: 20px;
                }
                
                .customize-control-nosfir-heading .customize-control-title {
                    font-size: 18px;
                    font-weight: 600;
                    border-bottom: 2px solid #2563eb;
                    padding-bottom: 10px;
                    margin-bottom: 15px;
                }
                
                .customize-control-nosfir-divider {
                    margin: 20px -12px;
                    border-top: 1px solid #ddd;
                }
                
                .nosfir-customizer-reset {
                    margin-top: 30px;
                    text-align: center;
                }
                
                .nosfir-customizer-reset .button {
                    background: #ef4444;
                    border-color: #ef4444;
                    color: #fff;
                }
                
                .nosfir-customizer-reset .button:hover {
                    background: #dc2626;
                    border-color: #dc2626;
                }
            </style>
            <?php
        }

        /**
         * CSS do front-end
         */
        public function customizer_css() {
            $css = $this->generate_css();
            
            if (!empty($css)) {
                wp_add_inline_style('nosfir-style', $css);
            }
        }

        /**
         * CSS do block editor
         */
        public function block_editor_css() {
            if (is_admin()) {
                $css = $this->generate_editor_css();
                
                if (!empty($css)) {
                    wp_add_inline_style('nosfir-block-editor', $css);
                }
            }
        }

        /**
         * Gera CSS baseado nas configurações
         */
        public function generate_css() {
            $css = '';
            
            // Cores
            $primary_color = get_theme_mod('nosfir_primary_color', $this->get_default('nosfir_primary_color'));
            $secondary_color = get_theme_mod('nosfir_secondary_color', $this->get_default('nosfir_secondary_color'));
            $accent_color = get_theme_mod('nosfir_accent_color', $this->get_default('nosfir_accent_color'));
            
            // CSS Variables
            $css .= ':root {';
            $css .= '--nosfir-primary-color: ' . $primary_color . ';';
            $css .= '--nosfir-secondary-color: ' . $secondary_color . ';';
            $css .= '--nosfir-accent-color: ' . $accent_color . ';';
            
            // Typography
            $body_font = get_theme_mod('nosfir_body_font_family', $this->get_default('nosfir_body_font_family'));
            $body_size = get_theme_mod('nosfir_body_font_size', $this->get_default('nosfir_body_font_size'));
            
            $css .= '--nosfir-body-font: ' . $body_font . ';';
            $css .= '--nosfir-body-size: ' . $body_size . ';';
            
            // Container
            $container_width = get_theme_mod('nosfir_container_width', $this->get_default('nosfir_container_width'));
            $css .= '--nosfir-container-width: ' . $container_width . ';';

            // Header Variables
            $css .= '--nosfir-header-bg: ' . get_theme_mod('nosfir_header_background', $this->get_default('nosfir_header_background')) . ';';
            $css .= '--nosfir-header-text: ' . get_theme_mod('nosfir_header_text_color', $this->get_default('nosfir_header_text_color')) . ';';

            // Footer Variables
            $css .= '--nosfir-footer-bg: ' . get_theme_mod('nosfir_footer_background', $this->get_default('nosfir_footer_background')) . ';';
            $css .= '--nosfir-footer-text: ' . get_theme_mod('nosfir_footer_text_color', $this->get_default('nosfir_footer_text_color')) . ';';
            
            $css .= '}';
            
            // Navigation Colors
            $nav_bg = get_theme_mod('nosfir_nav_background', $this->get_default('nosfir_nav_background'));
            $nav_link = get_theme_mod('nosfir_nav_link_color', $this->get_default('nosfir_nav_link_color'));
            $nav_link_hover = get_theme_mod('nosfir_nav_link_hover_color', $this->get_default('nosfir_nav_link_hover_color'));
            $nav_dd_bg = get_theme_mod('nosfir_nav_dropdown_background', $this->get_default('nosfir_nav_dropdown_background'));
            $nav_dd_link = get_theme_mod('nosfir_nav_dropdown_link_color', $this->get_default('nosfir_nav_dropdown_link_color'));

            $css .= '.main-navigation{background-color:' . $nav_bg . ';}';
            $css .= '.main-navigation a{color:' . $nav_link . ';}';
            $css .= '.main-navigation a:hover{color:' . $nav_link_hover . ';}';
            $css .= '.main-navigation ul ul{background-color:' . $nav_dd_bg . ';}';
            $css .= '.main-navigation ul ul a{color:' . $nav_dd_link . ';}';

            // Header link color
            $header_link = get_theme_mod('nosfir_header_link_color', $this->get_default('nosfir_header_link_color'));
            $css .= '.site-header a{color:' . $header_link . ';}';

            // Footer detailed colors
            $footer_heading = get_theme_mod('nosfir_footer_heading_color', $this->get_default('nosfir_footer_heading_color'));
            $footer_link = get_theme_mod('nosfir_footer_link_color', $this->get_default('nosfir_footer_link_color'));
            $css .= '.site-footer h1, .site-footer h2, .site-footer h3, .site-footer h4, .site-footer h5, .site-footer h6{color:' . $footer_heading . ';}';
            $css .= '.site-footer a{color:' . $footer_link . ';}';

            // Custom CSS
            $custom_css = get_theme_mod('nosfir_custom_css', '');
            if (!empty($custom_css)) {
                $css .= $custom_css;
            }
            
            return apply_filters('nosfir_customizer_css', $css);
        }

        /**
         * Gera CSS para o editor
         */
        public function generate_editor_css() {
            $css = '';
            
            // Aplica estilos similares ao front-end
            $css .= '.editor-styles-wrapper {';
            $css .= 'font-family: ' . get_theme_mod('nosfir_body_font_family', $this->get_default('nosfir_body_font_family')) . ';';
            $css .= 'font-size: ' . get_theme_mod('nosfir_body_font_size', $this->get_default('nosfir_body_font_size')) . ';';
            $css .= 'color: ' . get_theme_mod('nosfir_text_color', $this->get_default('nosfir_text_color')) . ';';
            $css .= '}';
            
            return apply_filters('nosfir_editor_css', $css);
        }

        /**
         * Classes do body
         */
        public function body_classes($classes) {
            // Layout classes
            $site_layout = get_theme_mod('nosfir_site_layout', $this->get_default('nosfir_site_layout'));
            $classes[] = 'site-layout-' . $site_layout;
            
            $sidebar_layout = get_theme_mod('nosfir_sidebar_layout', $this->get_default('nosfir_sidebar_layout'));
            $classes[] = 'sidebar-' . $sidebar_layout;
            
            // Header classes
            $header_layout = get_theme_mod('nosfir_header_layout', $this->get_default('nosfir_header_layout'));
            $classes[] = 'header-' . $header_layout;
            
            if (get_theme_mod('nosfir_header_sticky', $this->get_default('nosfir_header_sticky'))) {
                $classes[] = 'has-sticky-header';
            }
            if (get_theme_mod('nosfir_header_transparent', $this->get_default('nosfir_header_transparent'))) {
                $classes[] = 'header-transparent';
            }
            $classes[] = 'header-width-' . get_theme_mod('nosfir_header_width', $this->get_default('nosfir_header_width'));
            
            return $classes;
        }

        /**
         * Valores padrão dos theme mods
         */
        public function default_theme_mod_values() {
            foreach ($this->defaults as $mod => $val) {
                add_filter('theme_mod_' . $mod, array($this, 'get_theme_mod_value'), 10);
            }
        }

        /**
         * Obtém valor do theme mod
         */
        public function get_theme_mod_value($value) {
            $key = str_replace('theme_mod_', '', current_filter());
            
            $set_theme_mods = get_theme_mods();
            
            if (isset($set_theme_mods[$key])) {
                return $value;
            }
            
            return isset($this->defaults[$key]) ? $this->defaults[$key] : $value;
        }

        /**
         * Registra seção de Export/Import
         */
        public function register_export_import($wp_customize) {
            $wp_customize->add_section('nosfir_export_import', array(
                'title' => __('Export/Import', 'nosfir'),
                'priority' => 999,
                'panel' => 'nosfir_panel',
            ));

            $wp_customize->add_setting('nosfir_export_import_data', array(
                'default' => '',
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field',
            ));

            $wp_customize->add_control('nosfir_export_import_data', array(
                'label' => __('Export/Import Settings', 'nosfir'),
                'description' => __('Use the buttons below to export or import your customizer settings.', 'nosfir'),
                'section' => 'nosfir_export_import',
                'type' => 'text', // Placeholder since we don't have the custom control class handy
            ));
        }

        /**
         * Reset customizer via AJAX
         */
        public function ajax_reset_customizer() {
            check_ajax_referer('nosfir-customizer', 'nonce');
            
            if (!current_user_can('edit_theme_options')) {
                wp_send_json_error('Insufficient permissions');
            }
            
            // Remove todos os theme mods
            remove_theme_mods();
            
            wp_send_json_success('Customizer settings reset successfully');
        }

        /**
         * Export settings via AJAX
         */
        public function ajax_export_customizer() {
            check_ajax_referer('nosfir-customizer', 'nonce');
            
            if (!current_user_can('edit_theme_options')) {
                wp_send_json_error('Insufficient permissions');
            }
            
            $theme_mods = get_theme_mods();
            
            wp_send_json_success($theme_mods);
        }

        /**
         * Import settings via AJAX
         */
        public function ajax_import_customizer() {
            check_ajax_referer('nosfir-customizer', 'nonce');
            
            if (!current_user_can('edit_theme_options')) {
                wp_send_json_error('Insufficient permissions');
            }
            
            $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : array();
            
            if (empty($settings)) {
                wp_send_json_error('Invalid settings data');
            }
            
            foreach ($settings as $key => $value) {
                set_theme_mod($key, $value);
            }
            
            wp_send_json_success('Settings imported successfully');
        }

        /**
         * Renderiza site branding
         */
        public function render_site_branding() {
            get_template_part('template-parts/header/site', 'branding');
        }

        /**
         * Obtém opções de fontes
         */
        private function get_font_choices() {
            return array(
                'System Font Stack' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'Inter' => 'Inter, sans-serif',
                'Roboto' => 'Roboto, sans-serif',
                'Open Sans' => '"Open Sans", sans-serif',
                'Lato' => 'Lato, sans-serif',
                'Montserrat' => 'Montserrat, sans-serif',
                'Poppins' => 'Poppins, sans-serif',
                'Playfair Display' => '"Playfair Display", serif',
                'Merriweather' => 'Merriweather, serif',
            );
        }
    }

endif;

// Sanitization callbacks
if (!function_exists('nosfir_sanitize_checkbox')) :
    function nosfir_sanitize_checkbox($input) {
        return ($input === true || $input === '1') ? true : false;
    }
endif;

if (!function_exists('nosfir_sanitize_select')) :
    function nosfir_sanitize_select($input, $setting) {
        $input = sanitize_key($input);
        $choices = $setting->manager->get_control($setting->id)->choices;
        return (array_key_exists($input, $choices) ? $input : $setting->default);
    }
endif;

// Inicializa o Customizer
return Nosfir_Customizer::get_instance();
