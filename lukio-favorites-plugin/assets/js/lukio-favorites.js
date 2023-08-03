jQuery(document).ready(function ($) {
    /**
     * handling the front of lukio favorites plugin
     */
    class lukio_favorites_plugin_class {
        constructor() {
            this.favorites_working = false;
            this.fragment_indicator = lukio_favorites_data.fragment_indicator
            this.body = $('body');
            this.storage_key = 'lukio_favorites_fragments';
            this.supports_html5_storage = true;
            this.test_html5_storage();
        }

        /**
         * check if html5 storage is available. set the class indicator to false if not.
         * 
         * @author Itai Dotan
         */
        test_html5_storage() {
            try {
                this.supports_html5_storage = ('sessionStorage' in window && window.sessionStorage !== null);
                window.sessionStorage.setItem('lukio_favorites_test', 'test');
                window.sessionStorage.removeItem('lukio_favorites_test');
                window.localStorage.setItem('lukio_favorites_test', 'test');
                window.localStorage.removeItem('lukio_favorites_test');
            } catch (err) {
                this.supports_html5_storage = false;
            }
        }

        /**
         * Handle the plugin sending and result for the button click event
         * 
         * @param {jQuery} clicked $(this) object form the event
         * 
         * @author Itai Dotan
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
        * 
        * @param {jQuery} btns post favorites buttons
        * @param {Number | String} post_id the post id to trigger the plugin on
        * @param {String} post_type the post type the button is for
        * @param {string} nonce the button nonce
        * 
        * @author Itai Dotan
        */
        send_ajax(btns, post_id, post_type, nonce) {
            // store the class this to use it in the ajax
            let class_object = this,
                result = null;
            $.ajax({
                method: "POST",
                url: lukio_favorites_data.ajax_url,
                data: { action: 'lukio_favorites_button_click', post_id, post_type, nonce },
                success: function (response) {
                    if (response) {
                        result = JSON.parse(response);
                        btns.attr('data-lukio-fav', result.favorite).attr('title', result.title);
                        class_object.update_menu_button(result.empty);
                        class_object.update_display(result.fragments, result.favorite, result.empty, btns);
                        class_object.update_storage(result.fragments, result.posts, result.empty);
                        class_object.update_cookie(result.cookie_data);
                    }
                },
                complete: function () {
                    btns.removeClass('working');
                    class_object.favorites_working = false;
                    // trigger event indicating the ajax is done and add the buttons and result
                    class_object.body.trigger('lukio_favorites_plugin_refresh', [btns, result]);
                }
            })
        }

        /**
         * update the menu button empty status
         * 
         * @param {Boolean} empty true when the favorites is empty
         * 
         * @author Itai Dotan
         */
        update_menu_button(empty) {
            $('.lukio_favorites_menu_button')[empty ? 'addClass' : 'removeClass']('empty');
        }

        /**
         * update favorites fragments
         * 
         * @param {Object} fragments fragments to update, fragment content indexed by its selector
         * 
         * @author Itai Dotan
         */
        update_fragments(fragments) {
            $.each(fragments, function (selector, content) {
                $(selector + ':not(.skip_update)').replaceWith(content);
            });
        }

        /**
         * update favorites display 
         * 
         * @param {Object} fragments fragments to update, fragment content indexed by its selector
         * @param {Bool} favorite_action true when added to favorites
         * @param {Bool} empty true when the favorites is empty
         * @param {jQuery} btns post favorites buttons
         * 
         * @author Itai Dotan
         */
        update_display(fragments, favorite_action, empty, btns) {
            // when a post was added to favorites or the favorites been emptied, reload the full fragment
            if (favorite_action || empty) {
                this.update_fragments(fragments);
            } else {
                // when post was removed but the favorites isn't empty remove the post
                btns.closest('.lukio_favorites_post').remove();
            }
        }

        /**
         * update storage with the new data after a button was clicked
         * 
         * @param {Object} fragments fragments to update, fragment content indexed by its selector
         * @param {Array} posts post ids of posts in favorites
         * @param {Bool} empty true when the favorites is empty
         * 
         * @author Itai Dotan 
         */
        update_storage(fragments, posts, empty) {
            if (!this.supports_html5_storage) {
                return;
            }

            let timestamp = Date.now(),
                json_value = JSON.stringify({ timestamp, fragments, empty, posts });

            sessionStorage.setItem(this.storage_key, json_value);
            localStorage.setItem(this.storage_key, json_value);
        }

        /**
         * update favorites fragments and buttons when the storage was updated
         * 
         * @param {Event} e triggerd storage event
         * 
         * @author Itai Dotan
         */
        storage_updated(e) {
            if (e.originalEvent.key !== this.storage_key) {
                return;
            }

            let session = JSON.parse(sessionStorage.getItem(this.storage_key)),
                local = JSON.parse(localStorage.getItem(this.storage_key));

            if (session && session.timestamp === local.timestamp) {
                return;
            }

            this.update_fragments(local.fragments);
            this.update_menu_button(local.empty);

            // update the buttons which are not updated from the fragment
            $(`.lukio_favorites_button:not(.${this.fragment_indicator})`).each(function () {
                let btn = $(this);
                btn.attr('data-lukio-fav', local.posts.indexOf(btn.data('post-id')) != -1 ? 1 : 0);
            });
        }

        /**
         * update the cookie with the new data
         * 
         * @param {string} cookie_data json string to save to the cookie
         * 
         * @author Itai Dotan
         */
        update_cookie(cookie_data) {
            if (cookie_data == false) {
                return;
            }
            document.cookie = `${lukio_favorites_data.favorites_coockie}=${cookie_data};path=/;`;
        }
    };

    const lukio_favorites_plugin = new lukio_favorites_plugin_class();

    // add or remove post to users favorites
    $(document).on('click', '.lukio_favorites_button', function (e) {
        e.stopPropagation();
        e.preventDefault();
        lukio_favorites_plugin.clicked_favorite_button($(this));
    });

    $(window).on('storage onstorage', function (e) {
        lukio_favorites_plugin.storage_updated(e);
    });
});
