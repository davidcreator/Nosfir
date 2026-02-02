<?php
/**
 * Nosfir template functions.
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'nosfir_display_comments' ) ) {
	/**
	 * Nosfir display comments
	 *
	 * @since  1.0.0
	 */
	function nosfir_display_comments() {
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	}
}

if ( ! function_exists( 'nosfir_comment' ) ) {
	/**
	 * Nosfir comment template
	 *
	 * @param object $comment Comment object.
	 * @param array  $args    Comment arguments.
	 * @param int    $depth   Comment depth.
	 * @since 1.0.0
	 */
	function nosfir_comment( $comment, $args, $depth ) {
		if ( 'div' === $args['style'] ) {
			$tag       = 'div';
			$add_below = 'comment';
		} else {
			$tag       = 'li';
			$add_below = 'div-comment';
		}
		?>
		<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID(); ?>">
		
		<article class="comment-body">
			<div class="comment-meta">
				<div class="comment-author vcard">
					<?php 
					$avatar_size = 60;
					if ( '0' != $comment->comment_parent ) {
						$avatar_size = 40;
					}
					echo get_avatar( $comment, $avatar_size ); 
					?>
					
					<div class="comment-metadata">
						<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?>
						
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="comment-date">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php
								printf(
									/* translators: 1: date, 2: time */
									esc_html__( '%1$s at %2$s', 'nosfir' ),
									get_comment_date(),
									get_comment_time()
								);
								?>
							</time>
						</a>
						
						<?php if ( '0' == $comment->comment_approved ) : ?>
							<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'nosfir' ); ?></em>
						<?php endif; ?>
					</div>
				</div>
			</div>
			
			<?php if ( 'div' != $args['style'] ) : ?>
			<div id="div-comment-<?php comment_ID(); ?>" class="comment-content">
			<?php endif; ?>
				
				<div class="comment-text">
					<?php comment_text(); ?>
				</div>
				
				<div class="comment-actions">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => $add_below,
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="reply">',
								'after'     => '</span>',
							)
						)
					);
					?>
					
					<?php edit_comment_link( __( 'Edit', 'nosfir' ), '<span class="edit-link">', '</span>' ); ?>
					
					<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
						<span class="comment-moderate">
							<?php
							$spam_link = add_query_arg( array(
								'action' => 'spam',
								'c'      => $comment->comment_ID,
							), admin_url( 'comment.php' ) );
							?>
							<a href="<?php echo esc_url( wp_nonce_url( $spam_link, 'delete-comment_' . $comment->comment_ID ) ); ?>">
								<?php esc_html_e( 'Spam', 'nosfir' ); ?>
							</a>
							
							<?php
							$trash_link = add_query_arg( array(
								'action' => 'trash',
								'c'      => $comment->comment_ID,
							), admin_url( 'comment.php' ) );
							?>
							<a href="<?php echo esc_url( wp_nonce_url( $trash_link, 'delete-comment_' . $comment->comment_ID ) ); ?>">
								<?php esc_html_e( 'Trash', 'nosfir' ); ?>
							</a>
						</span>
					<?php endif; ?>
				</div>
				
			<?php if ( 'div' != $args['style'] ) : ?>
			</div>
			<?php endif; ?>
		</article>
		<?php
	}
}

