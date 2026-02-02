/**
 * Nosfir Radio Image Control
 *
 * @package Nosfir
 * @since 1.0.0
 */

(function($, api) {
    'use strict';

    if (typeof api === 'undefined') {
        return;
    }

    $(document).on('click', '.radio-image-option label', function(e) {
        var $label = $(this);
        var $input = $label.prev('input[type="radio"]');
        var $control = $label.closest('.customize-control-radio-image');
        
        $control.find('label').removeClass('selected');
        $label.addClass('selected');
        
        $input.prop('checked', true).trigger('change');
    });

    $(document).on('change', '.radio-image-option input[type="radio"]', function() {
        var $input = $(this);
        var $control = $input.closest('.customize-control-radio-image');
        var settingId = $control.attr('id').replace('customize-control-', '');
        
        if (api(settingId)) {
            api(settingId).set($input.val());
        }
    });

})(jQuery, wp.customize);