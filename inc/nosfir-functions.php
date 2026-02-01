<?php
/**
 * Nosfir functions.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'nosfir_is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 *
	 * @return boolean
	 */
	function nosfir_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' ) ? true : false;
	}
}

if ( ! function_exists( 'nosfir_is_jetpack_activated' ) ) {
	/**
	 * Query Jetpack activation
	 *
	 * @return boolean
	 */
	function nosfir_is_jetpack_activated() {
		return class_exists( 'Jetpack' ) ? true : false;
	}
}

if ( ! function_exists( 'nosfir_is_elementor_activated' ) ) {
	/**
	 * Query Elementor activation
	 *
	 * @return boolean
	 */
	function nosfir_is_elementor_activated() {
		return class_exists( '\Elementor\Plugin' ) ? true : false;
	}
}

if ( ! function_exists( 'nosfir_is_gutenberg_activated' ) ) {
	/**
	 * Check if Gutenberg is active
	 *
	 * @return boolean
	 */
	function nosfir_is_gutenberg_activated() {
		return function_exists( 'register_block_type' );
	}
}

if ( ! function_exists( 'nosfir_do_shortcode' ) ) {
	/**
	 * Call a shortcode function by tag name.
	 *
	 * @since  1.0.0
	 * @param string $tag     The shortcode whose function to call.
	 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
	 * @param array  $content The shortcode's content. Default is null (none).
	 * @return string|bool False on failure, the result of the shortcode on success.
	 */
	function nosfir_do_shortcode( $tag, array $atts = array(), $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}
}

if ( ! function_exists( 'nosfir_get_content_background_color' ) ) {
	/**
	 * Get the content background color
	 *
	 * @since  1.0.0
	 * @return string the background color
	 */
	function nosfir_get_content_background_color() {
		$content_bg_color = get_theme_mod( 'nosfir_content_background_color', '#ffffff' );
		
		// Check for boxed layout
		$layout = get_theme_mod( 'nosfir_layout', 'wide' );
		
		if ( 'boxed' === $layout ) {
			$boxed_bg_color = get_theme_mod( 'nosfir_boxed_background_color', '#f5f5f5' );
			if ( $boxed_bg_color ) {
				return $boxed_bg_color;
			}
		}
		
		// Fallback to main background color
		if ( ! $content_bg_color ) {
			$content_bg_color = '#' . get_background_color();
		}
		
		return $content_bg_color;
	}
}

if ( ! function_exists( 'nosfir_header_styles' ) ) {
	/**
	 * Apply inline style to the Nosfir header.
	 *
	 * @uses  get_header_image()
	 * @since  1.0.0
	 */
	function nosfir_header_styles() {
		$styles = array();
		
		// Header background image
		$header_image = get_header_image();
		if ( $header_image ) {
			$styles['background-image'] = 'url(' . esc_url( $header_image ) . ')';
			$styles['background-size'] = get_theme_mod( 'nosfir_header_background_size', 'cover' );
			$styles['background-position'] = get_theme_mod( 'nosfir_header_background_position', 'center center' );
			$styles['background-repeat'] = get_theme_mod( 'nosfir_header_background_repeat', 'no-repeat' );
		}
		
		// Header background color
		$header_bg_color = get_theme_mod( 'nosfir_header_background_color' );
		if ( $header_bg_color ) {
			$styles['background-color'] = $header_bg_color;
		}
		
		// Header text color
		$header_text_color = get_header_textcolor();
		if ( 'blank' !== $header_text_color ) {
			$styles['color'] = '#' . $header_text_color;
		}
		
		// Header padding
		$header_padding = get_theme_mod( 'nosfir_header_padding' );
		if ( $header_padding ) {
			$styles['padding'] = $header_padding . 'px 0';
		}
		
		$styles = apply_filters( 'nosfir_header_styles', $styles );
		
		$css = '';
		foreach ( $styles as $style => $value ) {
			$css .= esc_attr( $style . ': ' . $value . '; ' );
		}
		
		return $css;
	}
}

