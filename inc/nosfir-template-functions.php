<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Nosfir
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ============================================
 * HEADER FUNCTIONS
 * ============================================
 */

/**
 * Site Branding - Logo and Site Title
 */
if ( ! function_exists( 'nosfir_site_branding' ) ) {
    function nosfir_site_branding() {
        ?>
        <div class="site-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            }
            ?>
            
            <div class="site-branding-text">
                <?php if ( is_front_page() && is_home() ) : ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </h1>
                <?php else : ?>
                    <p class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </p>
                <?php endif; ?>

                <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) :
                    ?>
                    <p class="site-description"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div><!-- .site-branding-text -->
        </div><!-- .site-branding -->
        <?php
    }
}

/**
 * Primary Menu Fallback
 */
if ( ! function_exists( 'nosfir_primary_menu_fallback' ) ) {
    function nosfir_primary_menu_fallback() {
        echo '<ul id="primary-menu" class="nav-menu primary-menu">';
        wp_list_pages(
            array(
                'title_li' => '',
                'depth'    => 2,
            )
        );
        echo '</ul>';
    }
}

/**
 * Header Search - Versão Final Corrigida
 * 
 * Renderiza apenas 1 botão de busca com dropdown
 */
if ( ! function_exists( 'nosfir_header_search' ) ) {
    function nosfir_header_search() {
        // Prevenir execução duplicada
        static $rendered = false;
        if ( $rendered ) {
            return;
        }
        $rendered = true;
        
        // Verificar se está habilitado
        if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
            return;
        }
        ?>
        <div class="header-search" id="nosfir-header-search">
            <button type="button" 
                    class="search-toggle" 
                    id="nosfir-search-toggle"
                    aria-label="<?php esc_attr_e( 'Open search', 'nosfir' ); ?>" 
                    aria-expanded="false">
                <svg class="search-icon-open" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="9" cy="9" r="7"/>
                    <path d="M14 14l4 4"/>
                </svg>
                <svg class="search-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <div class="search-dropdown" id="nosfir-search-dropdown" aria-hidden="true">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" 
                           class="search-field" 
                           placeholder="<?php esc_attr_e( 'Type to search...', 'nosfir' ); ?>" 
                           value="<?php echo get_search_query(); ?>" 
                           name="s" 
                           autocomplete="off"
                           aria-label="<?php esc_attr_e( 'Search', 'nosfir' ); ?>" />
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'nosfir' ); ?>">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="9" cy="9" r="7"/>
                            <path d="M14 14l4 4"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}


/**
 * ============================================
 * POST META FUNCTIONS
 * ============================================
 */

/**
 * Prints HTML with meta information for the current post-date/time
 */
if ( ! function_exists( 'nosfir_posted_on' ) ) {
    function nosfir_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        printf(
            '<span class="posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span>',
            esc_url( get_permalink() ),
            $time_string
        );
    }
}

/**
 * Prints HTML with meta information for the current author
 */
if ( ! function_exists( 'nosfir_posted_by' ) ) {
    function nosfir_posted_by() {
        printf(
            '<span class="byline"><span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span></span>',
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        );
    }
}

/**
 * Reading time estimate
 */
if ( ! function_exists( 'nosfir_reading_time' ) ) {
    function nosfir_reading_time() {
        $content = get_post_field( 'post_content', get_the_ID() );
        $word_count = str_word_count( strip_tags( $content ) );
        $reading_time = ceil( $word_count / 200 ); // Average reading speed
        
        printf(
            '<span class="reading-time">%s %s</span>',
            esc_html( $reading_time ),
            esc_html( _n( 'min read', 'min read', $reading_time, 'nosfir' ) )
        );
    }
}

/**
 * Post Tags
 */
if ( ! function_exists( 'nosfir_post_tags' ) ) {
    function nosfir_post_tags() {
        if ( ! has_tag() || ! get_theme_mod( 'nosfir_post_tags', true ) ) {
            return;
        }
        ?>
        <div class="post-tags">
            <span class="tags-label"><?php esc_html_e( 'Tags:', 'nosfir' ); ?></span>
            <?php the_tags( '', '', '' ); ?>
        </div>
        <?php
    }
}

/**
 * ============================================
 * SOCIAL & SHARING FUNCTIONS
 * ============================================
 */

/**
 * Get Social Links from Customizer
 * 
 * @return array Array of social links with url, icon, and name
 */
