(function ($) {
    $(document).ready(function () {
        ////// Favorites //////
        class lukio_favorites_plugin_class {
            constructor() {
                this.favorites_working = { status: false };
            }
            /**
             * Handle the plugin sending and result for the button click event
             * @param {jQuery} clicked $(this) object form the event
             */
            clicked_favorite_button(clicked) {
                /**
                 * send the ajax and apply the result
                 * @param {jQuery} btn the clicked plugin button
                 * @param {Number | String} post_id the post to trigger the plugin on
                 * @param {Object} working_flag the class status object
                 */
                function send_ajax(btn, post_id, working_flag) {
                    $.ajax({
                        method: "POST",
                        url: lukio_favorites_ajax.ajax_url,
                        data: { action: 'lukio_favorites_button_click', post_id: post_id },
                        success: function (result) {
                            result = JSON.parse(result);
                            btn.removeClass('working').attr('data-lukio-fav', result.favorite);
                        },
                        complete: function () {
                            working_flag.status = false;
                            // trigger event indicating the ajax is done and add the buttons affected as a parameter
                            $('body').trigger('lukio_favorites_plugin_refresh', [btn]);
                        }
                    })
                }

                // check if the plugin is mid work
                if (this.favorites_working.status) { return; };

                this.favorites_working.status = true;

                // get all the buttons of the same product
                let fav_btns = $(`.lukio_favorites_button[data-post-id="${clicked.data('post-id')}"]`);
                fav_btns.addClass('working');

                send_ajax(fav_btns, clicked.data('post-id'), this.favorites_working);
            }
        };

        let lukio_favorites_plugin = new lukio_favorites_plugin_class();

        // add or remove recipe to the users favorites
        $(document).on('click', '.lukio_favorites_button', function (e) {
            e.stopPropagation();
            e.preventDefault();
            lukio_favorites_plugin.clicked_favorite_button($(this));
        })
    })
})(jQuery)