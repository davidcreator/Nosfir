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
 * Header Search
 */
if ( ! function_exists( 'nosfir_header_search' ) ) {
    function nosfir_header_search() {
        if ( ! get_theme_mod( 'nosfir_header_search', true ) ) {
            return;
        }
        ?>
        <div class="header-search">
            <button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'nosfir' ); ?>" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="9" cy="9" r="7"/>
                    <path d="M14 14l4 4"/>
                </svg>
            </button>
            <div class="search-dropdown" aria-hidden="true">
                <?php get_search_form(); ?>
            </div>
        </div>
        <?php
    }
}

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
 * Social Share Buttons
 */
if ( ! function_exists( 'nosfir_social_share' ) ) {
    function nosfir_social_share() {
        if ( ! get_theme_mod( 'nosfir_post_share', true ) ) {
            return;
        }
        
        $url   = urlencode( get_permalink() );
        $title = urlencode( get_the_title() );
        ?>
        <div class="social-share">
            <span class="share-label"><?php esc_html_e( 'Share:', 'nosfir' ); ?></span>
            
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on Facebook', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                </svg>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on Twitter', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/>
                </svg>
            </a>
            
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'nosfir' ); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6.5 21.5h-5v-13h5v13zM4 6.5C2.5 6.5 1.5 5.3 1.5 4s1-2.4 2.5-2.4c1.6 0 2.5 1 2.6 2.5 0 1.4-1 2.5-2.6 2.5zm11.5 6c-1 0-2 1-2 2v7h-5v-13h5V10s1.6-1.5 4-1.5c3 0 5 2.2 5 6.3v6.7h-5v-7c0-1-1-2-2-2z"/>
                </svg>
            </a>
        </div>
        <?php
    }
}

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
 * Pagination
 */
if ( ! function_exists( 'nosfir_pagination' ) ) {
    function nosfir_pagination() {
        if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
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
                    )
                );
                ?>
            </div>
        </nav>
        <?php
    }
}

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
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> 
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
            </span>
            <span class="theme-credit">
                <?php
                printf(
                    /* translators: %s: theme name and link */
                    esc_html__( 'Theme: %s', 'nosfir' ),
                    '<a href="https://github.com/davidcreator/Nosfir" rel="noopener">Nosfir</a>'
                );
                ?>
            </span>
            <?php
        }
    }
}

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
            yoast_breadcrumb( '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'nosfir' ) . '"><div class="container">', '</div></nav>' );
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
 * Helper: Check if has recent posts
 */
if ( ! function_exists( 'nosfir_has_recent_posts' ) ) {
    function nosfir_has_recent_posts() {
        $recent = wp_get_recent_posts( array( 'numberposts' => 1 ) );
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
 * Get homepage section template
 *
 * @since 1.0.0
 * @param string $section Section name
 */
if ( ! function_exists( 'nosfir_get_homepage_section' ) ) {
    function nosfir_get_homepage_section( $section ) {
        $template_path = 'template-parts/homepage/section-' . $section . '.php';
        
        if ( locate_template( $template_path ) ) {
            get_template_part( 'template-parts/homepage/section', $section );
            return true;
        }
        
        return false;
    }
}