/**
 * Nosfir Customizer Controls
 *
 * @package Nosfir
 * @since 1.0.0
 */

(function($, api) {
    'use strict';

    if (typeof api === 'undefined') {
        console.log('Nosfir: Customizer API not available');
        return;
    }

    api.bind('ready', function() {
        console.log('Nosfir: Customizer Controls Ready');
        initConditionalControls();
    });

    function initConditionalControls() {
        // Sidebar visibility
        api('nosfir_sidebar_position', function(setting) {
            api.control('nosfir_sidebar_width', function(control) {
                if (!control) return;
                
                function toggle(value) {
                    control.container.toggle(value !== 'none');
                }
                
                toggle(setting.get());
                setting.bind(toggle);
            });
        });
    }

})(jQuery, wp.customize);