if ( ! function_exists( 'nosfir_get_social_links' ) ) {
    function nosfir_get_social_links() {
        $social_networks = array(
            'facebook'  => array( 'icon' => 'fab fa-facebook-f', 'name' => 'Facebook' ),
            'twitter'   => array( 'icon' => 'fab fa-twitter', 'name' => 'Twitter' ),
            'instagram' => array( 'icon' => 'fab fa-instagram', 'name' => 'Instagram' ),
            'linkedin'  => array( 'icon' => 'fab fa-linkedin-in', 'name' => 'LinkedIn' ),
            'youtube'   => array( 'icon' => 'fab fa-youtube', 'name' => 'YouTube' ),
            'github'    => array( 'icon' => 'fab fa-github', 'name' => 'GitHub' ),
            'tiktok'    => array( 'icon' => 'fab fa-tiktok', 'name' => 'TikTok' ),
            'pinterest' => array( 'icon' => 'fab fa-pinterest', 'name' => 'Pinterest' ),
            'whatsapp'  => array( 'icon' => 'fab fa-whatsapp', 'name' => 'WhatsApp' ),
        );
        
        /**
         * Filter to add custom social networks
         * 
         * @param array $social_networks
         */
        $social_networks = apply_filters( 'nosfir_social_networks', $social_networks );
        
        $links = array();
        
        foreach ( $social_networks as $network => $data ) {
            $url = get_theme_mod( 'nosfir_social_' . $network, '' );
            if ( ! empty( $url ) ) {
                $links[] = array(
                    'url'  => esc_url( $url ),
                    'icon' => esc_attr( $data['icon'] ),
                    'name' => esc_html( $data['name'] ),
                    'slug' => sanitize_key( $network ),
                );
            }
        }
        
        return $links;
    }
}

/**
 * Display Social Links
 */