if ( ! function_exists( 'nosfir_homepage_content_styles' ) ) {
	/**
	 * Apply inline style to the Nosfir homepage content.
	 *
	 * @uses  get_the_post_thumbnail_url()
	 * @since  1.0.0
	 */
	function nosfir_homepage_content_styles() {
		$styles = array();
		
		// Featured image as background
		if ( is_page_template( 'template-homepage.php' ) ) {
			$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
			
			if ( $featured_image ) {
				$styles['background-image'] = 'url(' . esc_url( $featured_image ) . ')';
				$styles['background-size'] = 'cover';
				$styles['background-position'] = 'center center';
				$styles['background-attachment'] = get_theme_mod( 'nosfir_homepage_parallax', false ) ? 'fixed' : 'scroll';
			}
		}
		
		$styles = apply_filters( 'nosfir_homepage_content_styles', $styles );
		
		$css = '';
		foreach ( $styles as $style => $value ) {
			$css .= esc_attr( $style . ': ' . $value . '; ' );
		}
		
		return $css;
	}
}

if ( ! function_exists( 'nosfir_get_rgb_values_from_hex' ) ) {
	/**
	 * Given an hex colors, returns an array with the colors components.
	 *
	 * @param  string $hex Hex color e.g. #111111.
	 * @return array       Array with color components (r, g, b).
	 * @since  1.0.0
	 */
	function nosfir_get_rgb_values_from_hex( $hex ) {
		// Format the hex color string
		$hex = str_replace( '#', '', $hex );
		
		if ( 3 === strlen( $hex ) ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . 
			       str_repeat( substr( $hex, 1, 1 ), 2 ) . 
			       str_repeat( substr( $hex, 2, 1 ), 2 );
		}
		
		// Get decimal values
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		
		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b,
		);
	}
}

if ( ! function_exists( 'nosfir_hex_to_rgba' ) ) {
	/**
	 * Convert hex color to rgba
	 *
	 * @param  string $hex     Hex color.
	 * @param  float  $opacity Opacity value.
	 * @return string          RGBA color.
	 * @since  1.0.0
	 */
	function nosfir_hex_to_rgba( $hex, $opacity = 1 ) {
		$rgb = nosfir_get_rgb_values_from_hex( $hex );
		return sprintf( 'rgba(%d, %d, %d, %s)', $rgb['r'], $rgb['g'], $rgb['b'], $opacity );
	}
}

if ( ! function_exists( 'nosfir_is_color_light' ) ) {
	/**
	 * Returns true for light colors and false for dark colors.
	 *
	 * @param  string $hex Hex color e.g. #111111.
	 * @return bool        True if the average lightness of the three components is >= 127.5.
	 * @since  1.0.0
	 */
	function nosfir_is_color_light( $hex ) {
		$rgb_values        = nosfir_get_rgb_values_from_hex( $hex );
		$average_lightness = ( $rgb_values['r'] + $rgb_values['g'] + $rgb_values['b'] ) / 3;
		return $average_lightness >= 127.5;
	}
}

if ( ! function_exists( 'nosfir_get_color_brightness' ) ) {
	/**
	 * Calculate color brightness
	 *
	 * @param  string $hex Hex color.
	 * @return float       Brightness value.
	 * @since  1.0.0
	 */
	function nosfir_get_color_brightness( $hex ) {
		$rgb = nosfir_get_rgb_values_from_hex( $hex );
		return ( ( $rgb['r'] * 299 ) + ( $rgb['g'] * 587 ) + ( $rgb['b'] * 114 ) ) / 1000;
	}
}

