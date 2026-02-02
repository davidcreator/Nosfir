<?php
/**
 * WordPress shims.
 * 
 * Provides backwards compatibility for newer WordPress functions
 * to ensure the theme works with older WordPress versions.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * wp_body_open()
 * Adds backwards compatibility for wp_body_open() introduced with WordPress 5.2
 *
 * @since WordPress 5.2.0
 */
if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
	 *
	 * @since 1.0.0
	 * @see https://developer.wordpress.org/reference/functions/wp_body_open/
	 * @return void
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening body tag.
		 */
		do_action( 'wp_body_open' );
	}
}

/**
 * wp_get_environment_type()
 * Adds backwards compatibility for wp_get_environment_type() introduced with WordPress 5.5
 *
 * @since WordPress 5.5.0
 */
if ( ! function_exists( 'wp_get_environment_type' ) ) {
	/**
	 * Retrieves the current environment type.
	 *
	 * @since 1.0.0
	 * @see https://developer.wordpress.org/reference/functions/wp_get_environment_type/
	 * @return string The current environment type. Possible values include 'local', 'development', 'staging', 'production'.
	 */
	function wp_get_environment_type() {
		return 'production';
	}
}

/**
 * wp_is_mobile()
 * Adds backwards compatibility for older WordPress versions
 *
 * @since WordPress 3.4.0
 */