if ( ! function_exists( 'nosfir_display_social_links' ) ) {
    function nosfir_display_social_links( $args = array() ) {
        $defaults = array(
            'class'       => 'social-links',
            'show_labels' => false,
            'icon_size'   => 'normal', // small, normal, large
        );
        
        $args = wp_parse_args( $args, $defaults );
        $links = nosfir_get_social_links();
        
        if ( empty( $links ) ) {
            return;
        }
        
        $size_class = 'icon-size-' . sanitize_html_class( $args['icon_size'] );
        ?>
        <ul class="<?php echo esc_attr( $args['class'] . ' ' . $size_class ); ?>">
            <?php foreach ( $links as $link ) : ?>
                <li class="social-item social-<?php echo esc_attr( $link['slug'] ); ?>">
                    <a href="<?php echo esc_url( $link['url'] ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="<?php echo esc_attr( $link['name'] ); ?>">
                        <i class="<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></i>
                        <?php if ( $args['show_labels'] ) : ?>
                            <span class="social-label"><?php echo esc_html( $link['name'] ); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}

/**
 * Social Share Buttons
 */
if ( ! function_exists( 'nosfir_social_share' ) ) {
    function nosfir_social_share() {
        if ( ! get_theme_mod( 'nosfir_post_share', true ) ) {
            return;
        }
        
        $url   = urlencode( esc_url( get_permalink() ) );
        $title = urlencode( get_the_title() );
        ?>
        <div class="social-share">
            <span class="share-label"><?php esc_html_e( 'Share:', 'nosfir' ); ?></span>
            
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               class="share-facebook"
               aria-label="<?php esc_attr_e( 'Share on Facebook', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                </svg>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               class="share-twitter"
               aria-label="<?php esc_attr_e( 'Share on Twitter', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/>
                </svg>
            </a>
            
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               class="share-linkedin"
               aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z"/>
                </svg>
            </a>
            
            <a href="https://api.whatsapp.com/send?text=<?php echo $title; ?>%20<?php echo $url; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               class="share-whatsapp"
               aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </a>
        </div>
        <?php
    }
}

/**
 * ============================================
 * AUTHOR & POST FUNCTIONS
 * ============================================
 */

/**
 * Author Box
 */
if ( ! function_exists( 'nosfir_author_box' ) ) {
    function nosfir_author_box() {
        if ( ! get_theme_mod( 'nosfir_author_box', true ) ) {
            return;
        }
        
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        ?>
        <div class="author-box">
            <div class="author-avatar">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
            </div>
            <div class="author-info">
                <h3 class="author-name">
                    <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                        <?php the_author(); ?>
                    </a>
                </h3>
                <?php if ( get_the_author_meta( 'description' ) ) : ?>
                    <div class="author-bio">
                        <?php the_author_meta( 'description' ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

/**
 * Related Posts
 */
if ( ! function_exists( 'nosfir_related_posts' ) ) {
    function nosfir_related_posts() {
        if ( ! get_theme_mod( 'nosfir_related_posts', true ) ) {
            return;
        }
        
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        $categories = get_the_category();
        if ( empty( $categories ) ) {
            return;
        }
        
        $related = new WP_Query(
            array(
                'category__in'        => wp_list_pluck( $categories, 'term_id' ),
                'post__not_in'        => array( get_the_ID() ),
                'posts_per_page'      => 3,
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true, // Melhora performance
            )
        );
        
        if ( ! $related->have_posts() ) {
            return;
        }
        ?>
        <div class="related-posts">
            <h3 class="related-posts-title"><?php esc_html_e( 'Related Posts', 'nosfir' ); ?></h3>
            <div class="related-posts-grid">
                <?php
                while ( $related->have_posts() ) :
                    $related->the_post();
                    ?>
                    <article class="related-post">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="related-post-thumbnail">
                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                            </a>
                        <?php endif; ?>
                        <h4 class="related-post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    }
}

/**
 * ============================================
 * PAGINATION FUNCTIONS
 * ============================================
 */

/**
 * Pagination
 */
if ( ! function_exists( 'nosfir_pagination' ) ) {
    function nosfir_pagination() {
        global $wp_query;
        
        if ( $wp_query->max_num_pages < 2 ) {
            return;
        }
        ?>
        <nav class="navigation pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'nosfir' ); ?>">
            <h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'nosfir' ); ?></h2>
            <div class="nav-links">
                <?php
                echo paginate_links(
                    array(
                        'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'nosfir' ) . '</span><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>',
                        'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'nosfir' ) . '</span><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>',
                        'mid_size'  => 2,
                    )
                );
                ?>
            </div>
        </nav>
        <?php
    }
}

/**
 * Post Navigation (Single)
 */
if ( ! function_exists( 'nosfir_post_navigation' ) ) {
    function nosfir_post_navigation() {
        if ( ! is_singular( 'post' ) ) {
            return;
        }
        
        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'nosfir' ) . '</span> <span class="nav-title">%title</span>',
            )
        );
    }
}

/**
 * ============================================
 * FOOTER FUNCTIONS
 * ============================================
 */

/**
 * Footer Widgets
 */
if ( ! function_exists( 'nosfir_footer_widgets' ) ) {
    function nosfir_footer_widgets() {
        $columns = 0;
        
        for ( $i = 1; $i <= 4; $i++ ) {
            if ( is_active_sidebar( 'footer-' . $i ) ) {
                $columns++;
            }
        }
        
        if ( $columns === 0 ) {
            return;
        }
        ?>
        <div class="footer-widgets columns-<?php echo esc_attr( $columns ); ?>">
            <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                <?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
                    <div class="footer-widget-area footer-widget-<?php echo esc_attr( $i ); ?>">
                        <?php dynamic_sidebar( 'footer-' . $i ); ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div><!-- .footer-widgets -->
        <?php
    }
}

/**
 * Check if has footer widgets
 */
if ( ! function_exists( 'nosfir_has_footer_widgets' ) ) {
    function nosfir_has_footer_widgets() {
        for ( $i = 1; $i <= 4; $i++ ) {
            if ( is_active_sidebar( 'footer-' . $i ) ) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Footer Credits
 */
if ( ! function_exists( 'nosfir_footer_credits' ) ) {
    function nosfir_footer_credits() {
        $copyright = get_theme_mod( 'nosfir_footer_copyright' );
        
        if ( $copyright ) {
            echo wp_kses_post( $copyright );
        } else {
            ?>
            <span class="copyright">
                &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> 
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
            </span>
            <span class="theme-credit">
                <?php
                printf(
                    /* translators: %s: theme name and link */
                    esc_html__( 'Theme: %s', 'nosfir' ),
                    '<a href="https://github.com/davidcreator/Nosfir" rel="noopener" target="_blank">Nosfir</a>'
                );
                ?>
            </span>
            <?php
        }
    }
}

/**
 * ============================================
 * BREADCRUMB FUNCTIONS
 * ============================================
 */

/**
 * Breadcrumb
 */
if ( ! function_exists( 'nosfir_breadcrumb' ) ) {
    function nosfir_breadcrumb() {
        if ( is_front_page() || ! get_theme_mod( 'nosfir_breadcrumb', true ) ) {
            return;
        }
        
        // Use Yoast breadcrumb if available
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( 
                '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '"><div class="container">', 
                '</div></nav>' 
            );
            return;
        }
        
        // Use RankMath breadcrumb if available
        if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '"><div class="container">';
            rank_math_the_breadcrumbs();
            echo '</div></nav>';
            return;
        }
        
        // Use WooCommerce breadcrumb if on shop
        if ( function_exists( 'woocommerce_breadcrumb' ) && function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
            echo '<nav class="breadcrumb woocommerce-breadcrumb-wrapper" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '"><div class="container">';
            woocommerce_breadcrumb();
            echo '</div></nav>';
            return;
        }
        
        // Simple fallback breadcrumb
        ?>
        <nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'nosfir' ); ?>">
            <div class="container">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'nosfir' ); ?></a>
                <span class="separator" aria-hidden="true">/</span>
                <?php
                if ( is_category() || is_single() ) {
                    $categories = get_the_category();
                    if ( ! empty( $categories ) ) {
                        echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                        echo '<span class="separator" aria-hidden="true">/</span>';
                    }
                    if ( is_single() ) {
                        echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
                    }
                } elseif ( is_page() ) {
                    echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
                } elseif ( is_search() ) {
                    echo '<span class="current">' . esc_html__( 'Search Results', 'nosfir' ) . '</span>';
                } elseif ( is_archive() ) {
                    echo '<span class="current">' . esc_html( get_the_archive_title() ) . '</span>';
                } elseif ( is_404() ) {
                    echo '<span class="current">' . esc_html__( '404 Error', 'nosfir' ) . '</span>';
                }
                ?>
            </div>
        </nav>
        <?php
    }
}
add_action( 'nosfir_before_content', 'nosfir_breadcrumb', 10 );