if ( ! function_exists( 'nosfir_footer_widgets' ) ) {
	/**
	 * Display the footer widget regions
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_footer_widgets() {
		$rows    = intval( apply_filters( 'nosfir_footer_widget_rows', 1 ) );
		$regions = intval( apply_filters( 'nosfir_footer_widget_columns', 4 ) );

		for ( $row = 1; $row <= $rows; $row++ ) :

			// Defines the number of active columns in this footer row
			$active_columns = 0;
			for ( $region = 1; $region <= $regions; $region++ ) {
				$footer_n = $region + $regions * ( $row - 1 );
				if ( is_active_sidebar( 'footer-' . $footer_n ) ) {
					$active_columns++;
				}
			}

			if ( $active_columns > 0 ) :
				?>
				<div class="footer-widgets footer-widgets-row-<?php echo esc_attr( $row ); ?> footer-widgets-<?php echo esc_attr( $active_columns ); ?>-columns">
					<div class="container">
						<div class="footer-widgets-inner">
							<?php
							for ( $column = 1; $column <= $regions; $column++ ) :
								$footer_n = $column + $regions * ( $row - 1 );

								if ( is_active_sidebar( 'footer-' . $footer_n ) ) :
									?>
									<div class="footer-widget footer-widget-<?php echo esc_attr( $column ); ?>">
										<?php dynamic_sidebar( 'footer-' . $footer_n ); ?>
									</div>
									<?php
								endif;
							endfor;
							?>
						</div>
					</div>
				</div>
				<?php
			endif;
		endfor;
	}
}

if ( ! function_exists( 'nosfir_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_credit() {
		$copyright_text = get_theme_mod( 'nosfir_copyright_text', '' );
		
		if ( empty( $copyright_text ) ) {
			$copyright_text = sprintf(
				/* translators: 1: Current year, 2: Site name */
				esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'nosfir' ),
				date_i18n( 'Y' ),
				get_bloginfo( 'name' )
			);
		}
		
		$developer_link = get_theme_mod( 'nosfir_developer_link', true );
		?>
		<div class="site-info">
			<div class="container">
				<div class="site-info-inner">
					<div class="copyright">
						<?php echo wp_kses_post( $copyright_text ); ?>
					</div>
					
					<?php if ( $developer_link ) : ?>
						<div class="theme-credit">
							<?php
							printf(
								/* translators: 1: Theme name, 2: Theme author */
								esc_html__( 'Theme: %1$s by %2$s', 'nosfir' ),
								'Nosfir',
								'<a href="' . esc_url( 'https://github.com/davidcreator/Nosfir' ) . '" target="_blank" rel="noopener noreferrer">David Creator</a>'
							);
							?>
						</div>
					<?php endif; ?>
					
					<?php if ( function_exists( 'the_privacy_policy_link' ) ) : ?>
						<div class="privacy-policy">
							<?php the_privacy_policy_link(); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .site-info -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_header_widget_region' ) ) {
	/**
	 * Display header widget region
	 *
	 * @since  1.0.0
	 */
	function nosfir_header_widget_region() {
		if ( is_active_sidebar( 'header-1' ) ) {
			?>
			<div class="header-widget-region" role="complementary">
				<div class="container">
					<?php dynamic_sidebar( 'header-1' ); ?>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_site_branding() {
		?>
		<div class="site-branding">
			<?php nosfir_site_title_or_logo(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_site_title_or_logo' ) ) {
	/**
	 * Display the site title or logo
	 *
	 * @since 1.0.0
	 * @param bool $echo Echo the string or return it.
	 * @return string
	 */
	function nosfir_site_title_or_logo( $echo = true ) {
		$html = '';
		
		if ( has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
			$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
			
			if ( $logo ) {
				$html = '<div class="site-logo">';
				$html .= '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
				$html .= '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
				$html .= '</a>';
				$html .= '</div>';
			}
		}
		
		$show_title = get_theme_mod( 'nosfir_show_site_title', true );
		$show_tagline = get_theme_mod( 'nosfir_show_tagline', true );
		
		if ( $show_title || $show_tagline ) {
			$html .= '<div class="site-title-wrapper">';
			
			if ( $show_title ) {
				$tag = is_home() || is_front_page() ? 'h1' : 'p';
				$html .= '<' . $tag . ' class="site-title">';
				$html .= '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
				$html .= esc_html( get_bloginfo( 'name' ) );
				$html .= '</a>';
				$html .= '</' . $tag . '>';
			}
			
			if ( $show_tagline && get_bloginfo( 'description' ) ) {
				$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description' ) ) . '</p>';
			}
			
			$html .= '</div>';
		}
		
		if ( ! $echo ) {
			return $html;
		}
		
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'nosfir_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_primary_navigation() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'nosfir' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="menu-toggle-inner">
					<span class="toggle-bar"></span>
					<span class="toggle-bar"></span>
					<span class="toggle-bar"></span>
				</span>
				<span class="menu-toggle-text"><?php esc_html_e( 'Menu', 'nosfir' ); ?></span>
			</button>
			
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'menu_id'         => 'primary-menu',
					'menu_class'      => 'nav-menu',
					'container'       => 'div',
					'container_class' => 'primary-navigation',
					'fallback_cb'     => 'nosfir_fallback_menu',
				)
			);
			?>
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_secondary_navigation' ) ) {
	/**
	 * Display Secondary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_secondary_navigation() {
		if ( has_nav_menu( 'secondary' ) ) {
			?>
			<nav class="secondary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Secondary Navigation', 'nosfir' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'secondary',
						'menu_class'     => 'secondary-menu',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_mobile_navigation' ) ) {
	/**
	 * Display Mobile Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_mobile_navigation() {
		?>
		<nav id="mobile-navigation" class="mobile-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'nosfir' ); ?>">
			<div class="mobile-menu-header">
				<span class="mobile-menu-title"><?php esc_html_e( 'Menu', 'nosfir' ); ?></span>
				<button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close Menu', 'nosfir' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
						<line x1="18" y1="6" x2="6" y2="18"></line>
						<line x1="6" y1="6" x2="18" y2="18"></line>
					</svg>
				</button>
			</div>
			
			<?php
			wp_nav_menu(
				array(
					'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
					'menu_class'     => 'mobile-menu',
					'container'      => false,
					// 'walker'         => new Nosfir_Mobile_Walker_Nav_Menu(), // Comentado pois a classe Walker pode não existir
				)
			);
			?>
			
			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<div class="mobile-menu-social">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'social',
							'menu_class'     => 'social-menu',
							'container'      => false,
							'depth'          => 1,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>',
						)
					);
					?>
				</div>
			<?php endif; ?>
		</nav>
		<div class="mobile-menu-overlay"></div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_fallback_menu' ) ) {
	/**
	 * Fallback menu when no menu is selected
	 *
	 * @since 1.0.0
	 */
	function nosfir_fallback_menu() {
		?>
		<div class="primary-navigation">
			<ul class="nav-menu">
				<?php
				wp_list_pages(
					array(
						'title_li' => '',
						'depth'    => 1,
					)
				);
				?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_skip_links' ) ) {
	/**
	 * Skip links
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function nosfir_skip_links() {
		?>
		<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_html_e( 'Skip to navigation', 'nosfir' ); ?></a>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'nosfir' ); ?></a>
		<a class="skip-link screen-reader-text" href="#footer"><?php esc_html_e( 'Skip to footer', 'nosfir' ); ?></a>
		<?php
	}
}

if ( ! function_exists( 'nosfir_search_form' ) ) {
	/**
	 * Display search form
	 *
	 * @since 1.0.0
	 * @param string $form_id Form ID.
	 * @return void
	 */
	function nosfir_search_form( $form_id = 'searchform' ) {
		?>
		<form role="search" method="get" id="<?php echo esc_attr( $form_id ); ?>" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="<?php echo esc_attr( $form_id ); ?>-field" class="screen-reader-text">
				<?php esc_html_e( 'Search for:', 'nosfir' ); ?>
			</label>
			<div class="search-form-inner">
				<input type="search" 
					   id="<?php echo esc_attr( $form_id ); ?>-field" 
					   class="search-field" 
					   placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'nosfir' ); ?>" 
					   value="<?php echo get_search_query(); ?>" 
					   name="s" />
				<button type="submit" class="search-submit">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor">
						<circle cx="9" cy="9" r="7"/>
						<path d="M14 14l3.5 3.5"/>
					</svg>
					<span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'nosfir' ); ?></span>
				</button>
			</div>
		</form>
		<?php
	}
}

if ( ! function_exists( 'nosfir_homepage_header' ) ) {
	/**
	 * Display the homepage header
	 *
	 * @since 1.0.0
	 */
	function nosfir_homepage_header() {
		if ( is_page_template( 'template-homepage.php' ) ) {
			?>
			<header class="homepage-header">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="homepage-hero" style="<?php echo nosfir_homepage_content_styles(); ?>">
						<div class="homepage-hero-content">
							<?php the_title( '<h1 class="homepage-title">', '</h1>' ); ?>
							
							<?php if ( has_excerpt() ) : ?>
								<div class="homepage-excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif; ?>
							
							<?php
							$cta_text = get_post_meta( get_the_ID(), '_nosfir_cta_text', true );
							$cta_url = get_post_meta( get_the_ID(), '_nosfir_cta_url', true );
							
							if ( $cta_text && $cta_url ) : ?>
								<a href="<?php echo esc_url( $cta_url ); ?>" class="homepage-cta button button-primary">
									<?php echo esc_html( $cta_text ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php else : ?>
					<?php the_title( '<h1 class="homepage-title">', '</h1>' ); ?>
				<?php endif; ?>
				
				<?php edit_post_link( __( 'Edit this section', 'nosfir' ), '<div class="edit-link">', '</div>' ); ?>
			</header>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_page_header' ) ) {
	/**
	 * Display the page header
	 *
	 * @since 1.0.0
	 */
	function nosfir_page_header() {
		if ( is_front_page() && is_page_template( 'template-fullwidth.php' ) ) {
			return;
		}
		
		?>
		<header class="entry-header">
			<?php
			nosfir_post_thumbnail( 'full' );
			the_title( '<h1 class="entry-title">', '</h1>' );
			
			// Display page subtitle if set
			$subtitle = get_post_meta( get_the_ID(), '_nosfir_subtitle', true );
			if ( $subtitle ) {
				echo '<p class="entry-subtitle">' . esc_html( $subtitle ) . '</p>';
			}
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_page_content' ) ) {
	/**
	 * Display the post content
	 *
	 * @since 1.0.0
	 */
	function nosfir_page_content() {
		?>
		<div class="entry-content">
			<?php 
			the_content();
			
			wp_link_pages(
				array(
					'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
					'after'       => '</div>',
					'link_before' => '<span class="page-number">',
					'link_after'  => '</span>',
				)
			);
			?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function nosfir_post_header() {
		?>
		<header class="entry-header">
			<?php
			do_action( 'nosfir_post_header_before' );
			
			if ( is_sticky() && is_home() && ! is_paged() ) {
				echo '<span class="sticky-post">' . esc_html__( 'Featured', 'nosfir' ) . '</span>';
			}
			
			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			}
			
			if ( 'post' === get_post_type() ) {
				nosfir_post_meta();
			}
			
			do_action( 'nosfir_post_header_after' );
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function nosfir_post_content() {
		?>
		<div class="entry-content">
			<?php
			do_action( 'nosfir_post_content_before' );
			
			if ( is_single() || is_page() ) {
				the_content(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'nosfir' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					)
				);
			} else {
				if ( has_excerpt() || get_theme_mod( 'nosfir_excerpt_type', 'excerpt' ) === 'excerpt' ) {
					the_excerpt();
				} else {
					the_content(
						sprintf(
							wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
								__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'nosfir' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							get_the_title()
						)
					);
				}
			}
			
			wp_link_pages(
				array(
					'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'nosfir' ),
					'after'       => '</div>',
					'link_before' => '<span class="page-number">',
					'link_after'  => '</span>',
				)
			);
			
			do_action( 'nosfir_post_content_after' );
			?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_meta' ) ) {
	/**
	 * Display the post meta
	 *
	 * @since 1.0.0
	 */
	function nosfir_post_meta() {
		if ( 'post' !== get_post_type() ) {
			return;
		}
		
		$show_author = get_theme_mod( 'nosfir_post_author', true );
		$show_date = get_theme_mod( 'nosfir_post_date', true );
		$show_comments = get_theme_mod( 'nosfir_post_comments', true );
		$show_categories = get_theme_mod( 'nosfir_post_categories', true );
		$show_reading_time = get_theme_mod( 'nosfir_post_reading_time', true );
		
		?>
		<div class="entry-meta">
			<?php if ( $show_author ) : ?>
				<span class="posted-by">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 24 ); ?>
					<?php
					printf(
						/* translators: %s: post author */
						esc_html_x( 'by %s', 'post author', 'nosfir' ),
						'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
					);
					?>
				</span>
			<?php endif; ?>
			
			<?php if ( $show_date ) : ?>
				<span class="posted-on">
					<time class="entry-date published updated" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
				</span>
			<?php endif; ?>
			
			<?php if ( $show_categories && has_category() ) : ?>
				<span class="posted-in">
					<?php the_category( ', ' ); ?>
				</span>
			<?php endif; ?>
			
			<?php if ( $show_comments && ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
				<span class="comments-link">
					<?php comments_popup_link( esc_html__( 'Leave a comment', 'nosfir' ), esc_html__( '1 Comment', 'nosfir' ), esc_html__( '% Comments', 'nosfir' ) ); ?>
				</span>
			<?php endif; ?>
			
			<?php if ( $show_reading_time ) : ?>
				<span class="reading-time">
					<?php
					// $reading_time = nosfir_get_reading_time();
					$reading_time = 5; // Placeholder
					printf(
						/* translators: %s: reading time */
						esc_html( _n( '%s min read', '%s min read', $reading_time, 'nosfir' ) ),
						$reading_time
					);
					?>
				</span>
			<?php endif; ?>
			
			<?php edit_post_link( esc_html__( 'Edit', 'nosfir' ), '<span class="edit-link">', '</span>' ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_footer' ) ) {
	/**
	 * Display the post footer
	 *
	 * @since 1.0.0
	 */
	function nosfir_post_footer() {
		if ( ! is_single() ) {
			return;
		}
		
		$show_tags = get_theme_mod( 'nosfir_post_tags', true );
		$show_share = get_theme_mod( 'nosfir_post_share', true );
		$show_author_box = get_theme_mod( 'nosfir_author_box', true );
		
		?>
		<footer class="entry-footer">
			<?php if ( $show_tags && has_tag() ) : ?>
				<div class="entry-tags">
					<span class="tags-label"><?php esc_html_e( 'Tags:', 'nosfir' ); ?></span>
					<?php the_tags( '', '', '' ); ?>
				</div>
			<?php endif; ?>
			
			<?php if ( $show_share ) : ?>
				<div class="entry-share">
					<span class="share-label"><?php esc_html_e( 'Share:', 'nosfir' ); ?></span>
					<?php // nosfir_social_share(); ?>
				</div>
			<?php endif; ?>
			
			<?php if ( $show_author_box ) : ?>
				<?php nosfir_author_box(); ?>
			<?php endif; ?>
		</footer>
		<?php
	}
}

if ( ! function_exists( 'nosfir_author_box' ) ) {
	/**
	 * Display author box
	 *
	 * @since 1.0.0
	 */
	function nosfir_author_box() {
		$author_id = get_the_author_meta( 'ID' );
		$author_description = get_the_author_meta( 'description' );
		
		if ( ! $author_description ) {
			return;
		}
		
		?>
		<div class="author-box">
			<div class="author-avatar">
				<?php echo get_avatar( $author_id, 100 ); ?>
			</div>
			<div class="author-info">
				<h3 class="author-name">
					<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
						<?php the_author(); ?>
					</a>
				</h3>
				<div class="author-description">
					<?php echo wp_kses_post( $author_description ); ?>
				</div>
				<?php
				// $social_links = nosfir_get_author_social( $author_id );
				$social_links = array();
				if ( ! empty( $social_links ) ) :
					?>
					<div class="author-social">
						<?php foreach ( $social_links as $platform => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="social-<?php echo esc_attr( $platform ); ?>">
								<span class="screen-reader-text"><?php echo esc_html( ucfirst( $platform ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_post_navigation' ) ) {
	/**
	 * Display navigation to next/previous post
	 *
	 * @since 1.0.0
	 */
	function nosfir_post_navigation() {
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		
		if ( ! $prev_post && ! $next_post ) {
			return;
		}
		
		?>
		<nav class="post-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'nosfir' ); ?></h2>
			<div class="nav-links">
				<?php if ( $prev_post ) : ?>
					<div class="nav-previous">
						<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" rel="prev">
							<?php if ( has_post_thumbnail( $prev_post ) ) : ?>
								<?php echo get_the_post_thumbnail( $prev_post, 'thumbnail' ); ?>
							<?php endif; ?>
							<div class="nav-content">
								<span class="nav-subtitle"><?php esc_html_e( 'Previous Post', 'nosfir' ); ?></span>
								<span class="nav-title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
							</div>
						</a>
					</div>
				<?php endif; ?>
				
				<?php if ( $next_post ) : ?>
					<div class="nav-next">
						<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" rel="next">
							<div class="nav-content">
								<span class="nav-subtitle"><?php esc_html_e( 'Next Post', 'nosfir' ); ?></span>
								<span class="nav-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
							</div>
							<?php if ( has_post_thumbnail( $next_post ) ) : ?>
								<?php echo get_the_post_thumbnail( $next_post, 'thumbnail' ); ?>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'nosfir_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts
	 *
	 * @since 1.0.0
	 */
	function nosfir_paging_nav() {
		// nosfir_pagination();
		the_posts_pagination();
	}
}

if ( ! function_exists( 'nosfir_get_sidebar' ) ) {
	/**
	 * Display nosfir sidebar
	 *
	 * @uses get_sidebar()
	 * @since 1.0.0
	 */
	function nosfir_get_sidebar() {
		// $sidebar_position = nosfir_get_sidebar_position();
		$sidebar_position = 'right';
		
		if ( 'none' !== $sidebar_position ) {
			get_sidebar();
		}
	}
}

if ( ! function_exists( 'nosfir_post_thumbnail' ) ) {
	/**
	 * Display post thumbnail
	 *
	 * @var string $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses the_post_thumbnail
	 * @param string $size the post thumbnail size.
	 * @since 1.0.0
	 */
	function nosfir_post_thumbnail( $size = 'full' ) {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}
		
		if ( is_singular() ) {
			?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail( $size ); ?>
			</div>
			<?php
		} else {
			?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
				the_post_thumbnail(
					$size,
					array(
						'alt' => the_title_attribute(
							array(
								'echo' => false,
							)
						),
					)
				);
				?>
			</a>
			<?php
		}
	}
}

if ( ! function_exists( 'nosfir_related_posts' ) ) {
	/**
	 * Display related posts
	 *
	 * @since 1.0.0
	 */
	function nosfir_related_posts() {
		if ( ! is_single() || ! get_theme_mod( 'nosfir_related_posts', true ) ) {
			return;
		}
		
		$categories = wp_get_post_categories( get_the_ID() );
		
		if ( empty( $categories ) ) {
			return;
		}
		
		$args = array(
			'category__in'        => $categories,
			'post__not_in'        => array( get_the_ID() ),
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
		);
		
		$related_posts = new WP_Query( $args );
		
		if ( $related_posts->have_posts() ) :
			?>
			<div class="related-posts">
				<h3 class="related-posts-title"><?php esc_html_e( 'Related Posts', 'nosfir' ); ?></h3>
				<div class="related-posts-grid">
					<?php
					while ( $related_posts->have_posts() ) :
						$related_posts->the_post();
						?>
						<article class="related-post">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" class="related-post-thumbnail">
									<?php the_post_thumbnail( 'medium' ); ?>
								</a>
							<?php endif; ?>
							<h4 class="related-post-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h4>
							<div class="related-post-meta">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date() ); ?>
								</time>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'nosfir_breadcrumb' ) ) {
	/**
	 * Display breadcrumb
	 *
	 * @since 1.0.0
	 */
	function nosfir_breadcrumb() {
		if ( ! get_theme_mod( 'nosfir_breadcrumb', true ) ) {
			return;
		}
		
		if ( is_front_page() ) {
			return;
		}
		
		echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '">';
		echo '<ol class="breadcrumb-list">';
		
		// Home
		echo '<li class="breadcrumb-item"><a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Home', 'nosfir' ) . '</a></li>';
		
		if ( is_category() || is_single() ) {
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				echo '<li class="breadcrumb-item"><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
			}
			
			if ( is_single() ) {
				echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
			}
		} elseif ( is_page() ) {
			global $post;
			if ( $post->post_parent ) {
				$parent_id = $post->post_parent;
				$breadcrumbs = array();
				
				while ( $parent_id ) {
					$page = get_page( $parent_id );
					$breadcrumbs[] = '<li class="breadcrumb-item"><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . get_the_title( $page->ID ) . '</a></li>';
					$parent_id = $page->post_parent;
				}
				
				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) {
					echo $crumb;
				}
			}
			
			echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
		} elseif ( is_search() ) {
			echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html__( 'Search Results', 'nosfir' ) . '</li>';
		} elseif ( is_404() ) {
			echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html__( '404 Error', 'nosfir' ) . '</li>';
		}
		
		echo '</ol>';
		echo '</nav>';
	}
}

/**
 * MISSING FUNCTIONS ADDED BY ASSISTANT
 */

if ( ! function_exists( 'nosfir_header_styles' ) ) {
	function nosfir_header_styles() {
		// Return empty string or inline styles
		echo '';
	}
}

if ( ! function_exists( 'nosfir_top_bar_container' ) ) {
	function nosfir_top_bar_container() {
		echo '<div class="top-bar"><div class="container">';
	}
}

if ( ! function_exists( 'nosfir_top_bar_left' ) ) {
	function nosfir_top_bar_left() {
		echo '<div class="top-bar-left">';
		// Add default content if needed
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_top_bar_right' ) ) {
	function nosfir_top_bar_right() {
		echo '<div class="top-bar-right">';
		// Add default content if needed
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_top_bar_container_close' ) ) {
	function nosfir_top_bar_container_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'nosfir_header_container' ) ) {
	function nosfir_header_container() {
		echo '<div class="container">';
	}
}

if ( ! function_exists( 'nosfir_header_search' ) ) {
	function nosfir_header_search() {
		echo '<div class="header-search">';
		nosfir_search_form();
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_header_account' ) ) {
	function nosfir_header_account() {
		if ( ! nosfir_is_woocommerce_activated() ) {
			return;
		}
		echo '<div class="header-account">';
		echo '<a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" title="' . esc_attr__( 'My Account', 'nosfir' ) . '">';
		echo esc_html__( 'My Account', 'nosfir' );
		echo '</a>';
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_header_cart' ) ) {
	function nosfir_header_cart() {
		if ( ! nosfir_is_woocommerce_activated() ) {
			return;
		}
		// Basic WooCommerce cart stub
		?>
		<div class="header-cart">
			<a class="cart-customlocation" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'nosfir' ); ?>">
				<span class="count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'nosfir_header_container_close' ) ) {
	function nosfir_header_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_hero_section' ) ) {
	function nosfir_hero_section() {
		if ( is_front_page() && is_home() ) {
			// Hero section logic
		}
	}
}

if ( ! function_exists( 'nosfir_footer_cta' ) ) {
	function nosfir_footer_cta() {
		// Footer Call to Action
	}
}

if ( ! function_exists( 'nosfir_footer_container' ) ) {
	function nosfir_footer_container() {
		echo '<div class="footer-container">';
	}
}

if ( ! function_exists( 'nosfir_footer_navigation' ) ) {
	function nosfir_footer_navigation() {
		if ( has_nav_menu( 'footer' ) ) {
			echo '<nav class="footer-navigation">';
			wp_nav_menu( array( 'theme_location' => 'footer' ) );
			echo '</nav>';
		}
	}
}

if ( ! function_exists( 'nosfir_footer_social' ) ) {
	function nosfir_footer_social() {
		// Social icons in footer
	}
}

if ( ! function_exists( 'nosfir_footer_container_close' ) ) {
	function nosfir_footer_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_footer_bottom_container' ) ) {
	function nosfir_footer_bottom_container() {
		echo '<div class="footer-bottom-container">';
	}
}

if ( ! function_exists( 'nosfir_footer_bottom_container_close' ) ) {
	function nosfir_footer_bottom_container_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'nosfir_homepage_content' ) ) {
	function nosfir_homepage_content() {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'content', 'homepage' );
		}
	}
}

if ( ! function_exists( 'nosfir_homepage_content_styles' ) ) {
    function nosfir_homepage_content_styles() {
        return '';
    }
}