if ( ! function_exists( 'nosfir_adjust_color_brightness' ) ) {
	/**
	 * Adjust a hex color brightness
	 *
	 * @param  string  $hex     Hex color e.g. #111111.
	 * @param  integer $steps   Factor by which to brighten/darken (-255 to 255).
	 * @param  float   $opacity Opacity factor between 0 and 1.
	 * @return string           Brightened/darkened color.
	 * @since  1.0.0
	 */
	function nosfir_adjust_color_brightness( $hex, $steps, $opacity = 1 ) {
		// Steps should be between -255 and 255
		$steps = max( -255, min( 255, $steps ) );
		
		$rgb_values = nosfir_get_rgb_values_from_hex( $hex );
		
		// Adjust number of steps and keep it inside 0 to 255
		$r = max( 0, min( 255, $rgb_values['r'] + $steps ) );
		$g = max( 0, min( 255, $rgb_values['g'] + $steps ) );
		$b = max( 0, min( 255, $rgb_values['b'] + $steps ) );
		
		if ( $opacity >= 0 && $opacity < 1 ) {
			return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
		}
		
		$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
		$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
		$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
		
		return '#' . $r_hex . $g_hex . $b_hex;
	}
}

if ( ! function_exists( 'nosfir_get_contrast_color' ) ) {
	/**
	 * Get contrast color (black or white) based on background
	 *
	 * @param  string $hex Background color.
	 * @return string      #000000 or #ffffff.
	 * @since  1.0.0
	 */
	function nosfir_get_contrast_color( $hex ) {
		return nosfir_is_color_light( $hex ) ? '#000000' : '#ffffff';
	}
}

if ( ! function_exists( 'nosfir_sanitize_choices' ) ) {
	/**
	 * Sanitizes choices (selects / radios)
	 *
	 * @param array $input   The available choices.
	 * @param array $setting The setting object.
	 * @return string
	 * @since  1.0.0
	 */
	function nosfir_sanitize_choices( $input, $setting ) {
		// Ensure input is a slug
		$input = sanitize_key( $input );
		
		// Get list of choices from the control
		$choices = $setting->manager->get_control( $setting->id )->choices;
		
		// If the input is a valid key, return it; otherwise, return the default
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
}

if ( ! function_exists( 'nosfir_sanitize_checkbox' ) ) {
	/**
	 * Checkbox sanitization callback.
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_checkbox( $checked ) {
		return ( ( isset( $checked ) && true === $checked ) ? true : false );
	}
}

if ( ! function_exists( 'nosfir_sanitize_hex_color' ) ) {
	/**
	 * Sanitize hex color
	 *
	 * @param  string $color Hex color.
	 * @return string        Sanitized hex color.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}
		
		// 3 or 6 hex digits, or the empty string
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
		
		return '';
	}
}

if ( ! function_exists( 'nosfir_sanitize_number' ) ) {
	/**
	 * Sanitize number
	 *
	 * @param  int $number Number.
	 * @return int         Sanitized number.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_number( $number ) {
		return absint( $number );
	}
}

if ( ! function_exists( 'nosfir_sanitize_select' ) ) {
	/**
	 * Sanitize select
	 *
	 * @param  string $input   Input value.
	 * @param  object $setting Setting object.
	 * @return string          Sanitized value.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_select( $input, $setting ) {
		$input = sanitize_key( $input );
		$choices = $setting->manager->get_control( $setting->id )->choices;
		
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
}

if ( ! function_exists( 'nosfir_sanitize_textarea' ) ) {
	/**
	 * Sanitize textarea
	 *
	 * @param  string $input Input value.
	 * @return string        Sanitized value.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_textarea( $input ) {
		return wp_kses_post( $input );
	}
}

if ( ! function_exists( 'nosfir_sanitize_image' ) ) {
	/**
	 * Sanitize image URL
	 *
	 * @param  string $image   Image URL.
	 * @param  object $setting Setting object.
	 * @return string          Sanitized image URL.
	 * @since  1.0.0
	 */
	function nosfir_sanitize_image( $image, $setting ) {
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'tiff|tif'     => 'image/tiff',
			'ico'          => 'image/x-icon',
			'svg'          => 'image/svg+xml',
		);
		
		$file = wp_check_filetype( $image, $mimes );
		
		return ( $file['ext'] ? $image : $setting->default );
	}
}

