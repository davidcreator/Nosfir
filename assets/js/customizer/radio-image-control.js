/**
 * Radio Image Control JavaScript
 * 
 * Handles the radio image custom control functionality
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
     * Radio Image Control Handler
     */
    const RadioImageControl = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initKeyboardNavigation();
        },

        /**
         * Bind click events
         */
        bindEvents: function() {
            // Handle radio image selection
            $(document).on('click', '.radio-image-option label', function(e) {
                const $label = $(this);
                const $input = $label.prev('input[type="radio"]');
                const $control = $label.closest('.customize-control-radio-image');
                
                // Update visual state
                $control.find('.radio-image-option label').removeClass('selected');
                $label.addClass('selected');
                
                // Trigger the input change
                $input.prop('checked', true).trigger('change');
            });

            // Handle input change (for programmatic changes)
            $(document).on('change', '.radio-image-option input[type="radio"]', function() {
                const $input = $(this);
                const $control = $input.closest('.customize-control-radio-image');
                const settingId = $control.attr('id').replace('customize-control-', '');
                const value = $input.val();
                
                // Update the customizer setting
                if (api(settingId)) {
                    api(settingId).set(value);
                }
                
                // Update visual state
                $control.find('.radio-image-option label').removeClass('selected');
                $input.next('label').addClass('selected');
            });
        },

        /**
         * Initialize keyboard navigation for accessibility
         */
        initKeyboardNavigation: function() {
            $(document).on('keydown', '.radio-image-option input[type="radio"]', function(e) {
                const $input = $(this);
                const $options = $input.closest('.radio-image-options');
                const $allInputs = $options.find('input[type="radio"]');
                const currentIndex = $allInputs.index($input);
                let newIndex;

                switch (e.keyCode) {
                    case 37: // Left arrow
                    case 38: // Up arrow
                        e.preventDefault();
                        newIndex = currentIndex > 0 ? currentIndex - 1 : $allInputs.length - 1;
                        break;
                    case 39: // Right arrow
                    case 40: // Down arrow
                        e.preventDefault();
                        newIndex = currentIndex < $allInputs.length - 1 ? currentIndex + 1 : 0;
                        break;
                    case 13: // Enter
                    case 32: // Space
                        e.preventDefault();
                        $input.prop('checked', true).trigger('change');
                        return;
                    default:
                        return;
                }

                const $newInput = $allInputs.eq(newIndex);
                $newInput.focus().prop('checked', true).trigger('change');
            });
        },

        /**
         * Refresh control (for dynamic loading)
         */
        refresh: function(controlId) {
            const $control = $('#customize-control-' + controlId);
            if ($control.length) {
                const $checkedInput = $control.find('input[type="radio"]:checked');
                if ($checkedInput.length) {
                    $control.find('.radio-image-option label').removeClass('selected');
                    $checkedInput.next('label').addClass('selected');
                }
            }
        }
    };

    /**
     * Extend wp.customize.Control for Radio Image
     */
    api.controlConstructor['radio-image'] = api.Control.extend({
        ready: function() {
            const control = this;
            
            // Set initial selected state
            control.container.find('input[type="radio"]:checked').next('label').addClass('selected');
            
            // Handle setting changes from other sources
            control.setting.bind(function(value) {
                control.container.find('input[type="radio"]').each(function() {
                    const $input = $(this);
                    const isChecked = $input.val() === value;
                    $input.prop('checked', isChecked);
                    $input.next('label').toggleClass('selected', isChecked);
                });
            });
        }
    });

    /**
     * Document Ready
     */
    $(document).ready(function() {
        RadioImageControl.init();
    });

    // Expose for external use
    window.NosfirRadioImageControl = RadioImageControl;

})(jQuery, wp);