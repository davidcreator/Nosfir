<?php
/**
 * Nosfir Class
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
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->version = wp_get_theme()->get( 'Version' );
			
			// Setup theme
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			
			// Register widgets
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			
			// Enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'child_scripts' ), 30 );
			add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			
			// Body classes
			add_filter( 'body_class', array( $this, 'body_classes' ) );
			
			// Page menu args
			add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );
			
			// Navigation
			add_filter( 'navigation_markup_template', array( $this, 'navigation_markup_template' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 2 );
			add_filter( 'nav_menu_item_id', array( $this, 'nav_menu_item_id' ), 10, 2 );
			
			// Embeds
			add_action( 'enqueue_embed_scripts', array( $this, 'print_embed_styles' ) );
			
			// Content width
			add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
			
			// Custom hooks
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'wp_head', array( $this, 'wp_head' ), 5 );
			add_action( 'wp_footer', array( $this, 'wp_footer' ) );
			
			// AJAX handlers
			add_action( 'wp_ajax_nosfir_load_more', array( $this, 'load_more_posts' ) );
			add_action( 'wp_ajax_nopriv_nosfir_load_more', array( $this, 'load_more_posts' ) );
			
			// Theme updates
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 */
		public function setup() {
			/**
			 * Load Localisation files
			 */
			// Loads wp-content/languages/themes/nosfir-it_IT.mo
			load_theme_textdomain( 'nosfir', trailingslashit( WP_LANG_DIR ) . 'themes' );

			// Loads wp-content/themes/child-theme-name/languages/it_IT.mo
			load_theme_textdomain( 'nosfir', get_stylesheet_directory() . '/languages' );

			// Loads wp-content/themes/nosfir/languages/it_IT.mo
			load_theme_textdomain( 'nosfir', get_template_directory() . '/languages' );

			/**
			 * Add default posts and comments RSS feed links to head
			 */
			add_theme_support( 'automatic-feed-links' );

			/**
			 * Enable support for Post Thumbnails on posts and pages
			 */
			add_theme_support( 'post-thumbnails' );

			// Set default sizes
			set_post_thumbnail_size( 825, 510, true );
			
			// Add custom image sizes
			add_image_size( 'nosfir-featured', 1920, 1080, true );
			add_image_size( 'nosfir-medium', 768, 432, true );
			add_image_size( 'nosfir-small', 375, 211, true );
			add_image_size( 'nosfir-square', 600, 600, true );

			/**
			 * Enable support for site logo
			 */
			add_theme_support(
				'custom-logo',
				apply_filters(
					'nosfir_custom_logo_args',
					array(
						'height'      => 100,
						'width'       => 400,
						'flex-width'  => true,
						'flex-height' => true,
						'unlink-homepage-logo' => false,
					)
				)
			);

			/**
			 * Register menu locations
			 */
			register_nav_menus(
				apply_filters(
					'nosfir_register_nav_menus',
					array(
						'primary'   => __( 'Primary Menu', 'nosfir' ),
						'secondary' => __( 'Secondary Menu', 'nosfir' ),
						'mobile'    => __( 'Mobile Menu', 'nosfir' ),
						'footer'    => __( 'Footer Menu', 'nosfir' ),
						'social'    => __( 'Social Menu', 'nosfir' ),
					)
				)
			);

			/**
			 * Switch default core markup to output valid HTML5
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

			/**
			 * Enable support for Post Formats
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

			/**
			 * Setup the WordPress core custom background feature
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

			/**
			 * Setup the WordPress core custom header feature
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
					)
				)
			);

			/**
			 * Enable support for title tag
			 */
			add_theme_support( 'title-tag' );

			/**
			 * Enable support for selective refreshing of widgets
			 */
			add_theme_support( 'customize-selective-refresh-widgets' );

			/**
			 * Add support for Block Styles
			 */
			add_theme_support( 'wp-block-styles' );

			/**
			 * Add support for full and wide align images
			 */
			add_theme_support( 'align-wide' );

			/**
			 * Add support for editor styles
			 */
			add_theme_support( 'editor-styles' );

			/**
			 * Custom editor font sizes
			 */
			add_theme_support(
				'editor-font-sizes',
				array(
					array(
						'name' => __( 'Small', 'nosfir' ),
						'size' => 12,
						'slug' => 'small',
					),
					array(
						'name' => __( 'Normal', 'nosfir' ),
						'size' => 16,
						'slug' => 'normal',
					),
					array(
						'name' => __( 'Medium', 'nosfir' ),
						'size' => 20,
						'slug' => 'medium',
					),
					array(
						'name' => __( 'Large', 'nosfir' ),
						'size' => 24,
						'slug' => 'large',
					),
					array(
						'name' => __( 'Extra Large', 'nosfir' ),
						'size' => 32,
						'slug' => 'extra-large',
					),
					array(
						'name' => __( 'Huge', 'nosfir' ),
						'size' => 48,
						'slug' => 'huge',
					),
				)
			);

			/**
			 * Custom color palette
			 */
			add_theme_support(
				'editor-color-palette',
				array(
					array(
						'name'  => __( 'Primary', 'nosfir' ),
						'slug'  => 'primary',
						'color' => get_theme_mod( 'nosfir_primary_color', '#2c3e50' ),
					),
					array(
						'name'  => __( 'Secondary', 'nosfir' ),
						'slug'  => 'secondary',
						'color' => get_theme_mod( 'nosfir_secondary_color', '#e74c3c' ),
					),
					array(
						'name'  => __( 'Dark', 'nosfir' ),
						'slug'  => 'dark',
						'color' => '#1a1a1a',
					),
					array(
						'name'  => __( 'Light', 'nosfir' ),
						'slug'  => 'light',
						'color' => '#f8f9fa',
					),
					array(
						'name'  => __( 'White', 'nosfir' ),
						'slug'  => 'white',
						'color' => '#ffffff',
					),
				)
			);

			/**
			 * Enqueue editor styles
			 */
			add_editor_style( array( 'assets/css/editor-style.css', $this->google_fonts() ) );

			/**
			 * Add support for responsive embedded content
			 */
			add_theme_support( 'responsive-embeds' );

			/**
			 * Add support for custom line height
			 */
			add_theme_support( 'custom-line-height' );

			/**
			 * Add support for custom units
			 */
			add_theme_support( 'custom-units', array( 'px', 'em', 'rem', '%', 'vh', 'vw' ) );

			/**
			 * Add support for appearance tools
			 */
			add_theme_support( 'appearance-tools' );

			/**
			 * Add support for link color control
			 */
			add_theme_support( 'link-color' );

			/**
			 * Add support for experimental features
			 */
			add_theme_support( 'custom-spacing' );
			add_theme_support( 'border' );

			/**
			 * Add theme support for WooCommerce
			 */
			if ( class_exists( 'WooCommerce' ) ) {
				add_theme_support( 'woocommerce' );
				add_theme_support( 'wc-product-gallery-zoom' );
				add_theme_support( 'wc-product-gallery-lightbox' );
				add_theme_support( 'wc-product-gallery-slider' );
			}

			/**
			 * Add support for core custom logo
			 */
			add_theme_support( 'custom-logo' );

			/**
			 * Add support for widgets block editor
			 */
			add_theme_support( 'widgets-block-editor' );

			/**
			 * Add support for AMP if plugin is active
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
		}

		/**
		 * Set the content width in pixels
		 */
		public function content_width() {
			$GLOBALS['content_width'] = apply_filters( 'nosfir_content_width', 1140 );
		}

		/**
		 * Register widget areas
		 */
		public function widgets_init() {
			$sidebar_args = array();

			$sidebar_args['sidebar'] = array(
				'name'        => __( 'Sidebar', 'nosfir' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Main sidebar that appears on the left or right.', 'nosfir' ),
			);

			$sidebar_args['header'] = array(
				'name'        => __( 'Header Widget Area', 'nosfir' ),
				'id'          => 'header-1',
				'description' => __( 'Widgets in this area will be shown in the header.', 'nosfir' ),
			);

			if ( class_exists( 'WooCommerce' ) ) {
				$sidebar_args['shop'] = array(
					'name'        => __( 'Shop Sidebar', 'nosfir' ),
					'id'          => 'sidebar-shop',
					'description' => __( 'Widgets for WooCommerce shop pages.', 'nosfir' ),
				);
			}

			$rows    = intval( apply_filters( 'nosfir_footer_widget_rows', 1 ) );
			$regions = intval( apply_filters( 'nosfir_footer_widget_columns', 4 ) );

			for ( $row = 1; $row <= $rows; $row++ ) {
				for ( $region = 1; $region <= $regions; $region++ ) {
					$footer_n = $region + $regions * ( $row - 1 );
					$footer   = sprintf( 'footer_%d', $footer_n );

					if ( 1 === $rows ) {
						$footer_region_name = sprintf( __( 'Footer Column %1$d', 'nosfir' ), $region );
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', 'nosfir' ), $region );
					} else {
						$footer_region_name = sprintf( __( 'Footer Row %1$d - Column %2$d', 'nosfir' ), $row, $region );
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'nosfir' ), $region, $row );
					}

					$sidebar_args[ $footer ] = array(
						'name'        => $footer_region_name,
						'id'          => sprintf( 'footer-%d', $footer_n ),
						'description' => $footer_region_description,
					);
				}
			}

			$sidebar_args = apply_filters( 'nosfir_sidebar_args', $sidebar_args );

			foreach ( $sidebar_args as $sidebar => $args ) {
				$widget_tags = array(
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				);

				$filter_hook = sprintf( 'nosfir_%s_widget_tags', $sidebar );
				$widget_tags = apply_filters( $filter_hook, $widget_tags );

				if ( is_array( $widget_tags ) ) {
					register_sidebar( $args + $widget_tags );
				}
			}
		}

		/**
		 * Enqueue scripts and styles
		 */
		public function scripts() {
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Styles
			wp_enqueue_style( 'nosfir-style', get_stylesheet_uri(), array(), $this->version );
			wp_style_add_data( 'nosfir-style', 'rtl', 'replace' );

			// Icons
			wp_enqueue_style( 'nosfir-icons', get_template_directory_uri() . '/assets/css/icons' . $suffix . '.css', array(), $this->version );

			// Main styles
			wp_enqueue_style( 'nosfir-main', get_template_directory_uri() . '/assets/css/main' . $suffix . '.css', array(), $this->version );

			// Fonts
			wp_enqueue_style( 'nosfir-fonts', $this->google_fonts(), array(), null );

			// Scripts
			wp_enqueue_script( 'nosfir-navigation', get_template_directory_uri() . '/assets/js/navigation' . $suffix . '.js', array(), $this->version, true );
			wp_enqueue_script( 'nosfir-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix' . $suffix . '.js', array(), $this->version, true );
			wp_enqueue_script( 'nosfir-main', get_template_directory_uri() . '/assets/js/main' . $suffix . '.js', array( 'jquery' ), $this->version, true );

			// Localize scripts
			wp_localize_script(
				'nosfir-main',
				'nosfir_params',
				array(
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'nosfir-nonce' ),
					'is_mobile'   => wp_is_mobile(),
					'is_rtl'      => is_rtl(),
					'strings'     => array(
						'loading'     => __( 'Loading...', 'nosfir' ),
						'load_more'   => __( 'Load More', 'nosfir' ),
						'no_more'     => __( 'No more posts', 'nosfir' ),
						'error'       => __( 'Something went wrong. Please try again.', 'nosfir' ),
					),
				)
			);

			// Comment reply script
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			// Homepage specific scripts
			if ( is_page_template( 'template-homepage.php' ) ) {
				wp_enqueue_script( 'nosfir-homepage', get_template_directory_uri() . '/assets/js/homepage' . $suffix . '.js', array( 'jquery' ), $this->version, true );
			}

			// Add inline styles for customizer settings
			$this->inline_styles();
		}

		/**
		 * Register Google fonts
		 */
		public function google_fonts() {
			$google_fonts = apply_filters(
				'nosfir_google_font_families',
				array(
					'inter' => 'Inter:wght@300;400;500;600;700;800;900',
					'roboto' => 'Roboto:wght@300;400;500;700;900',
				)
			);

			if ( empty( $google_fonts ) ) {
				return false;
			}

			$query_args = array(
				'family'  => implode( '&family=', $google_fonts ),
				'subset'  => rawurlencode( 'latin,latin-ext' ),
				'display' => 'swap',
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css2' );

			return esc_url_raw( $fonts_url );
		}

		/**
		 * Enqueue block assets
		 */
		public function block_assets() {
			wp_enqueue_style( 
				'nosfir-block-styles', 
				get_template_directory_uri() . '/assets/css/blocks.css', 
				array(), 
				$this->version 
			);
		}

		/**
		 * Enqueue child theme stylesheet
		 */
		public function child_scripts() {
			if ( is_child_theme() ) {
				$child_theme = wp_get_theme( get_stylesheet() );
				wp_enqueue_style( 
					'nosfir-child-style', 
					get_stylesheet_uri(), 
					array( 'nosfir-style' ), 
					$child_theme->get( 'Version' ) 
				);
			}
		}

		/**
		 * Enqueue admin scripts
		 */
		public function admin_scripts() {
			wp_enqueue_style( 
				'nosfir-admin', 
				get_template_directory_uri() . '/assets/css/admin.css', 
				array(), 
				$this->version 
			);

			wp_enqueue_script( 
				'nosfir-admin', 
				get_template_directory_uri() . '/assets/js/admin.js', 
				array( 'jquery' ), 
				$this->version, 
				true 
			);
		}

		/**
		 * Add inline styles
		 */
		private function inline_styles() {
			$primary_color = get_theme_mod( 'nosfir_primary_color', '#2c3e50' );
			$secondary_color = get_theme_mod( 'nosfir_secondary_color', '#e74c3c' );
			$text_color = get_theme_mod( 'nosfir_text_color', '#333333' );
			$link_color = get_theme_mod( 'nosfir_link_color', '#2c3e50' );

			$custom_css = "
				:root {
					--nosfir-primary: {$primary_color};
					--nosfir-secondary: {$secondary_color};
					--nosfir-text: {$text_color};
					--nosfir-link: {$link_color};
				}
			";

			wp_add_inline_style( 'nosfir-main', $custom_css );
		}

		/**
		 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link
		 */
		public function page_menu_args( $args ) {
			$args['show_home'] = true;
			return $args;
		}

		/**
		 * Adds custom classes to the array of body classes
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
			if ( is_active_sidebar( 'sidebar-1' ) ) {
				$classes[] = 'has-sidebar';
			} else {
				$classes[] = 'no-sidebar';
			}

			// Add class for header style
			$header_style = get_theme_mod( 'nosfir_header_style', 'default' );
			$classes[] = 'header-' . $header_style;

			// Add class for footer style
			$footer_style = get_theme_mod( 'nosfir_footer_style', 'default' );
			$classes[] = 'footer-' . $footer_style;

			// Add class for layout
			$layout = get_theme_mod( 'nosfir_layout', 'wide' );
			$classes[] = 'layout-' . $layout;

			// Add class when using homepage template + featured image
			if ( is_page_template( 'template-homepage.php' ) && has_post_thumbnail() ) {
				$classes[] = 'has-post-thumbnail';
			}

			// Add class if is mobile
			if ( wp_is_mobile() ) {
				$classes[] = 'is-mobile';
			}

			// Add dark mode class if enabled
			if ( get_theme_mod( 'nosfir_dark_mode', false ) ) {
				$classes[] = 'dark-mode';
			}

			// Add custom class
			$custom_class = get_theme_mod( 'nosfir_body_class', '' );
			if ( ! empty( $custom_class ) ) {
				$classes[] = sanitize_html_class( $custom_class );
			}

			return $classes;
		}

		/**
		 * Custom navigation markup template
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
		 */
		public function nav_menu_css_class( $classes, $item ) {
			if ( in_array( 'current-menu-item', $classes ) ) {
				$classes[] = 'active';
			}
			return $classes;
		}

		/**
		 * Remove navigation menu item IDs
		 */
		public function nav_menu_item_id( $id, $item ) {
			return '';
		}

		/**
		 * Init hook
		 */
		public function init() {
			// Register custom post types
			do_action( 'nosfir_register_post_types' );

			// Register custom taxonomies
			do_action( 'nosfir_register_taxonomies' );
		}

		/**
		 * WP Head hook
		 */
		public function wp_head() {
			// Add meta tags
			?>
			<meta name="theme-color" content="<?php echo esc_attr( get_theme_mod( 'nosfir_primary_color', '#2c3e50' ) ); ?>">
			<meta name="msapplication-TileColor" content="<?php echo esc_attr( get_theme_mod( 'nosfir_primary_color', '#2c3e50' ) ); ?>">
			<?php
			
			// Add custom CSS
			$custom_css = get_theme_mod( 'nosfir_custom_css', '' );
			if ( ! empty( $custom_css ) ) {
				?>
				<style type="text/css">
					<?php echo wp_strip_all_tags( $custom_css ); ?>
				</style>
				<?php
			}

			// Add custom head code
			$head_code = get_theme_mod( 'nosfir_head_code', '' );
			if ( ! empty( $head_code ) ) {
				echo $head_code;
			}
		}

		/**
		 * WP Footer hook
		 */
		public function wp_footer() {
			// Add custom footer code
			$footer_code = get_theme_mod( 'nosfir_footer_code', '' );
			if ( ! empty( $footer_code ) ) {
				echo $footer_code;
			}

			// Add back to top button
			if ( get_theme_mod( 'nosfir_back_to_top', true ) ) {
				?>
				<button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
						<path d="M10 3l7 7-1.41 1.41L10 5.83 4.41 11.41 3 10l7-7z"/>
					</svg>
				</button>
				<?php
			}
		}

		/**
		 * Load more posts AJAX handler
		 */
		public function load_more_posts() {
			check_ajax_referer( 'nosfir-nonce', 'nonce' );

			$paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
			$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';

			$args = array(
				'post_type'      => $post_type,
				'posts_per_page' => get_option( 'posts_per_page' ),
				'paged'          => $paged,
				'post_status'    => 'publish',
			);

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					get_template_part( 'template-parts/content', get_post_format() );
				}
				wp_die();
			} else {
				wp_die( 'no_more' );
			}
		}

		/**
		 * Check for theme updates
		 */
		public function check_for_update( $transient ) {
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			// Check for updates from your server
			// This is a placeholder for actual update checking logic
			
			return $transient;
		}

		/**
		 * Add styles for embeds
		 */
		public function print_embed_styles() {
			$primary_color = get_theme_mod( 'nosfir_primary_color', '#2c3e50' );
			$text_color = get_theme_mod( 'nosfir_text_color', '#333333' );
			?>
			<style type="text/css">
				.wp-embed {
					padding: 2em;
					border: 1px solid #e5e5e5;
					border-radius: 4px;
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
					color: <?php echo esc_attr( $text_color ); ?>;
				}
				.wp-embed-featured-image {
					margin-bottom: 1.5em;
				}
				.wp-embed-featured-image img {
					width: 100%;
					height: auto;
				}
				.wp-embed-heading a {
					color: <?php echo esc_attr( $primary_color ); ?>;
					text-decoration: none;
				}
				.wp-embed-heading a:hover {
					text-decoration: underline;
				}
			</style>
			<?php
		}
	}

endif;

// Initialize the theme
return Nosfir::instance();