if ( ! function_exists( 'wp_is_mobile' ) ) {
	/**
	 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	function wp_is_mobile() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
			|| strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return apply_filters( 'wp_is_mobile', $is_mobile );
	}
}

/**
 * wp_lazy_loading_enabled()
 * Adds backwards compatibility for wp_lazy_loading_enabled() introduced with WordPress 5.5
 *
 * @since WordPress 5.5.0
 */
if ( ! function_exists( 'wp_lazy_loading_enabled' ) ) {
	/**
	 * Determines whether to add the `loading` attribute to the specified tag in the specified context.
	 *
	 * @since 1.0.0
	 * @param string $tag_name The tag name.
	 * @param string $context  The context.
	 * @return bool
	 */
	function wp_lazy_loading_enabled( $tag_name, $context ) {
		return false;
	}
}

/**
 * wp_unique_id()
 * Adds backwards compatibility for wp_unique_id() introduced with WordPress 5.0.3
 *
 * @since WordPress 5.0.3
 */
if ( ! function_exists( 'wp_unique_id' ) ) {
	/**
	 * Gets unique ID.
	 *
	 * @since 1.0.0
	 * @param string $prefix Prefix for the returned ID.
	 * @return string Unique ID.
	 */
	function wp_unique_id( $prefix = '' ) {
		static $id_counter = 0;
		return $prefix . (string) ++$id_counter;
	}
}

/**
 * wp_get_wp_version()
 * Get WordPress version
 *
 * @since WordPress 3.4.0
 */
if ( ! function_exists( 'wp_get_wp_version' ) ) {
	/**
	 * Get the WordPress version
	 *
	 * @since 1.0.0
	 * @return string WordPress version
	 */
	function wp_get_wp_version() {
		global $wp_version;
		return $wp_version;
	}
}

/**
 * get_parent_theme_file_path()
 * Adds backwards compatibility for get_parent_theme_file_path() introduced with WordPress 4.7
 *
 * @since WordPress 4.7.0
 */
if ( ! function_exists( 'get_parent_theme_file_path' ) ) {
	/**
	 * Retrieves the path of a file in the parent theme.
	 *
	 * @since 1.0.0
	 * @param string $file Optional. File to return the path for in the template directory.
	 * @return string The path of the file.
	 */
	function get_parent_theme_file_path( $file = '' ) {
		$file = ltrim( $file, '/' );
		
		if ( empty( $file ) ) {
			$path = get_template_directory();
		} else {
			$path = get_template_directory() . '/' . $file;
		}
		
		return apply_filters( 'parent_theme_file_path', $path, $file );
	}
}

/**
 * get_parent_theme_file_uri()
 * Adds backwards compatibility for get_parent_theme_file_uri() introduced with WordPress 4.7
 *
 * @since WordPress 4.7.0
 */
if ( ! function_exists( 'get_parent_theme_file_uri' ) ) {
	/**
	 * Retrieves the URL of a file in the parent theme.
	 *
	 * @since 1.0.0
	 * @param string $file Optional. File to return the URL for in the template directory.
	 * @return string The URL of the file.
	 */
	function get_parent_theme_file_uri( $file = '' ) {
		$file = ltrim( $file, '/' );
		
		if ( empty( $file ) ) {
			$url = get_template_directory_uri();
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}
		
		return apply_filters( 'parent_theme_file_uri', $url, $file );
	}
}

/**
 * wp_doing_ajax()
 * Adds backwards compatibility for wp_doing_ajax() introduced with WordPress 4.7
 *
 * @since WordPress 4.7.0
 */
if ( ! function_exists( 'wp_doing_ajax' ) ) {
	/**
	 * Determines whether the current request is a WordPress Ajax request.
	 *
	 * @since 1.0.0
	 * @return bool True if it's a WordPress Ajax request, false otherwise.
	 */
	function wp_doing_ajax() {
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}

/**
 * wp_doing_cron()
 * Adds backwards compatibility for wp_doing_cron() introduced with WordPress 4.8
 *
 * @since WordPress 4.8.0
 */
if ( ! function_exists( 'wp_doing_cron' ) ) {
	/**
	 * Determines whether the current request is a WordPress cron request.
	 *
	 * @since 1.0.0
	 * @return bool True if it's a WordPress cron request, false otherwise.
	 */
	function wp_doing_cron() {
		return apply_filters( 'wp_doing_cron', defined( 'DOING_CRON' ) && DOING_CRON );
	}
}

/**
 * wp_parse_url()
 * Adds backwards compatibility for wp_parse_url() introduced with WordPress 4.4
 *
 * @since WordPress 4.4.0
 */
if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * A wrapper for PHP's parse_url() function that handles consistency in the return values.
	 *
	 * @since 1.0.0
	 * @param string $url       The URL to parse.
	 * @param int    $component The specific component to retrieve. Use one of the PHP predefined constants.
	 * @return mixed False on failure; Array of URL components on success; String when a specific component is requested.
	 */
	function wp_parse_url( $url, $component = -1 ) {
		$parts = parse_url( $url, $component );
		
		if ( -1 === $component ) {
			// For PHP 5.4.7+ parse_url() may return FALSE
			if ( false === $parts ) {
				return $parts;
			}
			
			// Ensure each component is set
			$parts = wp_parse_args( $parts, array(
				'scheme'   => null,
				'host'     => null,
				'port'     => null,
				'user'     => null,
				'pass'     => null,
				'path'     => null,
				'query'    => null,
				'fragment' => null,
			) );
		}
		
		return $parts;
	}
}

/**
 * wp_site_icon()
 * Adds backwards compatibility for wp_site_icon() introduced with WordPress 4.3
 *
 * @since WordPress 4.3.0
 */
if ( ! function_exists( 'wp_site_icon' ) ) {
	/**
	 * Display site icon meta tags.
	 *
	 * @since 1.0.0
	 */
	function wp_site_icon() {
		if ( ! has_site_icon() && ! is_customize_preview() ) {
			return;
		}
		
		$meta_tags = array();
		$icon_32   = get_site_icon_url( 32 );
		
		if ( $icon_32 ) {
			$meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( $icon_32 ) );
		}
		
		$icon_192 = get_site_icon_url( 192 );
		
		if ( $icon_192 ) {
			$meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( $icon_192 ) );
		}
		
		$icon_180 = get_site_icon_url( 180 );
		
		if ( $icon_180 ) {
			$meta_tags[] = sprintf( '<link rel="apple-touch-icon-precomposed" href="%s" />', esc_url( $icon_180 ) );
		}
		
		$icon_270 = get_site_icon_url( 270 );
		
		if ( $icon_270 ) {
			$meta_tags[] = sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( $icon_270 ) );
		}
		
		$meta_tags = apply_filters( 'site_icon_meta_tags', $meta_tags );
		$meta_tags = array_filter( $meta_tags );
		
		foreach ( $meta_tags as $meta_tag ) {
			echo "$meta_tag\n";
		}
	}
}