/**
 * Header Search - Versão Corrigida
 * 
 * Exibe apenas 1 botão de lupa que ao clicar abre o campo de busca
 */
if ( ! function_exists( 'nosfir_header_search' ) ) {
    function nosfir_header_search() {
        // Verificar se a busca está habilitada no customizer
        if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
            return;
        }
        ?>
        <div class="header-search" id="header-search">
            <!-- Botão de Lupa -->
            <button type="button" 
                    class="search-toggle" 
                    id="search-toggle"
                    aria-label="<?php esc_attr_e( 'Open search', 'nosfir' ); ?>" 
                    aria-expanded="false"
                    aria-controls="search-dropdown">
                <!-- Ícone Lupa -->
                <svg class="search-icon icon-search" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <!-- Ícone Fechar (aparece quando aberto) -->
                <svg class="search-icon icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            
            <!-- Dropdown com formulário de busca -->
            <div class="search-dropdown" id="search-dropdown" aria-hidden="true">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <label class="screen-reader-text" for="header-search-input">
                        <?php esc_html_e( 'Search for:', 'nosfir' ); ?>
                    </label>
                    <input type="search" 
                           id="header-search-input"
                           class="search-field" 
                           placeholder="<?php esc_attr_e( 'Type to search...', 'nosfir' ); ?>" 
                           value="<?php echo get_search_query(); ?>" 
                           name="s" 
                           autocomplete="off"
                           required />
                    <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit search', 'nosfir' ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}

/**
 * ============================================
 * HELPER FUNCTIONS
 * ============================================
 */

/**
 * Helper: Check if has recent posts
 */
if ( ! function_exists( 'nosfir_has_recent_posts' ) ) {
    function nosfir_has_recent_posts() {
        $recent = wp_get_recent_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
        return ! empty( $recent );
    }
}

/**
 * Helper: Check if has categories
 */
if ( ! function_exists( 'nosfir_has_categories' ) ) {
    function nosfir_has_categories() {
        $categories = get_categories( array( 'hide_empty' => true ) );
        return ! empty( $categories );
    }
}

/**
 * ============================================
 * WOOCOMMERCE FUNCTIONS
 * ============================================
 */

/**
 * Check if WooCommerce is active
 * 
 * @since 1.0.0
 * @return bool
 */
if ( ! function_exists( 'nosfir_is_woocommerce_active' ) ) {
    function nosfir_is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }
}