if ( ! function_exists( 'nosfir_get_post_thumbnail_url' ) ) {
	/**
	 * Get post thumbnail URL
	 *
	 * @param  int    $post_id Post ID.
	 * @param  string $size    Image size.
	 * @return string          Thumbnail URL.
	 * @since  1.0.0
	 */
	function nosfir_get_post_thumbnail_url( $post_id = null, $size = 'full' ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_id = get_post_thumbnail_id( $post_id );
			$thumb_url = wp_get_attachment_image_src( $thumb_id, $size, true );
			return $thumb_url[0];
		}
		
		// Return default image if set
		$default_image = get_theme_mod( 'nosfir_default_featured_image' );
		if ( $default_image ) {
			return $default_image;
		}
		
		return '';
	}
}

if ( ! function_exists( 'nosfir_get_excerpt' ) ) {
	/**
	 * Get custom excerpt
	 *
	 * @param  int    $length  Excerpt length.
	 * @param  string $more    More string.
	 * @param  int    $post_id Post ID.
	 * @return string          Custom excerpt.
	 * @since  1.0.0
	 */
	function nosfir_get_excerpt( $length = 55, $more = '...', $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		$post = get_post( $post_id );
		
		if ( ! $post ) {
			return '';
		}
		
		if ( has_excerpt( $post_id ) ) {
			$excerpt = $post->post_excerpt;
		} else {
			$excerpt = $post->post_content;
			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = wp_strip_all_tags( $excerpt );
		}
		
		$excerpt = wp_trim_words( $excerpt, $length, $more );
		
		return apply_filters( 'nosfir_excerpt', $excerpt, $post_id, $length, $more );
	}
}

if ( ! function_exists( 'nosfir_get_reading_time' ) ) {
	/**
	 * Calculate reading time
	 *
	 * @param  int $post_id Post ID.
	 * @return int          Reading time in minutes.
	 * @since  1.0.0
	 */
	function nosfir_get_reading_time( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		$content = get_post_field( 'post_content', $post_id );
		$word_count = str_word_count( strip_tags( $content ) );
		$reading_time = ceil( $word_count / 200 ); // Average reading speed: 200 words per minute
		
		return apply_filters( 'nosfir_reading_time', $reading_time, $post_id );
	}
}

