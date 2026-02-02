/**
 * Customizer Controls JavaScript
 * 
 * Handles custom controls in the WordPress Customizer panel
 * 
 * @package Nosfir
 * @since 1.0.0
 */

(function($, wp) {
    'use strict';

    // Ensure wp.customize exists
    if (typeof wp === 'undefined' || typeof wp.customize === 'undefined') {
        console.warn('Nosfir: wp.customize not available');
        return;
    }

    const api = wp.customize;

    /**
     * Nosfir Customizer Controls
     */
    const NosfirCustomizerControls = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initConditionalControls();
            this.initColorControls();
            this.initRangeSliders();
            this.initSortableControls();
        },

        /**
         * Bind general events
         */
        bindEvents: function() {
            // Panel/Section expand tracking
            api.panel.each(function(panel) {
                panel.expanded.bind(function(isExpanded) {
                    if (isExpanded) {
                        $(document).trigger('nosfir_panel_expanded', [panel.id]);
                    }
                });
            });

            api.section.each(function(section) {
                section.expanded.bind(function(isExpanded) {
                    if (isExpanded) {
                        $(document).trigger('nosfir_section_expanded', [section.id]);
                    }
                });
            });
        },

        /**
         * Initialize conditional controls (show/hide based on other settings)
         */
        initConditionalControls: function() {
            const conditionalSettings = {
                // Header search toggle
                'nosfir_header_search': ['nosfir_header_search_style'],
                
                // Sidebar settings
                'nosfir_sidebar_position': ['nosfir_sidebar_width'],
                
                // Blog layout
                'nosfir_blog_layout': ['nosfir_blog_columns'],
                
                // Footer widgets
                'nosfir_footer_widgets_enable': [
                    'nosfir_footer_widgets_columns',
                    'nosfir_footer_widgets_bg'
                ],
                
                // Hero section
                'nosfir_section_hero_enable': [
                    'nosfir_hero_title',
                    'nosfir_hero_subtitle',
                    'nosfir_hero_bg_image',
                    'nosfir_hero_btn_text',
                    'nosfir_hero_btn_url'
                ],
            };

            $.each(conditionalSettings, function(parentSetting, childSettings) {
                api(parentSetting, function(setting) {
                    // Initial state
                    NosfirCustomizerControls.toggleControls(setting.get(), childSettings);
                    
                    // On change
                    setting.bind(function(value) {
                        NosfirCustomizerControls.toggleControls(value, childSettings);
                    });
                });
            });
        },

        /**
         * Toggle controls visibility
         */
        toggleControls: function(value, controls) {
            $.each(controls, function(index, controlId) {
                api.control(controlId, function(control) {
                    if (value && value !== 'none' && value !== '0' && value !== false) {
                        control.container.slideDown(200);
                    } else {
                        control.container.slideUp(200);
                    }
                });
            });
        },

        /**
         * Initialize color controls with alpha support
         */
        initColorControls: function() {
            // Add reset button to color controls
            api.control.each(function(control) {
                if (control.params.type === 'color') {
                    const $container = control.container;
                    const defaultValue = control.params.default || '';
                    
                    if (defaultValue && !$container.find('.color-reset').length) {
                        const $resetBtn = $('<button type="button" class="button color-reset">Reset</button>');
                        $resetBtn.on('click', function(e) {
                            e.preventDefault();
                            control.setting.set(defaultValue);
                        });
                        $container.find('.customize-control-content').append($resetBtn);
                    }
                }
            });
        },

        /**
         * Initialize range slider controls
         */
        initRangeSliders: function() {
            $('.customize-control-range').each(function() {
                const $control = $(this);
                const $slider = $control.find('input[type="range"]');
                const $value = $control.find('.range-value');
                
                if ($slider.length && $value.length) {
                    $slider.on('input', function() {
                        $value.text(this.value);
                    });
                }
            });
        },

        /**
         * Initialize sortable controls (for section ordering)
         */
        initSortableControls: function() {
            $('.customize-control-sortable .sortable-list').sortable({
                handle: '.sortable-handle',
                placeholder: 'sortable-placeholder',
                update: function(event, ui) {
                    const $list = $(this);
                    const $control = $list.closest('.customize-control');
                    const controlId = $control.attr('id').replace('customize-control-', '');
                    
                    const order = [];
                    $list.find('.sortable-item').each(function() {
                        order.push($(this).data('value'));
                    });
                    
                    api(controlId).set(order.join(','));
                }
            });
        }
    };

    /**
     * Document Ready
     */
    $(document).ready(function() {
        // Wait for Customizer to be ready
        api.bind('ready', function() {
            NosfirCustomizerControls.init();
        });
    });

    // Expose for external use
    window.NosfirCustomizerControls = NosfirCustomizerControls;

})(jQuery, wp);