/**
 * the_privacy_policy_link()
 * Adds backwards compatibility for the_privacy_policy_link() introduced with WordPress 4.9.6
 *
 * @since WordPress 4.9.6
 */
if ( ! function_exists( 'the_privacy_policy_link' ) ) {
	/**
	 * Displays the privacy policy link with formatting.
	 *
	 * @since 1.0.0
	 * @param string $before Optional. Display before privacy policy link. Default empty.
	 * @param string $after  Optional. Display after privacy policy link. Default empty.
	 */
	function the_privacy_policy_link( $before = '', $after = '' ) {
		echo get_the_privacy_policy_link( $before, $after );
	}
}

/**
 * get_the_privacy_policy_link()
 * Adds backwards compatibility for get_the_privacy_policy_link() introduced with WordPress 4.9.6
 *
 * @since WordPress 4.9.6
 */
if ( ! function_exists( 'get_the_privacy_policy_link' ) ) {
	/**
	 * Returns the privacy policy link with formatting.
	 *
	 * @since 1.0.0
	 * @param string $before Optional. Display before privacy policy link. Default empty.
	 * @param string $after  Optional. Display after privacy policy link. Default empty.
	 * @return string Markup for the link and surrounding elements. Empty string if no privacy policy is set.
	 */
	function get_the_privacy_policy_link( $before = '', $after = '' ) {
		$link = '';
		
		$privacy_policy_url = get_privacy_policy_url();
		
		if ( $privacy_policy_url ) {
			$link = sprintf(
				'<a class="privacy-policy-link" href="%s">%s</a>',
				esc_url( $privacy_policy_url ),
				__( 'Privacy Policy', 'nosfir' )
			);
		}
		
		if ( $link ) {
			return $before . $link . $after;
		}
		
		return '';
	}
}

/**
 * get_privacy_policy_url()
 * Adds backwards compatibility for get_privacy_policy_url() introduced with WordPress 4.9.6
 *
 * @since WordPress 4.9.6
 */
if ( ! function_exists( 'get_privacy_policy_url' ) ) {
	/**
	 * Retrieves the URL to the privacy policy page.
	 *
	 * @since 1.0.0
	 * @return string The URL to the privacy policy page. Empty string if not set.
	 */
	function get_privacy_policy_url() {
		$privacy_policy_page_id = get_option( 'wp_page_for_privacy_policy' );
		$url = '';
		
		if ( ! empty( $privacy_policy_page_id ) && get_post_status( $privacy_policy_page_id ) === 'publish' ) {
			$url = get_permalink( $privacy_policy_page_id );
		}
		
		return apply_filters( 'privacy_policy_url', $url, $privacy_policy_page_id );
	}
}

/**
 * wp_sanitize_script_attributes()
 * Adds backwards compatibility for wp_sanitize_script_attributes() introduced with WordPress 5.7
 *
 * @since WordPress 5.7.0
 */
if ( ! function_exists( 'wp_sanitize_script_attributes' ) ) {
	/**
	 * Sanitizes an attributes array into an attributes string to be placed inside a `<script>` tag.
	 *
	 * @since 1.0.0
	 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
	 * @return string String made of sanitized `<script>` tag attributes.
	 */
	function wp_sanitize_script_attributes( $attributes ) {
		$html5_script_support = ! is_admin() && ! current_theme_supports( 'html5', 'script' );
		$attributes_string = '';
		
		// If HTML5 script tag is supported, only the `src` attribute is allowed.
		$attributes = $html5_script_support ? array( 'src' => '' ) : $attributes;
		
		foreach ( $attributes as $attribute_name => $attribute_value ) {
			if ( is_bool( $attribute_value ) ) {
				if ( $attribute_value ) {
					$attributes_string .= $attribute_name . ' ';
				}
			} else {
				$attributes_string .= sprintf( '%s="%s" ', esc_attr( $attribute_name ), esc_attr( $attribute_value ) );
			}
		}
		
		return $attributes_string;
	}
}

/**
 * wp_get_attachment_image_url()
 * Adds backwards compatibility for wp_get_attachment_image_url() introduced with WordPress 4.4
 *
 * @since WordPress 4.4.0
 */