if ( ! function_exists( 'nosfir_pagination' ) ) {
	/**
	 * Display pagination
	 *
	 * @param  array $args Pagination arguments.
	 * @return void
	 * @since  1.0.0
	 */
	function nosfir_pagination( $args = array() ) {
		$defaults = array(
			'prev_text' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>',
			'next_text' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>',
			'type'      => 'list',
			'mid_size'  => 2,
			'end_size'  => 1,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$pagination = paginate_links( $args );
		
		if ( $pagination ) {
			echo '<nav class="nosfir-pagination" aria-label="' . esc_attr__( 'Pagination', 'nosfir' ) . '">';
			echo wp_kses_post( $pagination );
			echo '</nav>';
		}
	}
}

if ( ! function_exists( 'nosfir_is_blog' ) ) {
	/**
	 * Check if current page is blog
	 *
	 * @return boolean
	 * @since  1.0.0
	 */
	function nosfir_is_blog() {
		return ( is_archive() || is_author() || is_category() || is_home() || is_single() || is_tag() ) && 'post' === get_post_type();
	}
}

if ( ! function_exists( 'nosfir_get_sidebar_position' ) ) {
	/**
	 * Get sidebar position
	 *
	 * @return string Sidebar position.
	 * @since  1.0.0
	 */
	function nosfir_get_sidebar_position() {
		$sidebar_position = 'right';
		
		if ( is_singular() ) {
			$post_sidebar = get_post_meta( get_the_ID(), '_nosfir_sidebar_position', true );
			if ( $post_sidebar && 'default' !== $post_sidebar ) {
				$sidebar_position = $post_sidebar;
			} else {
				$sidebar_position = get_theme_mod( 'nosfir_single_sidebar_position', 'right' );
			}
		} elseif ( nosfir_is_blog() ) {
			$sidebar_position = get_theme_mod( 'nosfir_blog_sidebar_position', 'right' );
		} elseif ( is_page() ) {
			$sidebar_position = get_theme_mod( 'nosfir_page_sidebar_position', 'right' );
		}
		
		return apply_filters( 'nosfir_sidebar_position', $sidebar_position );
	}
}

if ( ! function_exists( 'nosfir_get_layout_class' ) ) {
	/**
	 * Get layout class
	 *
	 * @return string Layout class.
	 * @since  1.0.0
	 */
	function nosfir_get_layout_class() {
		$sidebar_position = nosfir_get_sidebar_position();
		
		if ( 'none' === $sidebar_position || ! is_active_sidebar( 'sidebar-1' ) ) {
			return 'full-width';
		}
		
		return 'sidebar-' . $sidebar_position;
	}
}

if ( ! function_exists( 'nosfir_social_share' ) ) {
	/**
	 * Display social share buttons
	 *
	 * @param  array $args Share arguments.
	 * @return void
	 * @since  1.0.0
	 */
	function nosfir_social_share( $args = array() ) {
		$defaults = array(
			'facebook'  => true,
			'twitter'   => true,
			'linkedin'  => true,
			'pinterest' => true,
			'whatsapp'  => true,
			'email'     => true,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$url   = urlencode( get_permalink() );
		$title = urlencode( get_the_title() );
		$image = urlencode( nosfir_get_post_thumbnail_url() );
		
		?>
		<div class="nosfir-social-share">
			<?php if ( $args['facebook'] ) : ?>
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $url ); ?>" 
				   target="_blank" 
				   rel="noopener noreferrer" 
				   class="share-facebook"
				   aria-label="<?php esc_attr_e( 'Share on Facebook', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
					</svg>
				</a>
			<?php endif; ?>
			
			<?php if ( $args['twitter'] ) : ?>
				<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" 
				   target="_blank" 
				   rel="noopener noreferrer" 
				   class="share-twitter"
				   aria-label="<?php esc_attr_e( 'Share on Twitter', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M23.32 4.64c-.85.38-1.77.64-2.73.76 1-.6 1.73-1.54 2.08-2.68-.92.55-1.93.95-3 1.16-.87-.93-2.1-1.5-3.47-1.5-2.63 0-4.77 2.13-4.77 4.76 0 .37.04.73.12 1.08-3.96-.2-7.47-2.1-9.82-4.98-.4.7-.64 1.52-.64 2.4 0 1.65.84 3.1 2.12 3.96-.78-.03-1.52-.24-2.16-.6v.06c0 2.3 1.64 4.23 3.82 4.67-.4.1-.82.16-1.25.16-.3 0-.6-.03-.9-.08.62 1.9 2.36 3.28 4.44 3.32-1.63 1.28-3.68 2.04-5.9 2.04-.38 0-.76-.02-1.14-.07 2.1 1.35 4.6 2.14 7.28 2.14 8.74 0 13.52-7.24 13.52-13.52 0-.2 0-.4-.02-.6.93-.67 1.74-1.5 2.38-2.46z"/>
					</svg>
				</a>
			<?php endif; ?>
			
			<?php if ( $args['linkedin'] ) : ?>
				<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_attr( $url ); ?>&title=<?php echo esc_attr( $title ); ?>" 
				   target="_blank" 
				   rel="noopener noreferrer" 
				   class="share-linkedin"
				   aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 15h-5c0-6 0-13 0-13h5v1.8c.6-1.1 2.2-2.3 4.5-2.3 3.5 0 5.5 2.3 5.5 6.7V21.5h-5.5V15c0-2-1-3.5-2.5-3.5-1.5 0-2.5 1.5-2.5 3.5v6.5z"/>
					</svg>
				</a>
			<?php endif; ?>
			
			<?php if ( $args['pinterest'] && $image ) : ?>
				<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_attr( $url ); ?>&media=<?php echo esc_attr( $image ); ?>&description=<?php echo esc_attr( $title ); ?>" 
				   target="_blank" 
				   rel="noopener noreferrer" 
				   class="share-pinterest"
				   aria-label="<?php esc_attr_e( 'Share on Pinterest', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M12.14.5C5.86.5 2.7 5 2.7 8.75c0 2.27.86 4.3 2.7 5.05.3.12.57 0 .66-.33l.27-1.06c.1-.32.06-.44-.2-.73-.52-.62-.86-1.44-.86-2.6 0-3.33 2.5-6.32 6.5-6.32 3.55 0 5.5 2.17 5.5 5.07 0 3.8-1.7 7.02-4.2 7.02-1.37 0-2.4-1.14-2.07-2.54.4-1.68 1.16-3.48 1.16-4.7 0-1.07-.58-1.98-1.78-1.98-1.4 0-2.55 1.47-2.55 3.42 0 1.25.42 2.1.42 2.1l-1.7 7.2c-.5 2.13-.08 4.75-.04 5 .02.17.22.2.3.1.14-.18 1.82-2.26 2.4-4.33.16-.58.93-3.63.93-3.63.45.88 1.8 1.65 3.22 1.65 4.25 0 7.13-3.87 7.13-9.05C20.5 4.15 17.18.5 12.14.5z"/>
					</svg>
				</a>
			<?php endif; ?>
			
			<?php if ( $args['whatsapp'] ) : ?>
				<a href="https://wa.me/?text=<?php echo esc_attr( $title . ' ' . $url ); ?>" 
				   target="_blank" 
				   rel="noopener noreferrer" 
				   class="share-whatsapp"
				   aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M17.5 14.3c-.3 0-2.8-1.4-3.2-1.6-.4-.1-.7-.2-1 .2-.3.5-1 1.3-1.3 1.6-.2.3-.5.3-.9.1s-1.8-.7-3.4-2.2c-1.3-1.2-2.1-2.7-2.4-3.1-.2-.5 0-.7.2-.9.2-.2.5-.6.7-.9.2-.3.3-.5.5-.8.1-.3 0-.6 0-.8-.1-.2-1-2.4-1.3-3.3-.4-.8-.7-.7-1-.7h-.8c-.3 0-.8.1-1.2.6-.4.5-1.6 1.5-1.6 3.7s1.6 4.3 1.8 4.6c.2.3 3.2 4.8 7.7 6.8 1 .5 1.9.7 2.5.9 1 .3 2 .3 2.8.2.8-.1 2.6-1 3-2 .3-1 .3-1.9.2-2-.1-.3-.4-.4-.7-.6zm-6.3 8.6h0c-2 0-4-.6-5.6-1.6L.4 22.8l1.5-5.4c-1.2-2-1.8-4.2-1.8-6.5 0-6.7 5.5-12.2 12.2-12.2 3.3 0 6.3 1.3 8.6 3.6 2.3 2.3 3.6 5.3 3.6 8.6C23.5 17.6 18 23 11.2 23z"/>
					</svg>
				</a>
			<?php endif; ?>
			
			<?php if ( $args['email'] ) : ?>
				<a href="mailto:?subject=<?php echo esc_attr( $title ); ?>&body=<?php echo esc_attr( $url ); ?>" 
				   class="share-email"
				   aria-label="<?php esc_attr_e( 'Share via Email', 'nosfir' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
						<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
					</svg>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_get_author_social' ) ) {
	/**
	 * Get author social links
	 *
	 * @param  int $author_id Author ID.
	 * @return array          Social links.
	 * @since  1.0.0
	 */
	function nosfir_get_author_social( $author_id = null ) {
		if ( ! $author_id ) {
			$author_id = get_the_author_meta( 'ID' );
		}
		
		$social_links = array(
			'facebook'  => get_the_author_meta( 'facebook', $author_id ),
			'twitter'   => get_the_author_meta( 'twitter', $author_id ),
			'linkedin'  => get_the_author_meta( 'linkedin', $author_id ),
			'instagram' => get_the_author_meta( 'instagram', $author_id ),
			'youtube'   => get_the_author_meta( 'youtube', $author_id ),
			'github'    => get_the_author_meta( 'github', $author_id ),
		);
		
		return array_filter( $social_links );
	}
}