// Alias for backwards compatibility
if ( ! function_exists( 'nosfir_is_woocommerce_activated' ) ) {
    function nosfir_is_woocommerce_activated() {
        return nosfir_is_woocommerce_active();
    }
}

/**
 * Render a WooCommerce homepage section
 *
 * @since 1.0.0
 * @param string $section_id   Section identifier
 * @param string $title        Section title
 * @param string $description  Section description
 * @param string $shortcode    WooCommerce shortcode to render
 */
if ( ! function_exists( 'nosfir_homepage_wc_section' ) ) {
    function nosfir_homepage_wc_section( $section_id, $title, $description = '', $shortcode = '' ) {
        if ( empty( $shortcode ) ) {
            return;
        }
        
        $section_classes = array(
            'homepage-section',
            'homepage-wc-section',
            'homepage-' . sanitize_html_class( $section_id ),
        );
        
        /**
         * Filter section classes
         */
        $section_classes = apply_filters( 'nosfir_homepage_wc_section_classes', $section_classes, $section_id );
        ?>
        <section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
            <div class="container">
                
                <?php if ( $title || $description ) : ?>
                    <header class="section-header">
                        <?php if ( $title ) : ?>
                            <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
                        <?php endif; ?>
                        
                        <?php if ( $description ) : ?>
                            <p class="section-description"><?php echo wp_kses_post( $description ); ?></p>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>
                
                <div class="section-content">
                    <?php echo do_shortcode( $shortcode ); ?>
                </div>
                
                <?php
                /**
                 * Hook after section content
                 */
                do_action( 'nosfir_homepage_wc_section_after_' . $section_id );
                ?>
                
            </div>
        </section>
        <?php
    }
}

/**
 * ============================================
 * HOMEPAGE SECTION FUNCTIONS
 * ============================================
 */

/**
 * Get homepage section template
 *
 * @since 1.0.0
 * @param string $section Section name
 * @return bool True if template was loaded
 */
if ( ! function_exists( 'nosfir_get_homepage_section' ) ) {
    function nosfir_get_homepage_section( $section ) {
        $section = sanitize_file_name( $section );
        $template_path = 'template-parts/homepage/section-' . $section . '.php';
        
        if ( locate_template( $template_path ) ) {
            get_template_part( 'template-parts/homepage/section', $section );
            return true;
        }
        
        return false;
    }
}

/**
 * Get all available homepage sections
 *
 * @since 1.0.0
 * @return array
 */
if ( ! function_exists( 'nosfir_get_homepage_sections' ) ) {
    function nosfir_get_homepage_sections() {
        $sections = array(
            'hero'         => __( 'Hero Section', 'nosfir' ),
            'features'     => __( 'Features', 'nosfir' ),
            'about'        => __( 'About', 'nosfir' ),
            'services'     => __( 'Services', 'nosfir' ),
            'portfolio'    => __( 'Portfolio', 'nosfir' ),
            'testimonials' => __( 'Testimonials', 'nosfir' ),
            'team'         => __( 'Team', 'nosfir' ),
            'blog'         => __( 'Blog', 'nosfir' ),
            'cta'          => __( 'Call to Action', 'nosfir' ),
            'contact'      => __( 'Contact', 'nosfir' ),
        );
        
        return apply_filters( 'nosfir_homepage_sections', $sections );
    }
}

/**
 * ============================================
 * CONTACT FORM AJAX HANDLER
 * ============================================
 */

/**
 * AJAX handler para formulário de contato
 */
