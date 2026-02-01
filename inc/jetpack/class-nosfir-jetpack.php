<?php
/**
 * Nosfir Jetpack Integration Class
 *
 * Integra e otimiza todas as funcionalidades do Jetpack com o tema Nosfir,
 * incluindo Infinite Scroll, Photon, Related Posts, Social Sharing e mais.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_Jetpack')) :

    /**
     * Classe de integração com Jetpack
     */
    class Nosfir_Jetpack {

        /**
         * Instance única da classe
         *
         * @var Nosfir_Jetpack
         */
        private static $instance = null;

        /**
         * Módulos Jetpack suportados
         *
         * @var array
         */
        private $supported_modules = array();

        /**
         * Configurações do Jetpack
         *
         * @var array
         */
        private $settings = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_Jetpack
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
            // Verifica se Jetpack está ativo
            if (!$this->is_jetpack_active()) {
                return;
            }

            // Define módulos suportados
            $this->set_supported_modules();

            // Inicializa configurações
            $this->init_settings();

            // Setup hooks
            $this->setup_hooks();
        }

        /**
         * Verifica se Jetpack está ativo
         *
         * @return bool
         */
        private function is_jetpack_active() {
            return class_exists('Jetpack');
        }

        /**
         * Define módulos suportados
         */
        private function set_supported_modules() {
            $this->supported_modules = array(
                'infinite-scroll',
                'photon',
                'carousel',
                'related-posts',
                'markdown',
                'latex',
                'shortcodes',
                'widgets',
                'widget-visibility',
                'contact-form',
                'tiled-gallery',
                'custom-css',
                'sharedaddy',
                'comments',
                'likes',
                'subscriptions',
                'stats',
                'seo-tools',
                'verification-tools',
                'sitemaps',
                'lazy-images',
                'videopress'
            );

            // Filtro para permitir customização dos módulos suportados
            $this->supported_modules = apply_filters('nosfir_jetpack_supported_modules', $this->supported_modules);
        }

        /**
         * Inicializa configurações
         */
        private function init_settings() {
            $this->settings = array(
                'infinite_scroll' => array(
                    'container' => 'main',
                    'footer' => 'page',
                    'wrapper' => true,
                    'type' => 'scroll', // scroll, click
                    'posts_per_page' => get_option('posts_per_page'),
                ),
                'related_posts' => array(
                    'enabled' => true,
                    'show_headline' => true,
                    'show_thumbnails' => true,
                    'posts_count' => 3,
                ),
                'sharing' => array(
                    'enabled' => true,
                    'services' => array('facebook', 'twitter', 'linkedin', 'email'),
                    'show_on' => array('post', 'page'),
                ),
                'lazy_loading' => array(
                    'enabled' => true,
                    'images' => true,
                    'iframes' => true,
                ),
            );

            // Filtro para permitir customização das configurações
            $this->settings = apply_filters('nosfir_jetpack_settings', $this->settings);
        }

        /**
         * Setup hooks
         */
        private function setup_hooks() {
            // Jetpack setup
            add_action('after_setup_theme', array($this, 'jetpack_setup'));
            add_action('init', array($this, 'jetpack_init'));

            // Scripts e estilos
            add_action('wp_enqueue_scripts', array($this, 'enqueue_jetpack_assets'), 10);

            // Infinite Scroll
            add_action('init', array($this, 'setup_infinite_scroll'));

            // Related Posts
            add_filter('jetpack_relatedposts_filter_options', array($this, 'related_posts_options'));
            add_filter('jetpack_relatedposts_filter_headline', array($this, 'related_posts_headline'));

            // Sharing
            add_action('init', array($this, 'setup_sharing'));
            add_filter('sharing_show', array($this, 'sharing_show_filter'));

            // Photon (Image CDN)
            add_filter('jetpack_photon_url', array($this, 'photon_url_filter'), 10, 3);
            add_filter('jetpack_photon_pre_args', array($this, 'photon_args_filter'), 10, 3);

            // Lazy Loading
            add_filter('jetpack_lazy_images_settings', array($this, 'lazy_loading_settings'));

            // Content Options
            add_action('after_setup_theme', array($this, 'jetpack_content_options'));

            // Custom CSS
            add_filter('jetpack_custom_css_pre_stylesheet_custom', array($this, 'custom_css_filter'));

            // Comments
            add_filter('jetpack_comment_form_enabled_for_product', '__return_false');

            // WooCommerce specific
            if (class_exists('WooCommerce')) {
                add_action('init', array($this, 'jetpack_woocommerce_setup'));
            }

            // Admin
            add_action('admin_menu', array($this, 'add_jetpack_menu_item'));
            add_action('customize_register', array($this, 'jetpack_customizer_options'));

            // Performance optimizations
            add_filter('jetpack_implode_frontend_css', '__return_true');
            add_filter('jetpack_sharing_counts', '__return_true');

            // Remove Jetpack styles if needed
            add_action('wp_print_styles', array($this, 'remove_jetpack_styles'), 100);
        }

        /**
         * Jetpack setup
         */
        public function jetpack_setup() {
            // Adiciona suporte para Responsive Videos
            add_theme_support('jetpack-responsive-videos');

            // Adiciona suporte para Social Menus
            add_theme_support('jetpack-social-menu');

            // Adiciona suporte para Content Options
            add_theme_support('jetpack-content-options', array(
                'post-details' => array(
                    'stylesheet' => 'nosfir-style',
                    'date' => '.posted-on',
                    'categories' => '.cat-links',
                    'tags' => '.tags-links',
                    'author' => '.byline',
                    'comment' => '.comments-link',
                ),
                'featured-images' => array(
                    'archive' => true,
                    'archive-option' => 'nosfir_archive_featured_image',
                    'post' => true,
                    'post-option' => 'nosfir_post_featured_image',
                    'page' => true,
                    'page-option' => 'nosfir_page_featured_image',
                ),
            ));

            // Adiciona suporte para Portfolio
            add_theme_support('jetpack-portfolio');

            // Adiciona suporte para Testimonials
            add_theme_support('jetpack-testimonial');

            // Adiciona suporte para Nova
            add_theme_support('nova_menu_item');

            // Adiciona tamanhos de imagem para Jetpack
            add_image_size('nosfir-jetpack-thumbnail', 400, 300, true);
            add_image_size('nosfir-jetpack-featured', 1200, 630, true);
        }

        /**
         * Jetpack init
         */
        public function jetpack_init() {
            // Remove Open Graph tags padrão se Yoast SEO estiver ativo
            if (defined('WPSEO_VERSION')) {
                add_filter('jetpack_enable_open_graph', '__return_false');
            }

            // Customiza o output do Jetpack
            add_filter('jetpack_development_mode', array($this, 'development_mode'));
        }

        /**
         * Setup Infinite Scroll
         */
        public function setup_infinite_scroll() {
            // Adiciona suporte para Infinite Scroll
            add_theme_support('infinite-scroll', apply_filters('nosfir_jetpack_infinite_scroll_args', array(
                'type' => $this->settings['infinite_scroll']['type'],
                'container' => $this->settings['infinite_scroll']['container'],
                'footer' => $this->settings['infinite_scroll']['footer'],
                'wrapper' => $this->settings['infinite_scroll']['wrapper'],
                'render' => array($this, 'infinite_scroll_render'),
                'posts_per_page' => $this->settings['infinite_scroll']['posts_per_page'],
                'footer_widgets' => $this->has_footer_widgets(),
                'click_handle' => true,
            )));

            // Hooks específicos para WooCommerce
            if (class_exists('WooCommerce')) {
                add_action('init', array($this, 'jetpack_woocommerce_infinite_scroll'));
            }
        }

        /**
         * Renderiza conteúdo do Infinite Scroll
         */
        public function infinite_scroll_render() {
            // Hook antes do loop
            do_action('nosfir_jetpack_infinite_scroll_before');

            // WooCommerce products
            if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
                $this->render_woocommerce_products();
            }
            // Blog posts
            else {
                $this->render_blog_posts();
            }

            // Hook depois do loop
            do_action('nosfir_jetpack_infinite_scroll_after');
        }

        /**
         * Renderiza posts do blog para Infinite Scroll
         */
        private function render_blog_posts() {
            while (have_posts()) {
                the_post();
                
                // Determina o template part baseado no post format
                $post_format = get_post_format();
                
                if (is_search()) {
                    get_template_part('template-parts/content', 'search');
                } elseif ($post_format) {
                    get_template_part('template-parts/content', $post_format);
                } else {
                    get_template_part('template-parts/content', get_post_type());
                }
            }
        }

        /**
         * Renderiza produtos WooCommerce para Infinite Scroll
         */
        private function render_woocommerce_products() {
            do_action('nosfir_jetpack_product_infinite_scroll_before');
            
            // Inicia o loop de produtos
            if (function_exists('woocommerce_product_loop_start')) {
                woocommerce_product_loop_start();
            }

            while (have_posts()) {
                the_post();
                wc_get_template_part('content', 'product');
            }

            // Finaliza o loop de produtos
            if (function_exists('woocommerce_product_loop_end')) {
                woocommerce_product_loop_end();
            }
            
            do_action('nosfir_jetpack_product_infinite_scroll_after');
        }

        /**
         * Setup WooCommerce Infinite Scroll
         */
        public function jetpack_woocommerce_infinite_scroll() {
            add_action('nosfir_jetpack_product_infinite_scroll_before', 'woocommerce_product_loop_start');
            add_action('nosfir_jetpack_product_infinite_scroll_after', 'woocommerce_product_loop_end');
        }

        /**
         * WooCommerce Jetpack setup
         */
        public function jetpack_woocommerce_setup() {
            // Desabilita Jetpack comments em produtos
            add_filter('jetpack_comment_form_enabled_for_product', '__return_false');
            
            // Customiza related products via Jetpack
            if (Jetpack::is_module_active('related-posts')) {
                add_filter('woocommerce_product_related_posts_relate_by_category', '__return_true');
                add_filter('woocommerce_product_related_posts_relate_by_tag', '__return_true');
            }
        }

        /**
         * Configurações de Related Posts
         */
        public function related_posts_options($options) {
            if (!$this->settings['related_posts']['enabled']) {
                return $options;
            }

            $options['show_headline'] = $this->settings['related_posts']['show_headline'];
            $options['show_thumbnails'] = $this->settings['related_posts']['show_thumbnails'];
            $options['size'] = $this->settings['related_posts']['posts_count'];

            return apply_filters('nosfir_jetpack_related_posts_options', $options);
        }

        /**
         * Headline dos Related Posts
         */
        public function related_posts_headline($headline) {
            return apply_filters('nosfir_jetpack_related_posts_headline', 
                __('Related Articles', 'nosfir')
            );
        }

        /**
         * Setup Sharing
         */
        public function setup_sharing() {
            if (!$this->settings['sharing']['enabled']) {
                remove_filter('the_content', 'sharing_display', 19);
                remove_filter('the_excerpt', 'sharing_display', 19);
                return;
            }

            // Customiza posição dos botões de compartilhamento
            add_action('nosfir_after_post_content', array($this, 'display_sharing_buttons'));
        }

        /**
         * Exibe botões de compartilhamento
         */
        public function display_sharing_buttons() {
            if (function_exists('sharing_display')) {
                sharing_display('', true);
            }
        }

        /**
         * Filtro para mostrar sharing
         */
        public function sharing_show_filter($show) {
            if (!is_singular()) {
                return false;
            }

            $post_type = get_post_type();
            
            if (!in_array($post_type, $this->settings['sharing']['show_on'])) {
                return false;
            }

            return $show;
        }

        /**
         * Filtro para Photon URL
         */
        public function photon_url_filter($photon_url, $image_url, $args) {
            // Adiciona parâmetros customizados para Photon
            if (is_ssl()) {
                $photon_url = str_replace('http://', 'https://', $photon_url);
            }

            return apply_filters('nosfir_jetpack_photon_url', $photon_url, $image_url, $args);
        }

        /**
         * Filtro para argumentos do Photon
         */
        public function photon_args_filter($args, $image_url, $scheme) {
            // Define qualidade padrão
            if (!isset($args['quality'])) {
                $args['quality'] = apply_filters('nosfir_jetpack_photon_quality', 85);
            }

            // Adiciona strip para remover metadata
            $args['strip'] = 'all';

            return apply_filters('nosfir_jetpack_photon_args', $args, $image_url, $scheme);
        }

        /**
         * Configurações de Lazy Loading
         */
        public function lazy_loading_settings($settings) {
            if ($this->settings['lazy_loading']['enabled']) {
                $settings['images'] = $this->settings['lazy_loading']['images'];
                $settings['iframes'] = $this->settings['lazy_loading']['iframes'];
            }

            return apply_filters('nosfir_jetpack_lazy_loading_settings', $settings);
        }

        /**
         * Content Options do Jetpack
         */
        public function jetpack_content_options() {
            // Excerpt settings
            add_filter('jetpack_content_options_featured_images_archive', 
                function() { return get_theme_mod('nosfir_archive_featured_image', true); }
            );
            
            add_filter('jetpack_content_options_featured_images_post', 
                function() { return get_theme_mod('nosfir_post_featured_image', true); }
            );
            
            add_filter('jetpack_content_options_featured_images_page', 
                function() { return get_theme_mod('nosfir_page_featured_image', false); }
            );
        }

        /**
         * Filtro para Custom CSS
         */
        public function custom_css_filter($css) {
            // Adiciona CSS customizado do tema antes do CSS do Jetpack
            $theme_css = get_theme_mod('nosfir_custom_css', '');
            
            if (!empty($theme_css)) {
                $css = $theme_css . "\n" . $css;
            }

            return $css;
        }

        /**
         * Enfileira assets do Jetpack
         */
        public function enqueue_jetpack_assets() {
            $theme_version = wp_get_theme()->get('Version');

            // CSS para Infinite Scroll
            if (wp_style_is('the-neverending-homepage', 'enqueued')) {
                wp_enqueue_style(
                    'nosfir-jetpack-infinite-scroll',
                    get_template_directory_uri() . '/assets/css/jetpack/infinite-scroll.css',
                    array('the-neverending-homepage'),
                    $theme_version
                );
                wp_style_add_data('nosfir-jetpack-infinite-scroll', 'rtl', 'replace');

                // JavaScript adicional para Infinite Scroll
                wp_enqueue_script(
                    'nosfir-jetpack-infinite-scroll',
                    get_template_directory_uri() . '/assets/js/jetpack/infinite-scroll.js',
                    array('jquery', 'the-neverending-homepage'),
                    $theme_version,
                    true
                );
            }

            // CSS para Widgets do Jetpack
            if ($this->has_jetpack_widgets()) {
                wp_enqueue_style(
                    'nosfir-jetpack-widgets',
                    get_template_directory_uri() . '/assets/css/jetpack/widgets.css',
                    array(),
                    $theme_version
                );
                wp_style_add_data('nosfir-jetpack-widgets', 'rtl', 'replace');
            }

            // CSS para Related Posts
            if (Jetpack::is_module_active('related-posts')) {
                wp_enqueue_style(
                    'nosfir-jetpack-related-posts',
                    get_template_directory_uri() . '/assets/css/jetpack/related-posts.css',
                    array(),
                    $theme_version
                );
            }

            // CSS para Sharing
            if (Jetpack::is_module_active('sharedaddy')) {
                wp_enqueue_style(
                    'nosfir-jetpack-sharing',
                    get_template_directory_uri() . '/assets/css/jetpack/sharing.css',
                    array(),
                    $theme_version
                );
            }

            // CSS para Portfolio
            if (Jetpack::is_module_active('custom-content-types') && get_theme_support('jetpack-portfolio')) {
                wp_enqueue_style(
                    'nosfir-jetpack-portfolio',
                    get_template_directory_uri() . '/assets/css/jetpack/portfolio.css',
                    array(),
                    $theme_version
                );
            }

            // JavaScript global do Jetpack
            wp_enqueue_script(
                'nosfir-jetpack',
                get_template_directory_uri() . '/assets/js/jetpack/jetpack.js',
                array('jquery'),
                $theme_version,
                true
            );

            // Localização
            wp_localize_script('nosfir-jetpack', 'nosfir_jetpack', array(
                'infinite_scroll' => array(
                    'msgText' => __('Loading...', 'nosfir'),
                    'finishedMsg' => __('No more posts to load.', 'nosfir'),
                ),
                'sharing' => array(
                    'share_text' => __('Share this:', 'nosfir'),
                ),
            ));
        }

        /**
         * Remove estilos desnecessários do Jetpack
         */
        public function remove_jetpack_styles() {
            // Remove estilos que serão customizados pelo tema
            if (apply_filters('nosfir_remove_jetpack_styles', true)) {
                // wp_dequeue_style('jetpack_css');
                // wp_dequeue_style('AtD_style');
                // wp_dequeue_style('jetpack-carousel');
            }
        }

        /**
         * Adiciona item no menu admin
         */
        public function add_jetpack_menu_item() {
            if (current_user_can('manage_options')) {
                add_submenu_page(
                    'nosfir-dashboard',
                    __('Jetpack Settings', 'nosfir'),
                    __('Jetpack', 'nosfir'),
                    'manage_options',
                    'nosfir-jetpack',
                    array($this, 'jetpack_settings_page')
                );
            }
        }

        /**
         * Página de configurações do Jetpack
         */
        public function jetpack_settings_page() {
            ?>
            <div class="wrap">
                <h1><?php _e('Jetpack Integration Settings', 'nosfir'); ?></h1>
                
                <div class="nosfir-jetpack-settings">
                    <h2><?php _e('Active Jetpack Modules', 'nosfir'); ?></h2>
                    
                    <div class="jetpack-modules-list">
                        <?php
                        $active_modules = Jetpack::get_active_modules();
                        
                        foreach ($this->supported_modules as $module) {
                            $is_active = in_array($module, $active_modules);
                            ?>
                            <div class="jetpack-module-item">
                                <span class="module-name"><?php echo esc_html($module); ?></span>
                                <span class="module-status <?php echo $is_active ? 'active' : 'inactive'; ?>">
                                    <?php echo $is_active ? __('Active', 'nosfir') : __('Inactive', 'nosfir'); ?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    
                    <p class="description">
                        <?php
                        printf(
                            __('To manage Jetpack modules, visit the %sJetpack Settings%s page.', 'nosfir'),
                            '<a href="' . admin_url('admin.php?page=jetpack') . '">',
                            '</a>'
                        );
                        ?>
                    </p>
                </div>
            </div>
            <?php
        }

        /**
         * Opções do Jetpack no Customizer
         */
        public function jetpack_customizer_options($wp_customize) {
            // Seção Jetpack
            $wp_customize->add_section('nosfir_jetpack', array(
                'title' => __('Jetpack Options', 'nosfir'),
                'priority' => 85,
                'panel' => 'nosfir_panel',
            ));

            // Infinite Scroll Type
            $wp_customize->add_setting('nosfir_infinite_scroll_type', array(
                'default' => 'scroll',
                'sanitize_callback' => 'sanitize_key',
            ));

            $wp_customize->add_control('nosfir_infinite_scroll_type', array(
                'label' => __('Infinite Scroll Type', 'nosfir'),
                'section' => 'nosfir_jetpack',
                'type' => 'select',
                'choices' => array(
                    'scroll' => __('Scroll', 'nosfir'),
                    'click' => __('Click to Load', 'nosfir'),
                ),
            ));

            // Related Posts Count
            $wp_customize->add_setting('nosfir_related_posts_count', array(
                'default' => 3,
                'sanitize_callback' => 'absint',
            ));

            $wp_customize->add_control('nosfir_related_posts_count', array(
                'label' => __('Related Posts Count', 'nosfir'),
                'section' => 'nosfir_jetpack',
                'type' => 'number',
                'input_attrs' => array(
                    'min' => 1,
                    'max' => 6,
                ),
            ));

            // Show Share Buttons
            $wp_customize->add_setting('nosfir_show_share_buttons', array(
                'default' => true,
                'sanitize_callback' => 'nosfir_sanitize_checkbox',
            ));

            $wp_customize->add_control('nosfir_show_share_buttons', array(
                'label' => __('Show Share Buttons', 'nosfir'),
                'section' => 'nosfir_jetpack',
                'type' => 'checkbox',
            ));
        }

        /**
         * Verifica se há widgets no footer
         */
        private function has_footer_widgets() {
            $footer_widgets = array();
            
            for ($i = 1; $i <= 4; $i++) {
                if (is_active_sidebar('footer-' . $i)) {
                    $footer_widgets[] = 'footer-' . $i;
                }
            }

            return !empty($footer_widgets) ? $footer_widgets : false;
        }

        /**
         * Verifica se há widgets do Jetpack
         */
        private function has_jetpack_widgets() {
            global $wp_widget_factory;
            
            if (!isset($wp_widget_factory->widgets)) {
                return false;
            }

            foreach ($wp_widget_factory->widgets as $widget) {
                if (strpos(get_class($widget), 'Jetpack') !== false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Development mode
         */
        public function development_mode($development_mode) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return true;
            }

            return $development_mode;
        }

        /**
         * Obtém módulos ativos
         */
        public function get_active_modules() {
            if (!$this->is_jetpack_active()) {
                return array();
            }

            return Jetpack::get_active_modules();
        }

        /**
         * Verifica se um módulo está ativo
         */
        public function is_module_active($module) {
            if (!$this->is_jetpack_active()) {
                return false;
            }

            return Jetpack::is_module_active($module);
        }

        /**
         * Obtém configurações
         */
        public function get_settings() {
            return $this->settings;
        }

        /**
         * Obtém uma configuração específica
         */
        public function get_setting($key, $default = null) {
            return isset($this->settings[$key]) ? $this->settings[$key] : $default;
        }
    }

endif;

// Função helper para verificar se Jetpack está ativo
if (!function_exists('nosfir_is_jetpack_active')) :
    function nosfir_is_jetpack_active() {
        return class_exists('Jetpack');
    }
endif;

// Função helper para verificar se um módulo Jetpack está ativo
if (!function_exists('nosfir_is_jetpack_module_active')) :
    function nosfir_is_jetpack_module_active($module) {
        return class_exists('Jetpack') && Jetpack::is_module_active($module);
    }
endif;

// Inicializa a classe apenas se Jetpack estiver ativo
if (class_exists('Jetpack')) {
    return Nosfir_Jetpack::get_instance();
}