if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	/**
	 * Get the URL of an image attachment.
	 *
	 * @since 1.0.0
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|array $size          Optional. Image size. Default 'thumbnail'.
	 * @param bool         $icon          Optional. Whether the image should be treated as an icon. Default false.
	 * @return string|false Attachment URL or false if no image is available.
	 */
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		return isset( $image[0] ) ? $image[0] : false;
	}
}

/**
 * wp_robots()
 * Adds backwards compatibility for wp_robots() introduced with WordPress 5.7
 *
 * @since WordPress 5.7.0
 */
if ( ! function_exists( 'wp_robots' ) ) {
	/**
	 * Displays the robots meta tag as necessary.
	 *
	 * @since 1.0.0
	 */
	function wp_robots() {
		$robots = array();
		
		if ( is_search() ) {
			$robots['noindex'] = true;
			$robots['nofollow'] = true;
		}
		
		if ( is_404() ) {
			$robots['noindex'] = true;
		}
		
		if ( is_attachment() ) {
			$robots['noindex'] = true;
		}
		
		/**
		 * Filters the directives to be included in the 'robots' meta tag.
		 */
		$robots = apply_filters( 'wp_robots', $robots );
		
		if ( empty( $robots ) ) {
			return;
		}
		
		$robots_strings = array();
		foreach ( $robots as $directive => $value ) {
			if ( is_string( $value ) ) {
				$robots_strings[] = "{$directive}:{$value}";
			} elseif ( $value ) {
				$robots_strings[] = $directive;
			}
		}
		
		if ( ! empty( $robots_strings ) ) {
			echo '<meta name="robots" content="' . esc_attr( implode( ', ', $robots_strings ) ) . '" />' . "\n";
		}
	}
}

/**
 * wp_is_tablet()
 * Custom function to detect tablet devices
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wp_is_tablet' ) ) {
	/**
	 * Test if the current browser runs on a tablet device
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	function wp_is_tablet() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$tablet_pattern = '/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i';
		
		return (bool) preg_match( $tablet_pattern, $user_agent );
	}
}

/**
 * array_key_first()
 * Adds backwards compatibility for array_key_first() introduced with PHP 7.3
 *
 * @since PHP 7.3.0
 */
if ( ! function_exists( 'array_key_first' ) ) {
	/**
	 * Gets the first key of an array
	 *
	 * @since 1.0.0
	 * @param array $array
	 * @return string|int|null
	 */
	function array_key_first( array $array ) {
		foreach ( $array as $key => $value ) {
			return $key;
		}
		return null;
	}
}

/**
 * array_key_last()
 * Adds backwards compatibility for array_key_last() introduced with PHP 7.3
 *
 * @since PHP 7.3.0
 */
if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Gets the last key of an array
	 *
	 * @since 1.0.0
	 * @param array $array
	 * @return string|int|null
	 */
	function array_key_last( array $array ) {
		if ( empty( $array ) ) {
			return null;
		}
		return array_keys( $array )[ count( $array ) - 1 ];
	}
}

/**
 * str_contains()
 * Adds backwards compatibility for str_contains() introduced with PHP 8.0
 *
 * @since PHP 8.0.0
 */
if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * Determine if a string contains a given substring
	 *
	 * @since 1.0.0
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	function str_contains( $haystack, $needle ) {
		return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
	}
}

/**
 * str_starts_with()
 * Adds backwards compatibility for str_starts_with() introduced with PHP 8.0
 *
 * @since PHP 8.0.0
 */
if ( ! function_exists( 'str_starts_with' ) ) {
	/**
	 * Checks if a string starts with a given substring
	 *
	 * @since 1.0.0
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	function str_starts_with( $haystack, $needle ) {
		return ( string ) $needle !== '' && strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
	}
}

/**
 * str_ends_with()
 * Adds backwards compatibility for str_ends_with() introduced with PHP 8.0
 *
 * @since PHP 8.0.0
 */
if ( ! function_exists( 'str_ends_with' ) ) {
	/**
	 * Checks if a string ends with a given substring
	 *
	 * @since 1.0.0
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	function str_ends_with( $haystack, $needle ) {
		return $needle !== '' && substr( $haystack, -strlen( $needle ) ) === (string) $needle;
	}
}