/**
 * Customizer Preview JavaScript
 * 
 * Handles live preview updates in the WordPress Customizer
 * 
 * @package Nosfir
 * @since 1.0.0
 */

(function($, wp) {
    'use strict';

    // Ensure wp.customize exists
    if (typeof wp === 'undefined' || typeof wp.customize === 'undefined') {
        console.warn('Nosfir: wp.customize not available in preview');
        return;
    }

    const api = wp.customize;

    /**
     * Helper function to update CSS custom properties
     */
    function updateCSSProperty(property, value) {
        document.documentElement.style.setProperty(property, value);
    }

    /**
     * Helper function to update element text
     */
    function updateText(selector, value) {
        $(selector).text(value);
    }

    /**
     * Helper function to update element HTML
     */
    function updateHTML(selector, value) {
        $(selector).html(value);
    }

    /**
     * Helper function to toggle class
     */
    function toggleClass(selector, className, condition) {
        $(selector).toggleClass(className, condition);
    }

    /**
     * Helper function to update background
     */
    function updateBackground(selector, value) {
        if (value) {
            $(selector).css('background-image', 'url(' + value + ')');
        } else {
            $(selector).css('background-image', 'none');
        }
    }

    // ========================================
    // Site Identity
    // ========================================

    // Site Title
    api('blogname', function(setting) {
        setting.bind(function(value) {
            $('.site-title a').text(value);
        });
    });

    // Site Description
    api('blogdescription', function(setting) {
        setting.bind(function(value) {
            $('.site-description').text(value);
        });
    });

    // ========================================
    // Colors
    // ========================================

    // Primary Color
    api('nosfir_primary_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-primary', value);
        });
    });

    // Secondary Color
    api('nosfir_secondary_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-secondary', value);
        });
    });

    // Accent Color
    api('nosfir_accent_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-accent', value);
        });
    });

    // Text Color
    api('nosfir_text_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-text', value);
        });
    });

    // Heading Color
    api('nosfir_heading_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-heading', value);
        });
    });

    // Link Color
    api('nosfir_link_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-link', value);
        });
    });

    // Link Hover Color
    api('nosfir_link_hover_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-color-link-hover', value);
        });
    });

    // ========================================
    // Typography
    // ========================================

    // Body Font Size
    api('nosfir_body_font_size', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-font-size-base', value + 'px');
        });
    });

    // Body Line Height
    api('nosfir_body_line_height', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-line-height-base', value);
        });
    });

    // Heading Font Family
    api('nosfir_heading_font', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-font-heading', value);
        });
    });

    // Body Font Family
    api('nosfir_body_font', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-font-body', value);
        });
    });

    // ========================================
    // Header
    // ========================================

    // Header Background
    api('nosfir_header_bg_color', function(setting) {
        setting.bind(function(value) {
            $('.site-header').css('background-color', value);
        });
    });

    // Header Text Color
    api('nosfir_header_text_color', function(setting) {
        setting.bind(function(value) {
            $('.site-header, .site-header a, .main-navigation a').css('color', value);
        });
    });

    // Sticky Header
    api('nosfir_sticky_header', function(setting) {
        setting.bind(function(value) {
            toggleClass('body', 'has-sticky-header', value);
        });
    });

    // ========================================
    // Hero Section
    // ========================================

    // Hero Title
    api('nosfir_hero_title', function(setting) {
        setting.bind(function(value) {
            updateHTML('.nosfir-hero__title', value);
        });
    });

    // Hero Subtitle
    api('nosfir_hero_subtitle', function(setting) {
        setting.bind(function(value) {
            updateHTML('.nosfir-hero__subtitle', value);
        });
    });

    // Hero Button Text
    api('nosfir_hero_btn_text', function(setting) {
        setting.bind(function(value) {
            $('.nosfir-hero__buttons .nosfir-btn--primary').text(value);
        });
    });

    // Hero Button URL
    api('nosfir_hero_btn_url', function(setting) {
        setting.bind(function(value) {
            $('.nosfir-hero__buttons .nosfir-btn--primary').attr('href', value);
        });
    });

    // Hero Background Image
    api('nosfir_hero_bg_image', function(setting) {
        setting.bind(function(value) {
            updateBackground('.nosfir-hero', value);
            toggleClass('.nosfir-hero', 'has-bg-image', !!value);
        });
    });

    // Hero Overlay Color
    api('nosfir_hero_bg_overlay', function(setting) {
        setting.bind(function(value) {
            $('.nosfir-hero__overlay').css('background-color', value);
        });
    });

    // ========================================
    // Section Titles (Generic)
    // ========================================

    const sections = ['features', 'about', 'services', 'portfolio', 'testimonials', 'team', 'blog', 'contact', 'cta'];

    sections.forEach(function(section) {
        // Section Title
        api('nosfir_' + section + '_title', function(setting) {
            setting.bind(function(value) {
                updateHTML('.nosfir-' + section + ' .nosfir-section__title', value);
            });
        });

        // Section Subtitle
        api('nosfir_' + section + '_subtitle', function(setting) {
            setting.bind(function(value) {
                updateHTML('.nosfir-' + section + ' .nosfir-section__subtitle', value);
            });
        });

        // Section Enable/Disable
        api('nosfir_section_' + section + '_enable', function(setting) {
            setting.bind(function(value) {
                if (value) {
                    $('.nosfir-' + section).slideDown(300);
                } else {
                    $('.nosfir-' + section).slideUp(300);
                }
            });
        });
    });

    // ========================================
    // Footer
    // ========================================

    // Footer Background Color
    api('nosfir_footer_bg_color', function(setting) {
        setting.bind(function(value) {
            $('.site-footer').css('background-color', value);
        });
    });

    // Footer Text Color
    api('nosfir_footer_text_color', function(setting) {
        setting.bind(function(value) {
            $('.site-footer, .site-footer a').css('color', value);
        });
    });

    // Footer Copyright
    api('nosfir_footer_copyright', function(setting) {
        setting.bind(function(value) {
            updateHTML('.footer-credits .copyright', value);
        });
    });

    // ========================================
    // Layout
    // ========================================

    // Container Width
    api('nosfir_container_width', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-container-width', value + 'px');
        });
    });

    // Sidebar Width
    api('nosfir_sidebar_width', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-sidebar-width', value + 'px');
        });
    });

    // Sidebar Position
    api('nosfir_sidebar_position', function(setting) {
        setting.bind(function(value) {
            $('body').removeClass('sidebar-left sidebar-right sidebar-none');
            if (value && value !== 'none') {
                $('body').addClass('sidebar-' + value);
            } else {
                $('body').addClass('sidebar-none');
            }
        });
    });

    // ========================================
    // Blog
    // ========================================

    // Blog Layout
    api('nosfir_blog_layout', function(setting) {
        setting.bind(function(value) {
            $('.blog-posts-container')
                .removeClass('layout-list layout-grid layout-masonry')
                .addClass('layout-' + value);
        });
    });

    // Blog Columns
    api('nosfir_blog_columns', function(setting) {
        setting.bind(function(value) {
            $('.blog-posts-container')
                .removeClass('columns-2 columns-3 columns-4')
                .addClass('columns-' + value);
        });
    });

    // ========================================
    // Selective Refresh Partials (for complex elements)
    // ========================================

    // These require page refresh - just trigger it
    api('nosfir_header_layout', function(setting) {
        setting.bind(function() {
            api.selectiveRefresh.requestFullRefresh();
        });
    });

    api('nosfir_footer_layout', function(setting) {
        setting.bind(function() {
            api.selectiveRefresh.requestFullRefresh();
        });
    });

    // ========================================
    // Custom Event Triggers
    // ========================================

    // Trigger custom event when any setting changes
    api.bind('change', function(setting) {
        $(document).trigger('nosfir_customizer_change', [setting.id, setting.get()]);
    });

    // Log for debugging (remove in production)
    if (typeof console !== 'undefined' && window.location.search.indexOf('customize_debug') > -1) {
        api.bind('change', function(setting) {
            console.log('Nosfir Customizer:', setting.id, '=', setting.get());
        });
    }

})(jQuery, wp);