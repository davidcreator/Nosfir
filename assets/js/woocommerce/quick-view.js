(function($) {
    'use strict';

    var NosfirQuickView = {
        init: function() {
            $(document).on('click', '.nosfir-quick-view-button', this.open);
            $(document).on('click', '.nosfir-quick-view-close, .nosfir-quick-view-overlay', this.close);
            $(document).keyup(function(e) {
                if (e.keyCode === 27) { // Escape key
                    NosfirQuickView.close();
                }
            });
        },

        open: function(e) {
            e.preventDefault();

            var $button = $(this),
                product_id = $button.data('product_id');

            if (!product_id) {
                return;
            }

            $button.addClass('loading');

            $.ajax({
                url: nosfir_wc_params.ajax_url,
                data: {
                    action: 'nosfir_quick_view',
                    product_id: product_id,
                    nonce: nosfir_wc_params.nonce
                },
                type: 'POST',
                success: function(response) {
                    $button.removeClass('loading');
                    NosfirQuickView.render(response);
                },
                error: function() {
                    $button.removeClass('loading');
                }
            });
        },

        render: function(content) {
            var html = '<div class="nosfir-quick-view-overlay"></div>' +
                       '<div class="nosfir-quick-view-modal">' +
                           '<button class="nosfir-quick-view-close">&times;</button>' +
                           '<div class="nosfir-quick-view-content">' + content + '</div>' +
                       '</div>';

            $('body').append(html);
            $('body').addClass('nosfir-quick-view-open');

            // Trigger WC events
            if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
                $( '.variations_form' ).each( function() {
                    $( this ).wc_variation_form();
                });
            }
        },

        close: function(e) {
            if (e) e.preventDefault();
            $('.nosfir-quick-view-overlay, .nosfir-quick-view-modal').remove();
            $('body').removeClass('nosfir-quick-view-open');
        }
    };

    $(document).ready(function() {
        NosfirQuickView.init();
    });

})(jQuery);