if ( ! function_exists( 'nosfir_contact_form_handler' ) ) {
    function nosfir_contact_form_handler() {
        // Verificar se é requisição AJAX
        if ( ! wp_doing_ajax() ) {
            wp_die( 'Invalid request' );
        }
        
        // Verificar nonce
        $nonce = isset( $_POST['nosfir_contact_nonce'] ) ? sanitize_text_field( $_POST['nosfir_contact_nonce'] ) : '';
        
        if ( ! wp_verify_nonce( $nonce, 'nosfir_contact_form' ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Erro de segurança. Recarregue a página e tente novamente.', 'nosfir' ) 
            ) );
        }
        
        // Sanitizar dados
        $name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( $_POST['contact_name'] ) : '';
        $email   = isset( $_POST['contact_email'] ) ? sanitize_email( $_POST['contact_email'] ) : '';
        $subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( $_POST['contact_subject'] ) : '';
        $message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( $_POST['contact_message'] ) : '';
        
        // Validar campos obrigatórios
        if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Preencha todos os campos obrigatórios.', 'nosfir' ) 
            ) );
        }
        
        // Validar email
        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Por favor, insira um email válido.', 'nosfir' ) 
            ) );
        }
        
        // Anti-spam: honeypot check
        if ( ! empty( $_POST['website'] ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Erro ao enviar mensagem.', 'nosfir' ) 
            ) );
        }
        
        // Rate limiting simples
        $transient_key = 'nosfir_contact_' . md5( $email );
        if ( get_transient( $transient_key ) ) {
            wp_send_json_error( array( 
                'message' => __( 'Aguarde alguns minutos antes de enviar outra mensagem.', 'nosfir' ) 
            ) );
        }
        
        // Preparar email
        $to = get_theme_mod( 'nosfir_contact_email', get_option( 'admin_email' ) );
        $site_name = get_bloginfo( 'name' );
        
        $email_subject = ! empty( $subject ) 
            ? sprintf( '[%s] %s', $site_name, $subject )
            : sprintf( __( '[%s] Contato de %s', 'nosfir' ), $site_name, $name );
        
        $email_body = sprintf(
            __( "Nome: %s\nEmail: %s\nAssunto: %s\n\nMensagem:\n%s\n\n---\nEnviado via formulário de contato do site.", 'nosfir' ),
            $name,
            $email,
            $subject ?: __( 'Não informado', 'nosfir' ),
            $message
        );
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            sprintf( 'From: %s <%s>', $site_name, get_option( 'admin_email' ) ),
            sprintf( 'Reply-To: %s <%s>', $name, $email ),
        );
        
        /**
         * Filter email headers
         */
        $headers = apply_filters( 'nosfir_contact_email_headers', $headers, $name, $email );
        
        // Enviar
        $sent = wp_mail( $to, $email_subject, $email_body, $headers );
        
        if ( $sent ) {
            // Set rate limiting
            set_transient( $transient_key, true, 2 * MINUTE_IN_SECONDS );
            
            /**
             * Action after successful contact form submission
             */
            do_action( 'nosfir_contact_form_success', $name, $email, $subject, $message );
            
            wp_send_json_success( array( 
                'message' => __( 'Mensagem enviada com sucesso! Entraremos em contato em breve.', 'nosfir' ) 
            ) );
        } else {
            wp_send_json_error( array( 
                'message' => __( 'Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato por outro meio.', 'nosfir' ) 
            ) );
        }
    }
}
add_action( 'wp_ajax_nosfir_contact_submit', 'nosfir_contact_form_handler' );
add_action( 'wp_ajax_nopriv_nosfir_contact_submit', 'nosfir_contact_form_handler' );

/**
 * ============================================
 * UTILITY FUNCTIONS
 * ============================================
 */

/**
 * Get excerpt with custom length
 *
 * @param int $length Excerpt length in words
 * @param string $more More text
 * @return string
 */
if ( ! function_exists( 'nosfir_get_excerpt' ) ) {
    function nosfir_get_excerpt( $length = 20, $more = '...' ) {
        $excerpt = get_the_excerpt();
        $excerpt = wp_trim_words( $excerpt, $length, $more );
        return $excerpt;
    }
}

/**
 * Get theme option with default
 *
 * @param string $option Option name
 * @param mixed $default Default value
 * @return mixed
 */
if ( ! function_exists( 'nosfir_get_option' ) ) {
    function nosfir_get_option( $option, $default = '' ) {
        return get_theme_mod( 'nosfir_' . $option, $default );
    }
}

/**
 * Debug helper (only in development)
 *
 * @param mixed $data Data to debug
 * @param bool $die Whether to die after output
 */
if ( ! function_exists( 'nosfir_debug' ) ) {
    function nosfir_debug( $data, $die = false ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        
        echo '<pre style="background:#1e1e1e;color:#f8f8f2;padding:15px;margin:10px;overflow:auto;font-size:12px;">';
        print_r( $data );
        echo '</pre>';
        
        if ( $die ) {
            die();
        }
    }
}