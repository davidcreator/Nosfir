(function($) {
    'use strict';

    var NosfirWishlist = {
        init: function() {
            $(document).on('click', '.nosfir-wishlist-button', this.toggle);
        },

        toggle: function(e) {
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
                    action: 'nosfir_add_to_wishlist',
                    product_id: product_id,
                    nonce: nosfir_wc_params.nonce
                },
                type: 'POST',
                success: function(response) {
                    $button.removeClass('loading');
                    
                    if (response.success) {
                        if (response.data.action === 'added') {
                            $button.addClass('active');
                            $button.attr('title', response.data.message);
                        } else {
                            $button.removeClass('active');
                            $button.attr('title', response.data.message);
                        }
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                }
            });
        }
    };

    $(document).ready(function() {
        NosfirWishlist.init();
    });

})(jQuery);
