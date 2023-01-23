jQuery(document).ready(function ($) {
    ////// Favorites //////
    class lukio_favorites_plugin_class {
        constructor() {
            this.favorites_working = false;
        }
        /**
         * Handle the plugin sending and result for the button click event
         * @param {jQuery} clicked $(this) object form the event
         */
        clicked_favorite_button(clicked) {
            // check if the plugin is mid work
            if (this.favorites_working) { return; };

            this.favorites_working = true;

            // get all the buttons of the same product
            let fav_btns = $(`.lukio_favorites_button[data-post-id="${clicked.data('post-id')}"]`);
            fav_btns.addClass('working');

            this.send_ajax(fav_btns, clicked.data('post-id'), clicked.data('post-type'), clicked.data('nonce'));
        }

        /**
        * send the ajax and apply the result
        * @param {jQuery} btns post favorites buttons
        * @param {Number | String} post_id the post id to trigger the plugin on
        * @param {String} post_type the post type the button is for
        * @param {string} nonce the button nonce
        */
        send_ajax(btns, post_id, post_type, nonce) {
            let class_object = this;
            $.ajax({
                method: "POST",
                url: lukio_favorites_ajax.ajax_url,
                data: { action: 'lukio_favorites_button_click', post_id, post_type, nonce },
                success: function (result) {
                    if (result) {
                        result = JSON.parse(result);
                        btns.attr('data-lukio-fav', result.favorite).attr('aria-label', result.aria_label);
                    }
                },
                complete: function () {
                    btns.removeClass('working');
                    class_object.favorites_working = false;
                    // trigger event indicating the ajax is done and add the buttons affected as a parameter
                    $('body').trigger('lukio_favorites_plugin_refresh', [btns]);
                }
            })
        }
    };

    const lukio_favorites_plugin = new lukio_favorites_plugin_class();

    // add or remove recipe to the users favorites
    $(document).on('click', '.lukio_favorites_button', function (e) {
        e.stopPropagation();
        e.preventDefault();
        lukio_favorites_plugin.clicked_favorite_button($(this));
    });
});
