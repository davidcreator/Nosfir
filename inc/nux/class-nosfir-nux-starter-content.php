<?php
/**
 * Nosfir NUX Starter Content Class
 *
 * Gerencia todo o conteúdo inicial (starter content) do tema,
 * incluindo páginas, posts, produtos, menus, widgets e configurações.
 *
 * @package  Nosfir
 * @since    1.0.0
 * @author   David Creator
 */

// Evita acesso direto
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Nosfir_NUX_Starter_Content')) :

    /**
     * Classe de Starter Content
     */
    class Nosfir_NUX_Starter_Content {

        /**
         * Instance única da classe
         *
         * @var Nosfir_NUX_Starter_Content
         */
        private static $instance = null;

        /**
         * Configurações do starter content
         *
         * @var array
         */
        private $config = array();

        /**
         * Conteúdo starter registrado
         *
         * @var array
         */
        private $starter_content = array();

        /**
         * Templates de conteúdo
         *
         * @var array
         */
        private $content_templates = array();

        /**
         * Status da importação
         *
         * @var array
         */
        private $import_status = array();

        /**
         * Retorna a instância única da classe
         *
         * @return Nosfir_NUX_Starter_Content
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
            // Configurações padrão
            $this->config = array(
                'import_media' => true,
                'import_customizer' => true,
                'import_widgets' => true,
                'import_menus' => true,
                'set_homepage' => true,
                'set_blog_page' => true,
                'demo_type' => 'default',
                'clear_existing' => false,
            );

            // Status da importação
            $this->import_status = get_option('nosfir_starter_content_status', array(
                'imported' => false,
                'date' => '',
                'items' => array(),
            ));

            // Define templates de conteúdo
            $this->setup_content_templates();
        }

        /**
         * Setup hooks
         */
        private function setup_hooks() {
            // Registro do starter content
            add_action('after_setup_theme', array($this, 'register_starter_content'));
            
            // Filtros do starter content
            add_filter('get_theme_starter_content', array($this, 'filter_starter_content'), 10, 2);
            
            // Customizer preview
            add_action('customize_preview_init', array($this, 'customize_preview_init'));
            
            // WooCommerce hooks
            if (class_exists('WooCommerce')) {
                add_action('woocommerce_product_query', array($this, 'filter_product_query'));
                add_filter('woocommerce_shortcode_products_query', array($this, 'filter_products_shortcode'), 10, 3);
                add_action('customize_preview_init', array($this, 'setup_product_data'));
                add_filter('woocommerce_product_categories', array($this, 'filter_product_categories'));
            }
            
            // Widgets
            add_action('after_setup_theme', array($this, 'maybe_clear_widgets'));
            
            // Post transitions
            add_action('transition_post_status', array($this, 'handle_post_transition'), 10, 3);
            
            // Title filters
            add_filter('the_title', array($this, 'filter_auto_draft_title'), 10, 2);
            
            // AJAX handlers
            add_action('wp_ajax_nosfir_import_starter_content', array($this, 'ajax_import_content'));
            add_action('wp_ajax_nosfir_reset_starter_content', array($this, 'ajax_reset_content'));
            
            // Homepage content
            add_action('customize_preview_init', array($this, 'update_homepage_content'));
        }

        /**
         * Setup templates de conteúdo
         */
        private function setup_content_templates() {
            $this->content_templates = array(
                'business' => array(
                    'name' => __('Business', 'nosfir'),
                    'homepage' => 'business-home',
                    'pages' => array('services', 'portfolio', 'team', 'contact'),
                    'posts' => 6,
                    'products' => 0,
                ),
                'blog' => array(
                    'name' => __('Blog', 'nosfir'),
                    'homepage' => 'blog-home',
                    'pages' => array('about', 'contact'),
                    'posts' => 12,
                    'products' => 0,
                ),
                'portfolio' => array(
                    'name' => __('Portfolio', 'nosfir'),
                    'homepage' => 'portfolio-home',
                    'pages' => array('about', 'services', 'contact'),
                    'posts' => 3,
                    'products' => 0,
                ),
                'shop' => array(
                    'name' => __('Shop', 'nosfir'),
                    'homepage' => 'shop-home',
                    'pages' => array('about', 'contact', 'faq', 'shipping'),
                    'posts' => 3,
                    'products' => 12,
                ),
            );

            $this->content_templates = apply_filters('nosfir_content_templates', $this->content_templates);
        }

        /**
         * Registra starter content
         */
        public function register_starter_content() {
            $starter_content = array(
                'posts' => $this->get_starter_posts(),
                'attachments' => $this->get_starter_attachments(),
                'options' => $this->get_starter_options(),
                'theme_mods' => $this->get_starter_theme_mods(),
                'widgets' => $this->get_starter_widgets(),
                'nav_menus' => $this->get_starter_nav_menus(),
            );

            // Adiciona produtos se WooCommerce estiver ativo
            if (class_exists('WooCommerce')) {
                $products = $this->get_starter_products();
                if (!empty($products)) {
                    $starter_content['posts'] = array_merge($starter_content['posts'], $products);
                }
            }

            // Permite filtrar o conteúdo
            $starter_content = apply_filters('nosfir_starter_content', $starter_content);

            // Registra suporte ao starter content
            add_theme_support('starter-content', $starter_content);
        }

        /**
         * Obtém posts iniciais
         */
        private function get_starter_posts() {
            $posts = array(
                // Homepage
                'home' => array(
                    'post_type' => 'page',
                    'post_title' => _x('Home', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_homepage_content(),
                    'template' => 'template-homepage.php',
                ),
                
                // About page
                'about' => array(
                    'post_type' => 'page',
                    'post_title' => _x('About', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_about_content(),
                ),
                
                // Services page
                'services' => array(
                    'post_type' => 'page',
                    'post_title' => _x('Services', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_services_content(),
                ),
                
                // Contact page
                'contact' => array(
                    'post_type' => 'page',
                    'post_title' => _x('Contact', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_contact_content(),
                ),
                
                // Blog page
                'blog' => array(
                    'post_type' => 'page',
                    'post_title' => _x('Blog', 'Theme starter content', 'nosfir'),
                ),
                
                // Sample posts
                'post-1' => array(
                    'post_type' => 'post',
                    'post_title' => _x('Welcome to Nosfir Theme', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_sample_post_content(1),
                    'thumbnail' => '{{post-featured-image-1}}',
                ),
                
                'post-2' => array(
                    'post_type' => 'post',
                    'post_title' => _x('Getting Started Guide', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_sample_post_content(2),
                    'thumbnail' => '{{post-featured-image-2}}',
                ),
                
                'post-3' => array(
                    'post_type' => 'post',
                    'post_title' => _x('Customization Tips', 'Theme starter content', 'nosfir'),
                    'post_content' => $this->get_sample_post_content(3),
                    'thumbnail' => '{{post-featured-image-3}}',
                ),
            );

            // Adiciona nome do post para cada item
            foreach ($posts as $id => &$post) {
                $post['post_name'] = $id;
            }

            return $posts;
        }

        /**
         * Obtém attachments iniciais
         */
        private function get_starter_attachments() {
            $attachments = array(
                // Hero images
                'hero-image' => array(
                    'post_title' => _x('Hero Image', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/hero.jpg',
                ),
                
                // Post featured images
                'post-featured-image-1' => array(
                    'post_title' => _x('Post Image 1', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/post-1.jpg',
                ),
                
                'post-featured-image-2' => array(
                    'post_title' => _x('Post Image 2', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/post-2.jpg',
                ),
                
                'post-featured-image-3' => array(
                    'post_title' => _x('Post Image 3', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/post-3.jpg',
                ),
                
                // Service images
                'service-image-1' => array(
                    'post_title' => _x('Service 1', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/service-1.jpg',
                ),
                
                'service-image-2' => array(
                    'post_title' => _x('Service 2', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/service-2.jpg',
                ),
                
                'service-image-3' => array(
                    'post_title' => _x('Service 3', 'Theme starter content', 'nosfir'),
                    'file' => 'assets/images/starter-content/service-3.jpg',
                ),
            );

            // Adiciona imagens de produtos se WooCommerce estiver ativo
            if (class_exists('WooCommerce')) {
                $product_images = $this->get_product_images();
                $attachments = array_merge($attachments, $product_images);
            }

            // Define post_name para cada attachment
            foreach ($attachments as $id => &$attachment) {
                $attachment['post_name'] = $id;
            }

            return $attachments;
        }

        /**
         * Obtém produtos iniciais para WooCommerce
         */
        private function get_starter_products() {
            if (!class_exists('WooCommerce')) {
                return array();
            }

            $products = array();
            
            // Categorias de produtos
            $categories = array(
                'clothing' => array(
                    'name' => __('Clothing', 'nosfir'),
                    'slug' => 'clothing',
                ),
                'accessories' => array(
                    'name' => __('Accessories', 'nosfir'),
                    'slug' => 'accessories',
                ),
                'electronics' => array(
                    'name' => __('Electronics', 'nosfir'),
                    'slug' => 'electronics',
                ),
            );

            // Produtos de exemplo
            $sample_products = array(
                'product-1' => array(
                    'post_title' => __('Premium T-Shirt', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-1}}',
                    'product_data' => array(
                        'regular_price' => '29.99',
                        'sale_price' => '24.99',
                        'featured' => true,
                        'category' => 'clothing',
                    ),
                ),
                
                'product-2' => array(
                    'post_title' => __('Designer Watch', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-2}}',
                    'product_data' => array(
                        'regular_price' => '199.99',
                        'sale_price' => '149.99',
                        'featured' => true,
                        'category' => 'accessories',
                    ),
                ),
                
                'product-3' => array(
                    'post_title' => __('Wireless Headphones', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-3}}',
                    'product_data' => array(
                        'regular_price' => '89.99',
                        'featured' => true,
                        'category' => 'electronics',
                    ),
                ),
                
                'product-4' => array(
                    'post_title' => __('Leather Wallet', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-4}}',
                    'product_data' => array(
                        'regular_price' => '49.99',
                        'category' => 'accessories',
                    ),
                ),
                
                'product-5' => array(
                    'post_title' => __('Smart Speaker', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-5}}',
                    'product_data' => array(
                        'regular_price' => '79.99',
                        'sale_price' => '59.99',
                        'category' => 'electronics',
                    ),
                ),
                
                'product-6' => array(
                    'post_title' => __('Sunglasses', 'nosfir'),
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'post_content' => $this->get_product_description(),
                    'thumbnail' => '{{product-image-6}}',
                    'product_data' => array(
                        'regular_price' => '39.99',
                        'category' => 'accessories',
                    ),
                ),
            );

            foreach ($sample_products as $id => $product) {
                $product['post_name'] = $id;
                $product['comment_status'] = 'open';
                $products[$id] = $product;
            }

            return $products;
        }

        /**
         * Obtém imagens de produtos
         */
        private function get_product_images() {
            $images = array();
            
            for ($i = 1; $i <= 6; $i++) {
                $images['product-image-' . $i] = array(
                    'post_title' => sprintf(__('Product %d', 'nosfir'), $i),
                    'file' => 'assets/images/starter-content/products/product-' . $i . '.jpg',
                );
            }
            
            return $images;
        }

        /**
         * Obtém opções iniciais
         */
        private function get_starter_options() {
            return array(
                'show_on_front' => 'page',
                'page_on_front' => '{{home}}',
                'page_for_posts' => '{{blog}}',
                'posts_per_page' => 9,
                'date_format' => 'F j, Y',
                'time_format' => 'g:i a',
                'start_of_week' => 1,
            );
        }

        /**
         * Obtém theme mods iniciais
         */
        private function get_starter_theme_mods() {
            return array(
                'nosfir_primary_color' => '#2563eb',
                'nosfir_secondary_color' => '#64748b',
                'nosfir_accent_color' => '#f59e0b',
                'nosfir_header_layout' => 'layout-1',
                'nosfir_footer_widgets' => 4,
                'nosfir_sidebar_layout' => 'right',
                'nosfir_blog_layout' => 'grid',
            );
        }

        /**
         * Obtém widgets iniciais
         */
        private function get_starter_widgets() {
            return array(
                'sidebar-1' => array(
                    'search',
                    'recent-posts',
                    'categories',
                    'archives',
                ),
                
                'footer-1' => array(
                    'text_about' => array(
                        'text',
                        array(
                            'title' => __('About Us', 'nosfir'),
                            'text' => __('We are a creative agency focused on delivering exceptional digital experiences.', 'nosfir'),
                        ),
                    ),
                ),
                
                'footer-2' => array(
                    'nav_menu' => array(
                        'nav_menu',
                        array(
                            'title' => __('Quick Links', 'nosfir'),
                            'nav_menu' => '{{primary}}',
                        ),
                    ),
                ),
                
                'footer-3' => array(
                    'text_contact' => array(
                        'text',
                        array(
                            'title' => __('Contact Info', 'nosfir'),
                            'text' => __('123 Main Street<br>City, State 12345<br>Phone: (555) 123-4567<br>Email: info@example.com', 'nosfir'),
                        ),
                    ),
                ),
                
                'footer-4' => array(
                    'text_social' => array(
                        'text',
                        array(
                            'title' => __('Follow Us', 'nosfir'),
                            'text' => $this->get_social_links_html(),
                        ),
                    ),
                ),
            );
        }

        /**
         * Obtém menus de navegação iniciais
         */
        private function get_starter_nav_menus() {
            $menus = array(
                'primary' => array(
                    'name' => __('Primary Menu', 'nosfir'),
                    'items' => array(
                        'home' => array(
                            'type' => 'post_type',
                            'object' => 'page',
                            'object_id' => '{{home}}',
                        ),
                        'about' => array(
                            'type' => 'post_type',
                            'object' => 'page',
                            'object_id' => '{{about}}',
                        ),
                        'services' => array(
                            'type' => 'post_type',
                            'object' => 'page',
                            'object_id' => '{{services}}',
                        ),
                        'blog' => array(
                            'type' => 'post_type',
                            'object' => 'page',
                            'object_id' => '{{blog}}',
                        ),
                        'contact' => array(
                            'type' => 'post_type',
                            'object' => 'page',
                            'object_id' => '{{contact}}',
                        ),
                    ),
                ),
                
                'footer' => array(
                    'name' => __('Footer Menu', 'nosfir'),
                    'items' => array(
                        'privacy' => array(
                            'type' => 'custom',
                            'title' => __('Privacy Policy', 'nosfir'),
                            'url' => '#',
                        ),
                        'terms' => array(
                            'type' => 'custom',
                            'title' => __('Terms of Service', 'nosfir'),
                            'url' => '#',
                        ),
                        'sitemap' => array(
                            'type' => 'custom',
                            'title' => __('Sitemap', 'nosfir'),
                            'url' => '#',
                        ),
                    ),
                ),
            );

            // Adiciona menu do WooCommerce se ativo
            if (class_exists('WooCommerce')) {
                $menus['primary']['items']['shop'] = array(
                    'type' => 'post_type',
                    'object' => 'page',
                    'object_id' => '{{shop}}',
                );
                
                // Adiciona páginas do WooCommerce
                $wc_pages = $this->get_woocommerce_pages();
                foreach ($wc_pages as $page_key => $page_id) {
                    $menus['footer']['items'][$page_key] = array(
                        'type' => 'post_type',
                        'object' => 'page',
                        'object_id' => '{{' . $page_key . '}}',
                    );
                }
            }

            return $menus;
        }

        /**
         * Conteúdo da homepage
         */
        private function get_homepage_content() {
            return '
                <!-- wp:cover {"url":"{{hero-image}}","dimRatio":40,"align":"full"} -->
                <div class="wp-block-cover alignfull">
                    <div class="wp-block-cover__inner-container">
                        <!-- wp:heading {"level":1,"align":"center"} -->
                        <h1 class="has-text-align-center">' . __('Welcome to Nosfir', 'nosfir') . '</h1>
                        <!-- /wp:heading -->
                        
                        <!-- wp:paragraph {"align":"center"} -->
                        <p class="has-text-align-center">' . __('Create amazing websites with our powerful theme', 'nosfir') . '</p>
                        <!-- /wp:paragraph -->
                        
                        <!-- wp:buttons {"align":"center"} -->
                        <div class="wp-block-buttons aligncenter">
                            <!-- wp:button -->
                            <div class="wp-block-button">
                                <a class="wp-block-button__link">' . __('Get Started', 'nosfir') . '</a>
                            </div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                </div>
                <!-- /wp:cover -->
                
                ' . $this->get_features_section() . '
                ' . $this->get_services_section() . '
                ' . $this->get_cta_section();
        }

        /**
         * Seção de features
         */
        private function get_features_section() {
            return '
                <!-- wp:group {"align":"full"} -->
                <div class="wp-block-group alignfull">
                    <!-- wp:heading {"align":"center"} -->
                    <h2 class="has-text-align-center">' . __('Our Features', 'nosfir') . '</h2>
                    <!-- /wp:heading -->
                    
                    <!-- wp:columns -->
                    <div class="wp-block-columns">
                        <!-- wp:column -->
                        <div class="wp-block-column">
                            <!-- wp:heading {"level":3} -->
                            <h3>' . __('Responsive Design', 'nosfir') . '</h3>
                            <!-- /wp:heading -->
                            <!-- wp:paragraph -->
                            <p>' . __('Your site will look perfect on all devices.', 'nosfir') . '</p>
                            <!-- /wp:paragraph -->
                        </div>
                        <!-- /wp:column -->
                        
                        <!-- wp:column -->
                        <div class="wp-block-column">
                            <!-- wp:heading {"level":3} -->
                            <h3>' . __('Fast Performance', 'nosfir') . '</h3>
                            <!-- /wp:heading -->
                            <!-- wp:paragraph -->
                            <p>' . __('Optimized for speed and performance.', 'nosfir') . '</p>
                            <!-- /wp:paragraph -->
                        </div>
                        <!-- /wp:column -->
                        
                        <!-- wp:column -->
                        <div class="wp-block-column">
                            <!-- wp:heading {"level":3} -->
                            <h3>' . __('SEO Ready', 'nosfir') . '</h3>
                            <!-- /wp:heading -->
                            <!-- wp:paragraph -->
                            <p>' . __('Built with SEO best practices in mind.', 'nosfir') . '</p>
                            <!-- /wp:paragraph -->
                        </div>
                        <!-- /wp:column -->
                    </div>
                    <!-- /wp:columns -->
                </div>
                <!-- /wp:group -->';
        }

        /**
         * Seção de serviços
         */
        private function get_services_section() {
            return '
                <!-- wp:group {"align":"full"} -->
                <div class="wp-block-group alignfull">
                    <!-- wp:heading {"align":"center"} -->
                    <h2 class="has-text-align-center">' . __('Our Services', 'nosfir') . '</h2>
                    <!-- /wp:heading -->
                    
                    <!-- wp:latest-posts {"postsToShow":3,"displayPostContent":true,"excerptLength":20,"displayFeaturedImage":true,"featuredImageAlign":"left"} /-->
                </div>
                <!-- /wp:group -->';
        }

        /**
         * Seção CTA
         */
        private function get_cta_section() {
            return '
                <!-- wp:group {"align":"full","backgroundColor":"primary"} -->
                <div class="wp-block-group alignfull has-primary-background-color has-background">
                    <!-- wp:heading {"align":"center","textColor":"white"} -->
                    <h2 class="has-text-align-center has-white-color">' . __('Ready to get started?', 'nosfir') . '</h2>
                    <!-- /wp:heading -->
                    
                    <!-- wp:paragraph {"align":"center","textColor":"white"} -->
                    <p class="has-text-align-center has-white-color">' . __('Contact us today for a free consultation.', 'nosfir') . '</p>
                    <!-- /wp:paragraph -->
                    
                    <!-- wp:buttons {"align":"center"} -->
                    <div class="wp-block-buttons aligncenter">
                        <!-- wp:button {"backgroundColor":"white","textColor":"primary"} -->
                        <div class="wp-block-button">
                            <a class="wp-block-button__link has-primary-color has-white-background-color">' . __('Contact Us', 'nosfir') . '</a>
                        </div>
                        <!-- /wp:button -->
                    </div>
                    <!-- /wp:buttons -->
                </div>
                <!-- /wp:group -->';
        }

        /**
         * Conteúdo da página About
         */
        private function get_about_content() {
            return sprintf(
                __('We are a team of passionate professionals dedicated to creating exceptional digital experiences. With years of experience in web design and development, we help businesses establish a strong online presence.', 'nosfir')
            );
        }

        /**
         * Conteúdo da página Services
         */
        private function get_services_content() {
            return sprintf(
                __('We offer a comprehensive range of services to help your business succeed online. From web design and development to digital marketing and SEO, we have the expertise to take your online presence to the next level.', 'nosfir')
            );
        }

        /**
         * Conteúdo da página Contact
         */
        private function get_contact_content() {
            return sprintf(
                __('Get in touch with us today. We\'d love to hear about your project and discuss how we can help you achieve your goals.', 'nosfir')
            );
        }

        /**
         * Conteúdo de post de exemplo
         */
        private function get_sample_post_content($post_number) {
            $contents = array(
                1 => __('Welcome to your new website powered by Nosfir theme! This is your first blog post. Edit or delete it, then start writing! Nosfir offers powerful features to help you create amazing content.', 'nosfir'),
                2 => __('Getting started with Nosfir is easy. Simply navigate to the Customizer to personalize your site\'s appearance, or use our Setup Wizard for a guided configuration process.', 'nosfir'),
                3 => __('Customize every aspect of your site with our intuitive theme options. From colors and typography to layouts and functionality, Nosfir gives you complete control.', 'nosfir'),
            );
            
            return isset($contents[$post_number]) ? $contents[$post_number] : $contents[1];
        }

        /**
         * Descrição de produto
         */
        private function get_product_description() {
            return __('This is a sample product description. This product features high-quality materials and excellent craftsmanship. Perfect for everyday use or special occasions.', 'nosfir');
        }

        /**
         * HTML de links sociais
         */
        private function get_social_links_html() {
            return '
                <div class="social-links">
                    <a href="#" target="_blank">Facebook</a> | 
                    <a href="#" target="_blank">Twitter</a> | 
                    <a href="#" target="_blank">Instagram</a> | 
                    <a href="#" target="_blank">LinkedIn</a>
                </div>
            ';
        }

        /**
         * Filtra starter content
         */
        public function filter_starter_content($content, $config) {
            // Verifica se deve filtrar
            if (!isset($_GET['nosfir_starter_content']) || !$_GET['nosfir_starter_content']) {
                return $content;
            }

            // Obtém tarefas
            $tasks = isset($_GET['nosfir_tasks']) ? explode(',', sanitize_text_field($_GET['nosfir_tasks'])) : array();
            
            // Processa tarefas
            foreach ($tasks as $task) {
                switch ($task) {
                    case 'homepage':
                        // Mantém apenas homepage
                        unset($content['options']);
                        break;
                        
                    case 'products':
                        // Remove produtos se não solicitado
                        foreach ($content['posts'] as $key => $post) {
                            if (isset($post['post_type']) && $post['post_type'] === 'product') {
                                unset($content['posts'][$key]);
                            }
                        }
                        break;
                }
            }

            return $content;
        }

        /**
         * Maybe clear widgets
         */
        public function maybe_clear_widgets() {
            if (get_option('nosfir_widgets_cleared')) {
                return;
            }

            if (get_option('fresh_site')) {
                update_option('sidebars_widgets', array('wp_inactive_widgets' => array()));
                update_option('nosfir_widgets_cleared', true);
            }
        }

        /**
         * AJAX: Import content
         */
        public function ajax_import_content() {
            check_ajax_referer('nosfir-import', 'nonce');

            if (!current_user_can('edit_theme_options')) {
                wp_send_json_error('Insufficient permissions');
            }

            $type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : 'all';
            
            // Inicia importação
            $result = $this->import_content($type);
            
            if ($result) {
                wp_send_json_success(array(
                    'message' => __('Content imported successfully!', 'nosfir'),
                    'imported_items' => $result,
                ));
            } else {
                wp_send_json_error(__('Failed to import content', 'nosfir'));
            }
        }

        /**
         * Importa conteúdo
         */
        private function import_content($type = 'all') {
            $imported = array();
            
            // Importa diferentes tipos de conteúdo
            if ($type === 'all' || $type === 'pages') {
                $imported['pages'] = $this->import_pages();
            }
            
            if ($type === 'all' || $type === 'posts') {
                $imported['posts'] = $this->import_posts();
            }
            
            if ($type === 'all' || $type === 'products') {
                if (class_exists('WooCommerce')) {
                    $imported['products'] = $this->import_products();
                }
            }
            
            if ($type === 'all' || $type === 'widgets') {
                $imported['widgets'] = $this->import_widgets();
            }
            
            if ($type === 'all' || $type === 'menus') {
                $imported['menus'] = $this->import_menus();
            }
            
            // Atualiza status
            $this->import_status['imported'] = true;
            $this->import_status['date'] = current_time('mysql');
            $this->import_status['items'] = $imported;
            
            update_option('nosfir_starter_content_status', $this->import_status);
            
            return $imported;
        }

        /**
         * Importa páginas
         */
        private function import_pages() {
            // Implementação da importação de páginas
            return array();
        }

        /**
         * Importa posts
         */
        private function import_posts() {
            // Implementação da importação de posts
            return array();
        }

        /**
         * Importa produtos
         */
        private function import_products() {
            // Implementação da importação de produtos
            return array();
        }

        /**
         * Importa widgets
         */
        private function import_widgets() {
            // Implementação da importação de widgets
            return array();
        }

        /**
         * Importa menus
         */
        private function import_menus() {
            // Implementação da importação de menus
            return array();
        }

        /**
         * Obtém páginas do WooCommerce
         */
        private function get_woocommerce_pages() {
            if (!class_exists('WooCommerce')) {
                return array();
            }

            $pages = array();
            
            $wc_pages = array(
                'shop' => wc_get_page_id('shop'),
                'cart' => wc_get_page_id('cart'),
                'checkout' => wc_get_page_id('checkout'),
                'myaccount' => wc_get_page_id('myaccount'),
            );
            
            foreach ($wc_pages as $key => $page_id) {
                if ($page_id > 0) {
                    $pages[$key] = $page_id;
                }
            }
            
            return $pages;
        }
    }

endif;

// Inicializa
return Nosfir_NUX_Starter_Content::get_instance();