<?php
/**
 * Nosfir Class
 *
 * Classe principal do tema que gerencia todas as funcionalidades core.
 *
 * @since    1.0.0
 * @package  Nosfir
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Nosfir' ) ) :

    /**
     * The main Nosfir class
     *
     * @since 1.0.0
     */
    class Nosfir {

        /**
         * Instance
         *
         * @var Nosfir The single instance of the class
         * @since 1.0.0
         */
        protected static $instance = null;

        /**
         * Theme Version
         *
         * @var string
         */
        public $version;

        /**
         * Theme Settings
         *
         * @var array
         */
        private $settings = array();

        /**
         * Debug Mode
         *
         * @var bool
         */
        private $debug_mode = false;

        /**
         * Main Nosfir Instance
         *
         * Ensures only one instance of Nosfir is loaded or can be loaded
         *
         * @since 1.0.0
         * @return Nosfir Main instance
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.0
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'nosfir' ), '1.0.0' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.0
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'nosfir' ), '1.0.0' );
        }

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        public function __construct() {
            // Set version
            $this->version = $this->get_theme_version();
            
            // Set debug mode
            $this->debug_mode = defined( 'WP_DEBUG' ) && WP_DEBUG;

            // Initialize hooks
            $this->init_hooks();
        }

        /**
         * Get theme version safely
         *
         * @since 1.0.0
         * @return string
         */
        private function get_theme_version() {
            if ( defined( 'NOSFIR_VERSION' ) ) {
                return NOSFIR_VERSION;
            }
            
            $theme = wp_get_theme( 'nosfir' );
            return $theme->get( 'Version' ) ?: '1.0.0';
        }

        /**
         * Initialize all hooks
         *
         * @since 1.0.0
         */
        private function init_hooks() {
            // Setup theme
            add_action( 'after_setup_theme', array( $this, 'setup' ), 10 );
            add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
            
            // Register widgets
            add_action( 'widgets_init', array( $this, 'widgets_init' ) );
            
            // Enqueue scripts and styles
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
            add_action( 'wp_enqueue_scripts', array( $this, 'child_scripts' ), 30 );
            add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            
            // Body classes
            add_filter( 'body_class', array( $this, 'body_classes' ) );
            
            // Page menu args
            add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );
            
            // Navigation
            add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );
            add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 4 );
            add_filter( 'nav_menu_link_attributes', array( $this, 'nav_menu_link_attributes' ), 10, 4 );
            
            // Embeds
            add_action( 'enqueue_embed_scripts', array( $this, 'print_embed_styles' ) );
            
            // Custom hooks
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'wp_head', array( $this, 'wp_head' ), 5 );
            add_action( 'wp_footer', array( $this, 'wp_footer' ), 20 );
            
            // AJAX handlers
            add_action( 'wp_ajax_nosfir_load_more', array( $this, 'ajax_load_more_posts' ) );
            add_action( 'wp_ajax_nopriv_nosfir_load_more', array( $this, 'ajax_load_more_posts' ) );
            
            // Excerpt
            add_filter( 'excerpt_more', array( $this, 'custom_excerpt_more' ) );
            add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ), 999 );
            
            // Password protected post form
            add_filter( 'the_password_form', array( $this, 'custom_password_form' ) );
        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * @since 1.0.0
         */
        public function setup() {
            /*
            |------------------------------------------------------------------
            | Load Localisation files
            |------------------------------------------------------------------
            */
            
            // Loads wp-content/languages/themes/nosfir-it_IT.mo
            load_theme_textdomain( 'nosfir', trailingslashit( WP_LANG_DIR ) . 'themes' );

            // Loads wp-content/themes/child-theme-name/languages/it_IT.mo
            load_theme_textdomain( 'nosfir', get_stylesheet_directory() . '/languages' );

            // Loads wp-content/themes/nosfir/languages/it_IT.mo
            load_theme_textdomain( 'nosfir', get_template_directory() . '/languages' );

            /*
            |------------------------------------------------------------------
            | Basic Theme Support
            |------------------------------------------------------------------
            */
            
            // Add default posts and comments RSS feed links to head
            add_theme_support( 'automatic-feed-links' );

            // Enable support for title tag
            add_theme_support( 'title-tag' );

            // Enable support for Post Thumbnails on posts and pages
            add_theme_support( 'post-thumbnails' );

            // Set default post thumbnail size
            set_post_thumbnail_size( 825, 510, true );
            
            // Add custom image sizes
            add_image_size( 'nosfir-featured', 1920, 1080, true );
            add_image_size( 'nosfir-medium', 768, 432, true );
            add_image_size( 'nosfir-small', 375, 211, true );
            add_image_size( 'nosfir-square', 600, 600, true );
            add_image_size( 'nosfir-thumbnail', 150, 150, true );

            /*
            |------------------------------------------------------------------
            | Custom Logo Support
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'custom-logo',
                apply_filters(
                    'nosfir_custom_logo_args',
                    array(
                        'height'               => 100,
                        'width'                => 400,
                        'flex-width'           => true,
                        'flex-height'          => true,
                        'unlink-homepage-logo' => false,
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | Register Navigation Menus
            |------------------------------------------------------------------
            */
            
            register_nav_menus(
                apply_filters(
                    'nosfir_register_nav_menus',
                    array(
                        'primary'   => esc_html__( 'Primary Menu', 'nosfir' ),
                        'secondary' => esc_html__( 'Secondary Menu', 'nosfir' ),
                        'mobile'    => esc_html__( 'Mobile Menu', 'nosfir' ),
                        'footer'    => esc_html__( 'Footer Menu', 'nosfir' ),
                        'social'    => esc_html__( 'Social Menu', 'nosfir' ),
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | HTML5 Support
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'html5',
                apply_filters(
                    'nosfir_html5_args',
                    array(
                        'search-form',
                        'comment-form',
                        'comment-list',
                        'gallery',
                        'caption',
                        'widgets',
                        'style',
                        'script',
                        'navigation-widgets',
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | Post Formats Support
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'post-formats',
                array(
                    'aside',
                    'gallery',
                    'link',
                    'image',
                    'quote',
                    'video',
                    'audio',
                    'status',
                    'chat',
                )
            );

            /*
            |------------------------------------------------------------------
            | Custom Background Support
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'custom-background',
                apply_filters(
                    'nosfir_custom_background_args',
                    array(
                        'default-color' => apply_filters( 'nosfir_default_background_color', 'ffffff' ),
                        'default-image' => '',
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | Custom Header Support
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'custom-header',
                apply_filters(
                    'nosfir_custom_header_args',
                    array(
                        'default-image'      => '',
                        'default-text-color' => '000000',
                        'width'              => 1920,
                        'height'             => 500,
                        'flex-width'         => true,
                        'flex-height'        => true,
                        'header-text'        => true,
                        'video'              => true,
                        'wp-head-callback'   => array( $this, 'header_style' ),
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | Customizer Support
            |------------------------------------------------------------------
            */
            
            add_theme_support( 'customize-selective-refresh-widgets' );

            /*
            |------------------------------------------------------------------
            | Block Editor (Gutenberg) Support
            |------------------------------------------------------------------
            */
            
            // Add support for Block Styles
            add_theme_support( 'wp-block-styles' );

            // Add support for full and wide align images
            add_theme_support( 'align-wide' );

            // Add support for editor styles
            add_theme_support( 'editor-styles' );

            // Add support for responsive embedded content
            add_theme_support( 'responsive-embeds' );

            // Add support for custom line height
            add_theme_support( 'custom-line-height' );

            // Add support for custom units
            add_theme_support( 'custom-units', array( 'px', 'em', 'rem', '%', 'vh', 'vw' ) );

            // Add support for appearance tools
            add_theme_support( 'appearance-tools' );

            // Add support for link color control
            add_theme_support( 'link-color' );

            // Add support for custom spacing
            add_theme_support( 'custom-spacing' );

            // Add support for border
            add_theme_support( 'border' );

            // Add support for widgets block editor
            add_theme_support( 'widgets-block-editor' );

            /*
            |------------------------------------------------------------------
            | Editor Font Sizes
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'editor-font-sizes',
                array(
                    array(
                        'name' => esc_html__( 'Small', 'nosfir' ),
                        'size' => 12,
                        'slug' => 'small',
                    ),
                    array(
                        'name' => esc_html__( 'Normal', 'nosfir' ),
                        'size' => 16,
                        'slug' => 'normal',
                    ),
                    array(
                        'name' => esc_html__( 'Medium', 'nosfir' ),
                        'size' => 20,
                        'slug' => 'medium',
                    ),
                    array(
                        'name' => esc_html__( 'Large', 'nosfir' ),
                        'size' => 24,
                        'slug' => 'large',
                    ),
                    array(
                        'name' => esc_html__( 'Extra Large', 'nosfir' ),
                        'size' => 32,
                        'slug' => 'extra-large',
                    ),
                    array(
                        'name' => esc_html__( 'Huge', 'nosfir' ),
                        'size' => 48,
                        'slug' => 'huge',
                    ),
                )
            );

            /*
            |------------------------------------------------------------------
            | Editor Color Palette
            |------------------------------------------------------------------
            */
            
            add_theme_support(
                'editor-color-palette',
                apply_filters(
                    'nosfir_editor_color_palette',
                    array(
                        array(
                            'name'  => esc_html__( 'Primary', 'nosfir' ),
                            'slug'  => 'primary',
                            'color' => get_theme_mod( 'nosfir_primary_color', '#2c3e50' ),
                        ),
                        array(
                            'name'  => esc_html__( 'Secondary', 'nosfir' ),
                            'slug'  => 'secondary',
                            'color' => get_theme_mod( 'nosfir_secondary_color', '#e74c3c' ),
                        ),
                        array(
                            'name'  => esc_html__( 'Dark', 'nosfir' ),
                            'slug'  => 'dark',
                            'color' => '#1a1a1a',
                        ),
                        array(
                            'name'  => esc_html__( 'Light', 'nosfir' ),
                            'slug'  => 'light',
                            'color' => '#f8f9fa',
                        ),
                        array(
                            'name'  => esc_html__( 'White', 'nosfir' ),
                            'slug'  => 'white',
                            'color' => '#ffffff',
                        ),
                        array(
                            'name'  => esc_html__( 'Success', 'nosfir' ),
                            'slug'  => 'success',
                            'color' => '#27ae60',
                        ),
                        array(
                            'name'  => esc_html__( 'Warning', 'nosfir' ),
                            'slug'  => 'warning',
                            'color' => '#f39c12',
                        ),
                        array(
                            'name'  => esc_html__( 'Info', 'nosfir' ),
                            'slug'  => 'info',
                            'color' => '#3498db',
                        ),
                    )
                )
            );

            /*
            |------------------------------------------------------------------
            | Editor Styles
            |------------------------------------------------------------------
            */
            
            $editor_styles = array( 'assets/css/editor-style.css' );
            
            $google_fonts = $this->get_google_fonts_url();
            if ( $google_fonts ) {
                $editor_styles[] = $google_fonts;
            }
            
            add_editor_style( $editor_styles );

            /*
            |------------------------------------------------------------------
            | WooCommerce Support
            |------------------------------------------------------------------
            */
            
            if ( $this->is_woocommerce_activated() ) {
                add_theme_support( 'woocommerce' );
                add_theme_support( 'wc-product-gallery-zoom' );
                add_theme_support( 'wc-product-gallery-lightbox' );
                add_theme_support( 'wc-product-gallery-slider' );
            }

            /*
            |------------------------------------------------------------------
            | AMP Support
            |------------------------------------------------------------------
            */
            
            if ( function_exists( 'amp_is_enabled' ) ) {
                add_theme_support(
                    'amp',
                    array(
                        'nav_menu_toggle' => array(
                            'nav_container_id'           => 'site-navigation',
                            'nav_container_toggle_class' => 'toggled',
                            'menu_button_id'             => 'menu-toggle',
                            'menu_button_toggle_class'   => 'toggled',
                        ),
                    )
                );
            }

            /**
             * Action hook after theme setup
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_after_setup_theme' );
        }

        /**
         * Set the content width in pixels
         *
         * @since 1.0.0
         * @global int $content_width
         */
        public function content_width() {
            $GLOBALS['content_width'] = apply_filters( 'nosfir_content_width', 1140 );
        }

        /**
         * Register widget areas
         *
         * @since 1.0.0
         */
        public function widgets_init() {
            // Default widget tags
            $widget_tags = array(
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h3 class="widget-title"><span>',
                'after_title'   => '</span></h3>',
            );

            // Sidebar
            register_sidebar(
                array_merge(
                    $widget_tags,
                    array(
                        'name'        => esc_html__( 'Sidebar', 'nosfir' ),
                        'id'          => 'sidebar-1',
                        'description' => esc_html__( 'Main sidebar that appears on the left or right.', 'nosfir' ),
                    )
                )
            );

            // Header Widget Area
            register_sidebar(
                array_merge(
                    $widget_tags,
                    array(
                        'name'        => esc_html__( 'Header Widget Area', 'nosfir' ),
                        'id'          => 'header-widget',
                        'description' => esc_html__( 'Widgets in this area will be shown in the header.', 'nosfir' ),
                    )
                )
            );

            // Shop Sidebar (WooCommerce)
            if ( $this->is_woocommerce_activated() ) {
                register_sidebar(
                    array_merge(
                        $widget_tags,
                        array(
                            'name'        => esc_html__( 'Shop Sidebar', 'nosfir' ),
                            'id'          => 'sidebar-shop',
                            'description' => esc_html__( 'Widgets for WooCommerce shop pages.', 'nosfir' ),
                        )
                    )
                );
            }

            // Footer Widget Areas
            $footer_columns = intval( apply_filters( 'nosfir_footer_widget_columns', 4 ) );
            
            for ( $i = 1; $i <= $footer_columns; $i++ ) {
                register_sidebar(
                    array_merge(
                        $widget_tags,
                        array(
                            'name'        => sprintf( esc_html__( 'Footer Column %d', 'nosfir' ), $i ),
                            'id'          => 'footer-' . $i,
                            'description' => sprintf( esc_html__( 'Widgets added here will appear in column %d of the footer.', 'nosfir' ), $i ),
                        )
                    )
                );
            }

            /**
             * Action hook after widgets init
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_widgets_init' );
        }

        /**
         * Enqueue styles
         *
         * @since 1.0.0
         */
        public function enqueue_styles() {
            $version = $this->version;
            $suffix  = $this->get_asset_suffix();

            // Google Fonts
            $google_fonts_url = $this->get_google_fonts_url();
            if ( $google_fonts_url ) {
                wp_enqueue_style(
                    'nosfir-fonts',
                    $google_fonts_url,
                    array(),
                    null
                );
            }

            // Main stylesheet
            wp_enqueue_style(
                'nosfir-style',
                get_stylesheet_uri(),
                array(),
                $version
            );
            wp_style_add_data( 'nosfir-style', 'rtl', 'replace' );

            // Theme main CSS (if separate from style.css)
            $main_css = $this->get_asset_path( '/assets/css/main' . $suffix . '.css' );
            if ( $main_css ) {
                wp_enqueue_style(
                    'nosfir-main-style',
                    $main_css['url'],
                    array( 'nosfir-style' ),
                    $version
                );
            }

            // Add inline custom styles
            $this->add_inline_styles();

            /**
             * Action hook after styles enqueue
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_enqueue_styles' );
        }

        /**
         * Enqueue scripts
         *
         * @since 1.0.0
         */
        public function enqueue_scripts() {
            $version = $this->version;
            $suffix  = $this->get_asset_suffix();

            // Mute jQuery migrate warnings in development
            if ( ! is_admin() && $this->debug_mode ) {
                wp_add_inline_script( 'jquery-migrate', 'jQuery.migrateMute = true;', 'before' );
            }

            // Navigation script
            $navigation_js = $this->get_asset_path( '/assets/js/navigation' . $suffix . '.js' );
            if ( $navigation_js ) {
                wp_enqueue_script(
                    'nosfir-navigation',
                    $navigation_js['url'],
                    array(),
                    $version,
                    true
                );
            }

            // Skip link focus fix
            $skip_link_js = $this->get_asset_path( '/assets/js/skip-link-focus-fix' . $suffix . '.js' );
            if ( $skip_link_js ) {
                wp_enqueue_script(
                    'nosfir-skip-link-focus-fix',
                    $skip_link_js['url'],
                    array(),
                    $version,
                    true
                );
            }

            // Main script
            $main_js = $this->get_asset_path( '/assets/js/main' . $suffix . '.js' );
            if ( $main_js ) {
                wp_enqueue_script(
                    'nosfir-main',
                    $main_js['url'],
                    array( 'jquery' ),
                    $version,
                    true
                );

                // Localize main script
                wp_localize_script(
                    'nosfir-main',
                    'nosfirData',
                    $this->get_localized_data()
                );
            }

            // Comment reply script
            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
            }

            // Homepage specific scripts
            if ( is_page_template( 'template-homepage.php' ) ) {
                $homepage_js = $this->get_asset_path( '/assets/js/homepage' . $suffix . '.js' );
                if ( $homepage_js ) {
                    wp_enqueue_script(
                        'nosfir-homepage',
                        $homepage_js['url'],
                        array( 'jquery', 'nosfir-main' ),
                        $version,
                        true
                    );
                }
            }

            /**
             * Action hook after scripts enqueue
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_enqueue_scripts' );
        }

        /**
         * Get localized script data
         *
         * @since 1.0.0
         * @return array
         */
        private function get_localized_data() {
            return apply_filters(
                'nosfir_localized_data',
                array(
                    'ajax_url'   => admin_url( 'admin-ajax.php' ),
                    'nonce'      => wp_create_nonce( 'nosfir_nonce' ),
                    'home_url'   => esc_url( home_url( '/' ) ),
                    'theme_url'  => esc_url( get_template_directory_uri() ),
                    'is_mobile'  => wp_is_mobile(),
                    'is_rtl'     => is_rtl(),
                    'is_home'    => is_home() || is_front_page(),
                    'is_single'  => is_singular(),
                    'breakpoint' => array(
                        'mobile'  => 576,
                        'tablet'  => 768,
                        'desktop' => 992,
                        'large'   => 1200,
                    ),
                    'i18n'       => array(
                        'loading'      => esc_html__( 'Loading...', 'nosfir' ),
                        'load_more'    => esc_html__( 'Load More', 'nosfir' ),
                        'no_more'      => esc_html__( 'No more posts', 'nosfir' ),
                        'error'        => esc_html__( 'Something went wrong. Please try again.', 'nosfir' ),
                        'menu_open'    => esc_html__( 'Open menu', 'nosfir' ),
                        'menu_close'   => esc_html__( 'Close menu', 'nosfir' ),
                        'search_open'  => esc_html__( 'Open search', 'nosfir' ),
                        'search_close' => esc_html__( 'Close search', 'nosfir' ),
                        'expand'       => esc_html__( 'Expand child menu', 'nosfir' ),
                        'collapse'     => esc_html__( 'Collapse child menu', 'nosfir' ),
                    ),
                )
            );
        }

        /**
         * Get asset path and URL
         *
         * @since 1.0.0
         * @param string $path Relative path to asset.
         * @return array|false Array with 'path' and 'url' keys, or false if not found.
         */
        private function get_asset_path( $path ) {
            $template_path = get_template_directory() . $path;
            $template_url  = get_template_directory_uri() . $path;

            // Check for minified version first
            if ( file_exists( $template_path ) ) {
                return array(
                    'path' => $template_path,
                    'url'  => $template_url,
                );
            }

            // Try non-minified version
            $path_no_min = str_replace( '.min', '', $path );
            $template_path_no_min = get_template_directory() . $path_no_min;
            $template_url_no_min  = get_template_directory_uri() . $path_no_min;

            if ( file_exists( $template_path_no_min ) ) {
                return array(
                    'path' => $template_path_no_min,
                    'url'  => $template_url_no_min,
                );
            }

            return false;
        }

        /**
         * Get asset suffix (.min or empty)
         *
         * @since 1.0.0
         * @return string
         */
        private function get_asset_suffix() {
            return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        }

        /**
         * Get Google Fonts URL
         *
         * @since 1.0.0
         * @return string|false
         */
        public function get_google_fonts_url() {
            $google_fonts = apply_filters(
                'nosfir_google_font_families',
                array(
                    'inter'  => 'Inter:wght@300;400;500;600;700;800;900',
                    'roboto' => 'Roboto:wght@300;400;500;700;900',
                )
            );

            if ( empty( $google_fonts ) ) {
                return false;
            }

            $query_args = array(
                'family'  => implode( '&family=', array_values( $google_fonts ) ),
                'subset'  => rawurlencode( 'latin,latin-ext' ),
                'display' => 'swap',
            );

            return esc_url_raw( add_query_arg( $query_args, 'https://fonts.googleapis.com/css2' ) );
        }

        /**
         * Add inline styles from customizer
         *
         * @since 1.0.0
         */
        private function add_inline_styles() {
            $primary_color   = get_theme_mod( 'nosfir_primary_color', '#2c3e50' );
            $secondary_color = get_theme_mod( 'nosfir_secondary_color', '#e74c3c' );
            $text_color      = get_theme_mod( 'nosfir_text_color', '#333333' );
            $link_color      = get_theme_mod( 'nosfir_link_color', '#2c3e50' );
            $heading_color   = get_theme_mod( 'nosfir_heading_color', '#1a1a1a' );

            $css = "
                :root {
                    --nosfir-primary: {$primary_color};
                    --nosfir-secondary: {$secondary_color};
                    --nosfir-text: {$text_color};
                    --nosfir-link: {$link_color};
                    --nosfir-heading: {$heading_color};
                    --nosfir-primary-rgb: " . $this->hex_to_rgb( $primary_color ) . ";
                    --nosfir-secondary-rgb: " . $this->hex_to_rgb( $secondary_color ) . ";
                }
            ";

            // Custom CSS from customizer
            $custom_css = get_theme_mod( 'nosfir_custom_css', '' );
            if ( ! empty( $custom_css ) ) {
                $css .= "\n/* Custom CSS */\n" . wp_strip_all_tags( $custom_css );
            }

            wp_add_inline_style( 'nosfir-style', $this->minify_css( $css ) );
        }

        /**
         * Convert hex color to RGB
         *
         * @since 1.0.0
         * @param string $hex Hex color code.
         * @return string RGB values comma separated.
         */
        private function hex_to_rgb( $hex ) {
            $hex = ltrim( $hex, '#' );
            
            if ( strlen( $hex ) === 3 ) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
            
            return "{$r}, {$g}, {$b}";
        }

        /**
         * Minify CSS
         *
         * @since 1.0.0
         * @param string $css CSS to minify.
         * @return string
         */
        private function minify_css( $css ) {
            // Remove comments
            $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
            // Remove whitespace
            $css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
            // Remove extra spaces
            $css = preg_replace( '/\s+/', ' ', $css );
            $css = preg_replace( '/\s*([{}|:;,])\s*/', '$1', $css );
            
            return trim( $css );
        }

        /**
         * Enqueue block assets
         *
         * @since 1.0.0
         */
        public function block_assets() {
            $suffix = $this->get_asset_suffix();
            $block_css = $this->get_asset_path( '/assets/css/blocks' . $suffix . '.css' );
            
            if ( $block_css ) {
                wp_enqueue_style(
                    'nosfir-block-styles',
                    $block_css['url'],
                    array(),
                    $this->version
                );
            }
        }

        /**
         * Enqueue child theme stylesheet
         *
         * @since 1.0.0
         */
        public function child_scripts() {
            if ( is_child_theme() ) {
                $child_theme = wp_get_theme();
                wp_enqueue_style(
                    'nosfir-child-style',
                    get_stylesheet_uri(),
                    array( 'nosfir-style' ),
                    $child_theme->get( 'Version' )
                );
            }
        }

        /**
         * Enqueue admin scripts and styles
         *
         * @since 1.0.0
         */
        public function admin_scripts() {
            $suffix = $this->get_asset_suffix();
            
            // Admin CSS
            $admin_css = $this->get_asset_path( '/assets/css/admin' . $suffix . '.css' );
            if ( $admin_css ) {
                wp_enqueue_style(
                    'nosfir-admin',
                    $admin_css['url'],
                    array(),
                    $this->version
                );
            }

            // Admin JS
            $admin_js = $this->get_asset_path( '/assets/js/admin' . $suffix . '.js' );
            if ( $admin_js ) {
                wp_enqueue_script(
                    'nosfir-admin',
                    $admin_js['url'],
                    array( 'jquery' ),
                    $this->version,
                    true
                );
            }
        }

        /**
         * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link
         *
         * @since 1.0.0
         * @param array $args Page menu arguments.
         * @return array
         */
        public function page_menu_args( $args ) {
            $args['show_home'] = true;
            return $args;
        }

        /**
         * Adds custom classes to the array of body classes
         *
         * @since 1.0.0
         * @param array $classes Body classes.
         * @return array
         */
        public function body_classes( $classes ) {
            // Adds a class of group-blog to blogs with more than 1 published author
            if ( is_multi_author() ) {
                $classes[] = 'group-blog';
            }

            // Adds a class of hfeed to non-singular pages
            if ( ! is_singular() ) {
                $classes[] = 'hfeed';
            }

            // Add class when sidebar is active
            if ( is_active_sidebar( 'sidebar-1' ) && ! is_page_template( 'template-fullwidth.php' ) ) {
                $classes[] = 'has-sidebar';
            } else {
                $classes[] = 'no-sidebar';
            }

            // Add class for header style
            $header_style = get_theme_mod( 'nosfir_header_style', 'default' );
            $classes[] = 'header-style-' . sanitize_html_class( $header_style );

            // Add class for footer style
            $footer_style = get_theme_mod( 'nosfir_footer_style', 'default' );
            $classes[] = 'footer-style-' . sanitize_html_class( $footer_style );

            // Add class for layout
            $layout = get_theme_mod( 'nosfir_layout', 'wide' );
            $classes[] = 'layout-' . sanitize_html_class( $layout );

            // Add class for sidebar position
            $sidebar_position = get_theme_mod( 'nosfir_sidebar_position', 'right' );
            $classes[] = 'sidebar-' . sanitize_html_class( $sidebar_position );

            // Add class if is mobile
            if ( wp_is_mobile() ) {
                $classes[] = 'is-mobile';
            }

            // Add dark mode class if enabled
            if ( get_theme_mod( 'nosfir_dark_mode', false ) ) {
                $classes[] = 'dark-mode';
            }

            // Add sticky header class
            if ( get_theme_mod( 'nosfir_sticky_header', false ) ) {
                $classes[] = 'has-sticky-header';
            }

            // Add class for homepage template
            if ( is_page_template( 'template-homepage.php' ) ) {
                $classes[] = 'page-template-homepage';
                
                if ( has_post_thumbnail() ) {
                    $classes[] = 'has-featured-image';
                }
            }

            // Add no-js class (will be removed by JS)
            $classes[] = 'no-js';

            // Custom class from customizer
            $custom_class = get_theme_mod( 'nosfir_body_class', '' );
            if ( ! empty( $custom_class ) ) {
                $custom_classes = array_map( 'sanitize_html_class', explode( ' ', $custom_class ) );
                $classes = array_merge( $classes, $custom_classes );
            }

            return array_unique( $classes );
        }

        /**
         * Custom navigation markup template
         *
         * @since 1.0.0
         * @return string
         */
        public function navigation_markup_template() {
            $template  = '<nav class="navigation %1$s" role="navigation" aria-label="' . esc_attr__( 'Posts navigation', 'nosfir' ) . '">';
            $template .= '<h2 class="screen-reader-text">%2$s</h2>';
            $template .= '<div class="nav-links">%3$s</div>';
            $template .= '</nav>';

            return apply_filters( 'nosfir_navigation_markup_template', $template );
        }

        /**
         * Add custom classes to navigation menu items
         *
         * @since 1.0.0
         * @param array    $classes CSS classes.
         * @param WP_Post  $item    Menu item.
         * @param stdClass $args    Menu arguments.
         * @param int      $depth   Menu depth.
         * @return array
         */
        public function nav_menu_css_class( $classes, $item, $args, $depth ) {
            // Add active class
            if ( in_array( 'current-menu-item', $classes, true ) ) {
                $classes[] = 'active';
            }

            // Add has-children class
            if ( in_array( 'menu-item-has-children', $classes, true ) ) {
                $classes[] = 'has-dropdown';
            }

            // Add depth class
            $classes[] = 'menu-depth-' . $depth;

            return $classes;
        }

        /**
         * Add custom attributes to navigation menu links
         *
         * @since 1.0.0
         * @param array    $atts   Link attributes.
         * @param WP_Post  $item   Menu item.
         * @param stdClass $args   Menu arguments.
         * @param int      $depth  Menu depth.
         * @return array
         */
        public function nav_menu_link_attributes( $atts, $item, $args, $depth ) {
            // Add aria-current for current page
            if ( in_array( 'current-menu-item', $item->classes, true ) ) {
                $atts['aria-current'] = 'page';
            }

            return $atts;
        }

        /**
         * Styles the header image and text displayed on the blog
         *
         * @since 1.0.0
         */
        public function header_style() {
            $header_text_color = get_header_textcolor();

            if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
                return;
            }

            ?>
            <style type="text/css">
            <?php if ( ! display_header_text() ) : ?>
                .site-title,
                .site-description {
                    position: absolute;
                    clip: rect(1px, 1px, 1px, 1px);
                }
            <?php else : ?>
                .site-title a,
                .site-description {
                    color: #<?php echo esc_attr( $header_text_color ); ?>;
                }
            <?php endif; ?>
            </style>
            <?php
        }

        /**
         * Init hook
         *
         * @since 1.0.0
         */
        public function init() {
            /**
             * Action hook for registering custom post types
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_register_post_types' );

            /**
             * Action hook for registering custom taxonomies
             *
             * @since 1.0.0
             */
            do_action( 'nosfir_register_taxonomies' );
        }

        /**
         * WP Head hook
         *
         * @since 1.0.0
         */
        public function wp_head() {
            $primary_color = esc_attr( get_theme_mod( 'nosfir_primary_color', '#2c3e50' ) );
            ?>
            <meta name="theme-color" content="<?php echo $primary_color; ?>">
            <meta name="msapplication-TileColor" content="<?php echo $primary_color; ?>">
            <?php

            // Add preconnect for Google Fonts
            if ( apply_filters( 'nosfir_google_font_families', array() ) ) {
                ?>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <?php
            }

            // Add custom head code (sanitized)
            $head_code = get_theme_mod( 'nosfir_head_code', '' );
            if ( ! empty( $head_code ) && current_user_can( 'unfiltered_html' ) ) {
                echo $head_code;
            }
        }

        /**
         * WP Footer hook
         *
         * @since 1.0.0
         */
        public function wp_footer() {
            // Add custom footer code
            $footer_code = get_theme_mod( 'nosfir_footer_code', '' );
            if ( ! empty( $footer_code ) && current_user_can( 'unfiltered_html' ) ) {
                echo $footer_code;
            }

            // Remove no-js class
            ?>
            <script>
                document.documentElement.classList.remove('no-js');
                document.body.classList.remove('no-js');
                document.body.classList.add('js');
            </script>
            <?php
        }

        /**
         * Load more posts AJAX handler
         *
         * @since 1.0.0
         */
        public function ajax_load_more_posts() {
            // Verify nonce
            if ( ! check_ajax_referer( 'nosfir_nonce', 'nonce', false ) ) {
                wp_send_json_error( array( 'message' => __( 'Security check failed.', 'nosfir' ) ) );
            }

            $paged     = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
            $post_type = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'post';

            $args = array(
                'post_type'      => $post_type,
                'posts_per_page' => get_option( 'posts_per_page' ),
                'paged'          => $paged,
                'post_status'    => 'publish',
            );

            // Allow filtering of query args
            $args = apply_filters( 'nosfir_load_more_args', $args );

            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                ob_start();
                
                while ( $query->have_posts() ) {
                    $query->the_post();
                    get_template_part( 'template-parts/content', get_post_format() );
                }
                
                wp_reset_postdata();
                
                $html = ob_get_clean();
                
                wp_send_json_success(
                    array(
                        'html'      => $html,
                        'has_more'  => $paged < $query->max_num_pages,
                        'max_pages' => $query->max_num_pages,
                    )
                );
            } else {
                wp_send_json_error( array( 'message' => __( 'No more posts found.', 'nosfir' ) ) );
            }
        }

        /**
         * Custom excerpt more
         *
         * @since 1.0.0
         * @param string $more Excerpt more string.
         * @return string
         */
        public function custom_excerpt_more( $more ) {
            return '&hellip;';
        }

        /**
         * Custom excerpt length
         *
         * @since 1.0.0
         * @param int $length Excerpt length.
         * @return int
         */
        public function custom_excerpt_length( $length ) {
            return apply_filters( 'nosfir_excerpt_length', 30 );
        }

        /**
         * Custom password form
         *
         * @since 1.0.0
         * @param string $output Password form HTML.
         * @return string
         */
        public function custom_password_form( $output ) {
            global $post;
            
            $label = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );
            
            $output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
                <p>' . esc_html__( 'This content is password protected. Please enter the password to view it.', 'nosfir' ) . '</p>
                <p>
                    <label for="' . esc_attr( $label ) . '" class="screen-reader-text">' . esc_html__( 'Password:', 'nosfir' ) . '</label>
                    <input name="post_password" id="' . esc_attr( $label ) . '" type="password" size="20" placeholder="' . esc_attr__( 'Password', 'nosfir' ) . '" />
                    <button type="submit" class="button">' . esc_html__( 'Submit', 'nosfir' ) . '</button>
                </p>
            </form>';
            
            return $output;
        }

        /**
         * Add styles for embeds
         *
         * @since 1.0.0
         */
        public function print_embed_styles() {
            $primary_color = esc_attr( get_theme_mod( 'nosfir_primary_color', '#2c3e50' ) );
            $text_color    = esc_attr( get_theme_mod( 'nosfir_text_color', '#333333' ) );
            ?>
            <style type="text/css">
                .wp-embed {
                    padding: 2em;
                    border: 1px solid #e5e5e5;
                    border-radius: 4px;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    color: <?php echo $text_color; ?>;
                }
                .wp-embed-featured-image {
                    margin-bottom: 1.5em;
                }
                .wp-embed-featured-image img {
                    width: 100%;
                    height: auto;
                    border-radius: 4px;
                }
                .wp-embed-heading a {
                    color: <?php echo $primary_color; ?>;
                    text-decoration: none;
                }
                .wp-embed-heading a:hover {
                    text-decoration: underline;
                }
            </style>
            <?php
        }

        /**
         * Check if WooCommerce is activated
         *
         * @since 1.0.0
         * @return bool
         */
        public function is_woocommerce_activated() {
            return class_exists( 'WooCommerce' );
        }

        /**
         * Get setting
         *
         * @since 1.0.0
         * @param string $key     Setting key.
         * @param mixed  $default Default value.
         * @return mixed
         */
        public function get_setting( $key, $default = null ) {
            if ( isset( $this->settings[ $key ] ) ) {
                return $this->settings[ $key ];
            }
            return $default;
        }

        /**
         * Set setting
         *
         * @since 1.0.0
         * @param string $key   Setting key.
         * @param mixed  $value Setting value.
         */
        public function set_setting( $key, $value ) {
            $this->settings[ $key ] = $value;
        }
    }

endif;

/**
 * Returns the main instance of Nosfir
 *
 * @since 1.0.0
 * @return Nosfir
 */
function nosfir() {
    return Nosfir::instance();
}

// Initialize the theme
return nosfir();