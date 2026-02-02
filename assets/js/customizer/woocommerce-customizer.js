/**
 * WooCommerce Customizer JavaScript
 * 
 * Handles WooCommerce-specific customizer preview updates
 * 
 * @package Nosfir
 * @since 1.0.0
 */

(function($, wp) {
    'use strict';

    // Check if WooCommerce is active
    if (typeof woocommerce_params === 'undefined' && !$('.woocommerce').length) {
        // WooCommerce not active, exit silently
        return;
    }

    // Ensure wp.customize exists
    if (typeof wp === 'undefined' || typeof wp.customize === 'undefined') {
        return;
    }

    const api = wp.customize;

    /**
     * Helper function to update CSS custom properties
     */
    function updateCSSProperty(property, value) {
        document.documentElement.style.setProperty(property, value);
    }

    // ========================================
    // Shop Layout
    // ========================================

    // Products per row
    api('nosfir_wc_products_per_row', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-products-per-row', value);
            
            // Update columns class
            $('.products').removeClass(function(index, className) {
                return (className.match(/columns-\d+/g) || []).join(' ');
            }).addClass('columns-' + value);
        });
    });

    // Products per page (requires refresh)
    api('nosfir_wc_products_per_page', function(setting) {
        setting.bind(function() {
            api.selectiveRefresh.requestFullRefresh();
        });
    });

    // ========================================
    // Product Card
    // ========================================

    // Show/Hide Sale Badge
    api('nosfir_wc_show_sale_badge', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.woocommerce .onsale').show();
            } else {
                $('.woocommerce .onsale').hide();
            }
        });
    });

    // Sale Badge Style
    api('nosfir_wc_sale_badge_style', function(setting) {
        setting.bind(function(value) {
            $('.woocommerce .onsale')
                .removeClass('badge-circle badge-rectangle badge-ribbon')
                .addClass('badge-' + value);
        });
    });

    // Show/Hide Rating
    api('nosfir_wc_show_rating', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.woocommerce .star-rating').show();
            } else {
                $('.woocommerce .star-rating').hide();
            }
        });
    });

    // Show/Hide Add to Cart Button
    api('nosfir_wc_show_add_to_cart', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.products .add_to_cart_button, .products .product_type_variable').show();
            } else {
                $('.products .add_to_cart_button, .products .product_type_variable').hide();
            }
        });
    });

    // Product Image Hover Effect
    api('nosfir_wc_product_hover', function(setting) {
        setting.bind(function(value) {
            $('.products .product')
                .removeClass('hover-none hover-zoom hover-fade hover-slide')
                .addClass('hover-' + value);
        });
    });

    // ========================================
    // Shop Colors
    // ========================================

    // Primary Shop Color
    api('nosfir_wc_primary_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-primary', value);
        });
    });

    // Sale Badge Color
    api('nosfir_wc_sale_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-sale-bg', value);
        });
    });

    // Price Color
    api('nosfir_wc_price_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-price', value);
            $('.woocommerce .price').css('color', value);
        });
    });

    // Button Color
    api('nosfir_wc_button_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-button-bg', value);
        });
    });

    // Button Hover Color
    api('nosfir_wc_button_hover_color', function(setting) {
        setting.bind(function(value) {
            updateCSSProperty('--nosfir-wc-button-hover-bg', value);
        });
    });

    // ========================================
    // Single Product
    // ========================================

    // Gallery Style
    api('nosfir_wc_gallery_style', function(setting) {
        setting.bind(function(value) {
            $('.single-product .woocommerce-product-gallery')
                .removeClass('gallery-default gallery-vertical gallery-horizontal')
                .addClass('gallery-' + value);
        });
    });

    // Show/Hide SKU
    api('nosfir_wc_show_sku', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.product_meta .sku_wrapper').show();
            } else {
                $('.product_meta .sku_wrapper').hide();
            }
        });
    });

    // Show/Hide Categories
    api('nosfir_wc_show_categories', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.product_meta .posted_in').show();
            } else {
                $('.product_meta .posted_in').hide();
            }
        });
    });

    // Show/Hide Tags
    api('nosfir_wc_show_tags', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.product_meta .tagged_as').show();
            } else {
                $('.product_meta .tagged_as').hide();
            }
        });
    });

    // ========================================
    // Cart
    // ========================================

    // Cart Style
    api('nosfir_wc_cart_style', function(setting) {
        setting.bind(function(value) {
            $('.woocommerce-cart .woocommerce')
                .removeClass('cart-default cart-modern cart-minimal')
                .addClass('cart-' + value);
        });
    });

    // ========================================
    // Checkout
    // ========================================

    // Checkout Layout
    api('nosfir_wc_checkout_layout', function(setting) {
        setting.bind(function(value) {
            $('.woocommerce-checkout')
                .removeClass('checkout-default checkout-two-column checkout-accordion')
                .addClass('checkout-' + value);
        });
    });

    // ========================================
    // Mini Cart
    // ========================================

    // Mini Cart Style
    api('nosfir_wc_mini_cart_style', function(setting) {
        setting.bind(function(value) {
            $('.mini-cart-container')
                .removeClass('mini-cart-dropdown mini-cart-sidebar mini-cart-modal')
                .addClass('mini-cart-' + value);
        });
    });

    // ========================================
    // Shop Page Title/Description
    // ========================================

    // Shop Title
    api('nosfir_wc_shop_title', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.woocommerce-products-header__title').text(value);
            }
        });
    });

    // Shop Description
    api('nosfir_wc_shop_description', function(setting) {
        setting.bind(function(value) {
            if (value) {
                $('.woocommerce-products-header .page-description').html(value);
            }
        });
    });

    // ========================================
    // Selective Refresh Triggers
    // ========================================

    // Settings that require page refresh
    const refreshSettings = [
        'nosfir_wc_sidebar_position',
        'nosfir_wc_related_products_count',
        'nosfir_wc_cross_sells_count'
    ];

    refreshSettings.forEach(function(settingId) {
        api(settingId, function(setting) {
            setting.bind(function() {
                api.selectiveRefresh.requestFullRefresh();
            });
        });
    });

    /**
     * Initialize when Customizer is ready
     */
    api.bind('ready', function() {
        // Add WooCommerce body class for customizer preview
        if ($('body').hasClass('woocommerce') || $('body').hasClass('woocommerce-page')) {
            $('body').addClass('nosfir-wc-customizer-active');
        }
    });

})(jQuery, wp);